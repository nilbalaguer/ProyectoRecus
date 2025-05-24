<script setup>
import { computed, ref, watch, onMounted } from "vue";
import { getEmojiById, getMarkerListById } from '@/composables/useMarkerList';
import { flyMapPositionAndRotation } from "@/composables/MapUtils.js";
import { GetAvgStarsByMarkerId, SetReviewToMarker, GetMyReviewByMarkerId } from "@/composables/useMarkerReviews";

const props = defineProps({
  visible: Boolean,
  marker: Object
});
const emit = defineEmits(['update:visible']);
const visible = computed({
  get: () => props.visible,
  set: val => emit('update:visible', val)
});

let currentMarkerId = ref(null);
const listData = ref('');
const loading = ref(false);
const rating_avg = ref({ average_stars: 0 });   // Es el valor promedio que tiene el marcador
const rating_client_value = ref();              // Es el valor que le da el usuario al marcador

// Ejecutar la carga de datos cuando el componente se monta
onMounted(async () => {
  if (props.visible) {
    await loadMarkerData(); // Cargar los datos si el componente ya está visible al montarse
  }
});

// Observar cambios en el marcador y en la visibilidad
watch([() => props.marker, () => props.visible], async ([newMarker, isVisible]) => {
  if (isVisible) {
    await loadMarkerData(newMarker);
  }
});

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Función que carga los datos
const loadMarkerData = async (marker = props.marker) => {
  try {
    loading.value = true;

    if (currentMarkerId.value !== marker.id) {
      rating_avg.value = { average_stars: 0 };
      currentMarkerId.value = marker.id;
    }

    const markerList = await getMarkerListById(marker.marker_list_id);
    const emoji = await getEmojiById(markerList.emoji_identifier);

    if(markerList.value)
      listData.value = `${emoji} ${markerList.name}`;
    else
    listData.value = `${emoji} All Markers`;

    // Estrellas promedias
    rating_avg.value = await GetAvgStarsByMarkerId(props.marker.id) || { average_stars: 0 };

    // Mis estrellas
    const myReviewResponse = await GetMyReviewByMarkerId(marker.id);
    if (myReviewResponse && myReviewResponse.review) {
      rating_client_value.value = myReviewResponse.review.review_stars;
    } else {
      rating_client_value.value = null;
    }

    flyMapPositionAndRotation([marker.lng, marker.lat], marker.zoom, marker.pitch, marker.bearing);

  } catch (error) {
    console.error('Error al cargar los datos:', error);
    listData.value = 'Error al cargar los datos';
  } finally {
    loading.value = false;
  }
};

async function RateMarker() {

  await SetReviewToMarker(props.marker.id, rating_client_value.value);
  await sleep(100);
  rating_avg.value = await GetAvgStarsByMarkerId(props.marker.id) || { average_stars: 0 };
}

</script>

<template>
  <Dialog position="bottom" v-model:visible="visible" class="popup bottom-popup">
    <div class="w-100 pt-5 text-center popup-header">
      <h2 style="font-weight: 800;">{{ marker.name }}</h2>
    </div>

    <div class="w-100 d-flex flex-grow-1 justify-content-center align-items-center m-0 p-0">
      <Rating v-model="rating_avg.average_stars" :stars=10 readonly>
        <template #onicon>

          <img v-if="rating_avg.average_stars >= 9" src="/images/MarkerReviews/Fire.webp" width="20" />
          <img v-else-if="rating_avg.average_stars >= 5" src="/images/MarkerReviews/Hearth.webp" width="20" />
          <img v-else src="/images/MarkerReviews/HappyFace.webp" width="20" />

        </template>
        <template #officon>
          <img src="/images/MarkerReviews/CryFace.webp" width="20" />
        </template>
      </Rating>
      <p style="font-weight: normal;">({{ rating_avg.count }})</p>

    </div>

    <div class="w-100 d-flex flex-column flex-grow-1 p-3">
      <h3 class="m-1" style="font-style: italic;">{{ loading ? 'Cargando...' : listData }}</h3>

      <p
        style="margin-left: 16px !important; height: auto; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; font-size: medium;">
        {{ marker.description }}
      </p>

      <hr>
      <div class="w-100 m-auto d-flex align-items-center">
        <Rating @click="RateMarker" v-model="rating_client_value" :stars="10">
          <template #onicon>
            <img v-if="rating_client_value >= 9" src="/images/MarkerReviews/Fire.webp" width="20" />
            <img v-else-if="rating_client_value >= 5" src="/images/MarkerReviews/Hearth.webp" width="20" />
            <img v-else src="/images/MarkerReviews/HappyFace.webp" width="20" />
          </template>
          <template #officon>
            <img src="/images/MarkerReviews/CryFace.webp" width="20" />
          </template>
        </Rating>
      </div>

    </div>
  </Dialog>
</template>
