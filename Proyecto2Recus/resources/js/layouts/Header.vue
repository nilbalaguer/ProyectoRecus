<template>
    <header>
        <div>
            <router-link to="/friends">
                <img v-if="hasNotification == true" src="/images/icon_notification-bell-marked.svg" alt="Solicitudes de amistad"
                    class="icon icon-shake-hover">
                <img v-else src="/images/icon_notification-bell.svg" class="icon icon-shake-hover">
            </router-link>
        </div>
        <div class=" w-100 text-center">
            <h2 class="brandName">{{ $t('brandName') }}.</h2>
        </div>
        <div>
            <router-link to="/settings">
                <img src="/images/settings.svg" alt="Setings image" class="icon">
            </router-link>
        </div>
    </header>
    <div style="height: 8vh;"></div> <!-- Margen -->
</template>

<script setup>

import LocaleSwitcher from "../components/LocaleSwitcher.vue";
import { onMounted, ref } from 'vue';
import { authStore } from "../store/auth";
import axios from 'axios';

const auth = authStore();
const user_id = ref(auth.user?.id);

const hasNotification = ref(false);
const users = ref([]);

onMounted(() => 
{

    axios.get('http://127.0.0.1:8000/api/friends/myFriends?user=' + user_id.value)
        .then(response => {
            users.value = response.data;
            CheckNotifications();
        })
        .catch(error => {
            console.error("[SearchView.vue] Error:", error);
        });
});

function CheckNotifications() 
    {
        for (let index = 0; index < users.value.length; index++) {
            if (users.value[index].request_status == 0) {
                hasNotification.value = true;
                break;
            }

        }
    }
</script>