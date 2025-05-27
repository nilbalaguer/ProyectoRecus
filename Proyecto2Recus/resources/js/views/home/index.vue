<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

import { emitter } from '@/composables/MapUtils';
import {
    InitializeMap,
    SetFriends,
    ReloadMapMarkers,
    SetMarkers,
    HideCenterMarker,
    OnMapDblClick,
    ShowMarkerOnMapCenter
} from "@/composables/MapUtils.js";

import PopupCreateMarker from '../../components/PopupCreateMarker.vue';
import PopupShowMarker from '../../components/PopupShowMarker.vue';
import { showMarkerById } from '../../composables/useMarkers.js';

const createMarkerPopupVisible = ref(false);
const showMarkerDataPopupVisible = ref(true);
const selectedMarkerData = ref(null);
const historialUbicaciones = ref([]);

let map;

function porId(id) {
    return item => item.id === id;
}

async function handleMarkerClick(id) {
    const data = await showMarkerById(id);

    if (data) {
        selectedMarkerData.value = data;
        showMarkerDataPopupVisible.value = true;
        guardarUbicacionEnHistorial(data);
    }
}

function irAUbicacion(ubicacion) {
    if (!map || !ubicacion.lat || !ubicacion.lng) return;

    map.flyTo({
        center: [ubicacion.lng, ubicacion.lat],
        zoom: 15,
        essential: true
    });

    selectedMarkerData.value = ubicacion;
    showMarkerDataPopupVisible.value = true;
}

function guardarUbicacionEnHistorial(marker) {
    const key = 'historialUbicaciones';
    let historial = JSON.parse(sessionStorage.getItem(key)) || [];

    if (!historial.find(porId(marker.id))) { //JsAvanzado
        historial.unshift(marker);
    }

    historial = historial.slice(0, 5);
    sessionStorage.setItem(key, JSON.stringify(historial));
    historialUbicaciones.value = historial;
}

function cargarHistorialDesdeSession() {
    const historial = JSON.parse(sessionStorage.getItem('historialUbicaciones')) || [];
    historialUbicaciones.value = historial;
}

function HandleCenterMarker() {
    if (createMarkerPopupVisible.value)
        ShowMarkerOnMapCenter();
    else
        HideCenterMarker();
}

onMounted(async () => {
    emitter.on('marker-clicked', handleMarkerClick);

    const friendsConnected = await loadUsers();
    const allMarkers = await loadMarkers();

    if (friendsConnected && Array.isArray(friendsConnected)) {
        SetFriends(friendsConnected);
        SetMarkers(allMarkers);
    } else {
        console.error("Error: La respuesta no es un array válido.");
    }

    map = InitializeMap();

    map.on('load', () => {
        OnMapDblClick(() => {
            ShowMarkerOnMapCenter();
            createMarkerPopupVisible.value = true;
        });

        ReloadMapMarkers(map);

        map.on('move', HandleCenterMarker);
        map.on('zoom', HandleCenterMarker);
    });

    cargarHistorialDesdeSession();
});

async function loadUsers() {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/friends/showFriends');
        return response.data;
    } catch (error) {
        console.error("[SearchView.vue] Error al cargar amigos:", error);
        return [];
    }
}

async function loadMarkers() {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/markers/');
        return response.data;
    } catch (error) {
        console.error("[SearchView.vue] Error al cargar marcadores:", error);
        return [];
    }
}

function ToggleCreateMarker() {
    createMarkerPopupVisible.value = !createMarkerPopupVisible.value;
}
</script>

<template>
    <div>
        <PopupCreateMarker v-model:visible="createMarkerPopupVisible" />
        <PopupShowMarker
            v-if="selectedMarkerData != null"
            v-model:visible="showMarkerDataPopupVisible"
            :marker="selectedMarkerData"
        />

        <button
            class="button-primary d-block d-sm-none"
            @click="ToggleCreateMarker"
            style="position: fixed; bottom: 64px; right: 16px; width: 32px; height: 32px; z-index: 999; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border-radius: 50%; border: 0;"
        >
            +
        </button>

        <div class="historialUbicacionesMenu">
            <h3>Historial De Ubicaciones</h3>
            <ul>
                <li
                    v-for="ubicacion in historialUbicaciones"
                    :key="ubicacion.id"
                    @click="irAUbicacion(ubicacion)"
                    style="cursor: pointer;"
                >
                    <p>{{ ubicacion.name || `Ubicación #${ubicacion.id}` }}</p> 
                    <button><img src="images/icon_eye.svg"></button>
                </li>
            </ul>
        </div>

        <div id="map"></div>
    </div>
</template>
