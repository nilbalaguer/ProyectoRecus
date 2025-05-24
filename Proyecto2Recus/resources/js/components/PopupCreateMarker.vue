<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from 'primevue/usetoast';
import { createMarkerList, getMarkerLists, getEmojiById, generateRandomEmoji, getIdByEmoji } from '../composables/useMarkerList';
import { createNewMarker } from '../composables/useMarkers';

const toast = useToast();
const MAX_POPUP_INDEX = 3;
const currentPopupIndex = ref(0);
const markerData = ref({ name: "", description: "", marker_list_id: undefined, lng: 0, lat: 0, zoom: 0, pitch: 0, bearing: 0 });
const markerList_array = ref([]);
const markerList_itemSelected = ref(null);
const createMarkerList_emoji = ref();
const createMarkerList_name = ref('');

const props = defineProps({ visible: Boolean });
const emit = defineEmits(['update:visible']);

const visible = computed({
  get: () => props.visible,
  set: val => emit('update:visible', val)
});

const MARKER_LIST_PLACEHOLDERS = [
  "Ruta gastronómica", "Guía de viaje", "Planes con amigos", "Mapa cultural",
  "Mapa verde", "Lugares fotogénicos", "Bares con billar", "Lugares fav en Roma", "Paises que quiero visitar"
];

onMounted(async () => {
  try {
    const result = await getMarkerLists();
    markerList_array.value = result || [];
  } catch (err) {
    console.error("Error cargando listas:", err);
    markerList_array.value = [];
  }
});

function NextPopupIndex() 
{  
  currentPopupIndex.value = (currentPopupIndex.value + 1) % MAX_POPUP_INDEX;
}

function PreviousPopupIndex() {
  currentPopupIndex.value = currentPopupIndex.value === 100 ? 1 : currentPopupIndex.value - 1;
}

function selectMarkerList(index) {
  markerList_itemSelected.value = markerList_itemSelected.value === index ? null : index;
  markerData.value.marker_list_id = markerList_itemSelected.value !== null ? markerList_array.value[markerList_itemSelected.value].id : null;
}

function showCreateMarkerListPopup() {
  createMarkerList_emoji.value = generateRandomEmoji();
  currentPopupIndex.value = 100;
}

function createMarker() 
{ 
  createNewMarker(markerData.value, () => visible.value = false, error => console.error(error));
  createMarkerList_name.value = null;
}

async function createMarkerListAndReturn() {
  try {
    const newList = await createMarkerList(createMarkerList_name.value, getIdByEmoji(createMarkerList_emoji.value));
    if (!Array.isArray(markerList_array.value)) {
      markerList_array.value = [];
    }
    markerList_array.value.push(newList);
    PreviousPopupIndex();
  } catch (error) {
    console.error("Error creando la lista:", error);
  }
}
</script>

