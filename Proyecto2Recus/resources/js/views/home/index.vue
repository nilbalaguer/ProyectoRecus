<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

// MaboxGL Compostable
import { emitter } from '@/composables/MapUtils';
import { InitializeMap, SetFriends, ReloadMapMarkers, AddMarkerToMap, SetMarkers, HideCenterMarker, OnMapDblClick, ShowMarkerOnMapCenter } from "@/composables/MapUtils.js";
import PopupCreateMarker from '../../components/PopupCreateMarker.vue';
import PopupShowMarker from '../../components/PopupShowMarker.vue';
import { showMarkerById, createNewMarker } from '../../composables/useMarkers.js';
import { create } from 'lodash';

const createMarkerPopupVisible = ref(false);
const showMarkerDataPopupVisible = ref(true);

const selectedMarkerData = ref(null);

async function handleMarkerClick(id) {
    const data = await showMarkerById(id).then()
    {
        selectedMarkerData.value = data;
        showMarkerDataPopupVisible.value = true;
    }

}

onMounted(async () => {
    // Event, on marked clicked get id.
    emitter.on('marker-clicked', handleMarkerClick);

    const friendsConnected = await loadUsers();
    const allMarkers = await loadMarkers();

    if (friendsConnected && Array.isArray(friendsConnected)) {
        SetFriends(friendsConnected);
        SetMarkers(allMarkers);
    }
    else {
        console.error("Error: La respuesta no es un array válido.");
    }

    // Map
    const map = InitializeMap();
    map.on('load', () => {
        OnMapDblClick((e) => {
            ShowMarkerOnMapCenter();
            createMarkerPopupVisible.value = true;
        });

        ReloadMapMarkers(map);

        map.on('move', () => {
            HandleCenterMarker();
        });
        map.on('zoom', () => {
            HandleCenterMarker();
        });
    });

    function HandleCenterMarker() {
        if (createMarkerPopupVisible.value == true)
            ShowMarkerOnMapCenter();
        else
            HideCenterMarker();
    }
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
        <PopupShowMarker v-if="selectedMarkerData != null" v-model:visible="showMarkerDataPopupVisible"
            :marker=selectedMarkerData />

            <button class="button-primary d-block d-sm-none" @click="ToggleCreateMarker" style="position: fixed; bottom: 64px; right: 16px; width: 32px; height: 32px; z-index: 999; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border-radius: 50%; border: 0;">+</button>

        <div id="map"></div>
    </div>
</template>
