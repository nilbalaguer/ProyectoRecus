<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { authStore } from "../../store/auth";
import ConfirmButtonPopup from '@/components/ConfirmButtonPopup.vue';

const usersList = ref([]);
const inputbusqueda = ref("");
const friendsRequestSended = ref([]);

async function cargarUsers() {
    axios.get('http://127.0.0.1:8000/api/friends/showFriends?search=' + inputbusqueda.value)
        .then(response => {
            usersList.value = response.data;

            // Cargar la imagen del usuario
            usersList.value.forEach(user => {
                try {
                    user.image = user.media_url ? user.media_url.split("localhost/")[1] : "";

                } catch (error) {
                    user.image = "";
                }
            });

        })
        .catch(error => {
            console.error("[SearchView.vue] Error:", error);
        });

    axios.get('http://127.0.0.1:8000/api/friends/GetUsersWithFriendRequests')
        .then(response => {
            friendsRequestSended.value = response.data;
        })
        .catch(error => {
            console.log(error);
        })
}

async function sendRequest(id_reciver) {
    await axios.post('http://127.0.0.1:8000/api/friend', {
        "id_sender": authStore().user.id,
        "id_receiver": id_reciver
    }).then(response => {
        friendsRequestSended.value.push({ id: id_reciver });
    }).catch(error => {
        console.error(error);
    });
}

async function deleteRequest(friend_id) {
    axios.get(`http://127.0.0.1:8000/api/friends/destroyRequest?id_sender=${authStore().user.id}&id_receiver=${friend_id}`)
        .then(response => {
            console.log('Friendship deleted:', response.data);
            friendsRequestSended.value = friendsRequestSended.value.filter(friend => friend.id !== friend_id);
        })
        .catch(error => {
            console.error('There was an error deleting the friendship:', error.response?.data || error.message);
        });
}

function manejarInput() {
    clearTimeout();

    if (inputbusqueda.value === '') {
        usersList.value = [];
    }

    setTimeout(cargarUsers, 500);
}

cargarUsers();
</script>

<template>
    <div class="search-background">
        <div>
            <input class="search-field" v-model="inputbusqueda" @input="manejarInput"
                :placeholder="$t('buscadoramigos')">
        </div>

        <div class="search-user-list-container">

            <!-- Fake search results for Loading -->
            <div v-if="usersList.length === 0" v-for="n in 4" :key="n" class="search-user-container">
                <div class="search-user-information-container">
                    <div>
                        <div class="search-fake-user-image"></div>
                    </div>
                    <div>
                        <div class="search-fake-user-username"></div>

                        <div class="d-flex flex-row">
                            <div class="search-fake-user-name"></div>
                            <div class="search-fake-user-description"></div>
                        </div>

                    </div>
                </div>
                <div>
                    <div class="search-fake-button"></div>
                </div>
            </div>

            <!-- Search results -->
            <a v-for="(user, index) in usersList" :key="index" :href="'profile/' + user.username"
                class="search-user-container" style="color: white;">

                <div class="search-user-information-container">
                    <div>
                        <img :src="user.image ? user.image : '/images/default_pf.jpg'" alt="User image"
                            class="search-user-information-image">
                    </div>

                    <div>
                        <b>
                            <p class="search-user-information-name">{{ user.name }}</p>
                        </b>
                        <span class="search-user-information-username">@{{ user.username }}</span>
                    </div>
                </div>

                <div>
                    <button v-if="friendsRequestSended.some(friend => friend.id === user.id)"
                        @click.stop.prevent="deleteRequest(user.id)" class="secondary-button danger-button-hover"
                        style="min-width: 7rem;">
                        {{ $t('cancel') }}
                    </button>

                    <button v-else @click.stop.prevent="sendRequest(user.id)" class="secondary-button button-hover"
                        style="min-width: 6rem;">

                        {{ $t('addFriendText') }}
                    </button>
                </div>

            </a>

            <div v-if="usersList.length === 0 && inputbusqueda !== ''" id="notfoundsearcherror">
                <h2>{{ $t('usernotfound') }}</h2>
            </div>

        </div>

    </div>
</template>

<style>
@media (max-width: 520px) 
{
    .search-user-list-container 
    {
        width: 100% !important;
        margin: 8px !important;
    }

    .search-background
    {
        margin: 8px !important;
    }

    .search-field
    {
        width: 90vw !important;
        margin: 16px !important; 
    }
}
</style>