<template>
  <Toast />

  <Dialog position="bottom" v-model:visible="visible" class="popup bottom-popup">
    <div class="w-100 text-center popup-header">
      <h2 style="font-weight: 800;" v-if="currentPopupIndex != 100">{{ $t("new_marker") }}</h2>
      <h2 style="font-weight: 800;" v-else>{{ $t("create_new_list") }}</h2>
    </div>

    <!-- Post Info (Name, Description) -->
    <div v-if="currentPopupIndex == 0" class="w-100 d-flex flex-column flex-grow-1">
      <label for="marker-name" style="font-weight: 600; font-size: large">{{$t("name")}}</label>
      <input placeholder="Name Here!" class="popup-input" type="text" id="marker-name" v-model="markerData.name" maxlength="24">

      <label for="marker-description" style="font-weight: 600; font-size: large;">{{$t("description")}}</label>
      <textarea maxlength="128" id="marker-description" v-model="markerData.description" class="popup-input"
        placeholder="Description Here!" style="height: 128px; width: 100%; resize: none;"></textarea>
    </div>

    <!-- Select Marker List -->
    <div v-if="currentPopupIndex == 1" class="w-100 p-3 d-flex flex-column flex-grow-1" style="overflow-y: scroll; height: 25vh;">
      <div @click="showCreateMarkerListPopup()" class="popup-list-item w-100 mb-3 d-flex clickable-div">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
        </svg>
        <p class="w-100 m-auto" style="margin-left: 8px !important;">{{ $t("create_new_list") }}</p>
      </div>

      <div v-for="(list, index) in markerList_array" :key="index" @click="selectMarkerList(index)" class="popup-list-item d-flex clickable-div">
        <p class="w-100 m-auto">{{ getEmojiById(list.emoji_identifier) }} {{ list.name }}</p>
        <div v-if="markerList_itemSelected === index" class="popup-list-item-active"></div>
      </div>
    </div>

    <!-- Summary -->
    <div v-if="currentPopupIndex == 2" class="w-100 d-flex flex-column flex-grow-1">
      <h2 class="m-1" v-if="markerList_itemSelected !== null && markerList_array[markerList_itemSelected]">
        {{ getEmojiById(markerList_array[markerList_itemSelected].emoji_identifier) }} {{ markerList_array[markerList_itemSelected].name }}
      </h2>
      <h3 class="m-1">{{ markerData.name }}</h3>
      <p style="margin-left: 16px !important;">{{ markerData.description }}</p>
    </div>

    <!-- Create Marker List -->
    <div v-if="currentPopupIndex == 100" class="w-100 d-flex flex-column flex-grow-1">
      <h3>{{ createMarkerList_emoji }} {{ createMarkerList_name }}</h3>

      <label for="marker-name" style="font-weight: 600; font-size: large">{{ $t("marker_list_name") }}</label>
      <input v-model="createMarkerList_name" maxlength="24"
        :placeholder="MARKER_LIST_PLACEHOLDERS[Math.floor(Math.random() * MARKER_LIST_PLACEHOLDERS.length)]"
        class="popup-input" type="text" id="marker-name">

      <label for="marker-name" style="font-weight: 600; font-size: large">Icon</label>
      <span class="d-flex align-items-center gap-2">
        <input readonly class="popup-input w-25 text-center" type="text" id="marker-name" style="font-size: large;"
          v-model="createMarkerList_emoji">
        <button @click="createMarkerList_emoji = generateRandomEmoji()" class="btn button-secondary fw-semibold fs-2 p-0 bg-transparent border-0">🎲</button>
      </span>
    </div>

    <div class="popup-footer">
      <Button v-if="currentPopupIndex != 0" class="btn secondary-button" @click="PreviousPopupIndex()"
        style="height: 32px !important; width: 32px !important; margin-right: 8px !important;">
        <span class="pi pi-arrow-left"></span>
      </Button>

      <button v-if="currentPopupIndex == 100" class="btn popup-button" @click="createMarkerListAndReturn()"
        :disabled='!createMarkerList_name'>Create</button>
      <button v-else-if="currentPopupIndex != 2" class="btn popup-button" @click="NextPopupIndex()"
        :disabled='!markerData.name || !markerData.description'>{{ $t("next") }}</button>
      <button v-else class="btn popup-button" @click="createMarker()">{{ $t("finish") }}</button>
    </div>
  </Dialog>
</template>

<style>
.clickable-div:hover {
  cursor: pointer;
}

.p-dialog-header {
  padding: 0 !important;
  position: absolute;
  top: 24px;
  left: 24px;
}

.p-button {
  background-color: var(--background2) !important;
  color: white !important;
}

.p-button:hover {
  background-color: white !important;
  color: black !important;
}

.p-dialog-content {
  overflow-x: hidden !important;
  padding-bottom: 0 !important;
  height: 100% !important;
}

.p-toast-message-error {
  background: black !important;
  border: 0 !important;
}

.p-toast-summary {
  font-weight: 800 !important;
}

.p-toast-detail {
  color: white !important;
}
</style>
