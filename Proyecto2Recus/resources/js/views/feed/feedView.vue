
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { authStore } from '../../store/auth';
import FeedCard from './feedCard.vue';

const auth = authStore();
const loading = ref(false);
const markers = ref([]);
const friendnumber = ref(0);
const user_id = ref(auth.user?.id);

onMounted(async () => {
    await getFeed();
});

async function getFeed() {
    loading.value = true;

    try {
        const response = await axios.post('http://127.0.0.1:8000/api/markers/getLastMarkerFromFriends', {
            user_id: user_id,
        });

        markers.value = response.data.markers;
        loading.value = false;

    } catch (error) {
        console.error("[ProfileView.vue] Error:", error);
        loading.value = false;
    }
}
</script>

<template>
    <div v-if="loading">
        <p>Loading...</p>
    </div>

    <div v-else class="d-flex flex-wrap justify-content-between align-items-center m-auto gap-5" style="width: 90%;">

        <FeedCard
            v-for="(marker, index) in markers"
            :key="index"
            :pfp="marker.profile_picture_url"
            :title="marker.name"
            :description="marker.description"
        />
    </div>
</template>