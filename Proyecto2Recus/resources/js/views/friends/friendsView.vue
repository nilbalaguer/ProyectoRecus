<template>
    <div id="backgroundfriends">
        <div id="friendsbuttons">
            <button :class="{ friendsbuttonsselected: pages, friendsbuttonsNOTselected: !pages }" @click="page1">
                {{$t('sendRequests')}}
            </button>
            <button :class="{ friendsbuttonsselected: !pages, friendsbuttonsNOTselected: pages }" @click="page2">
                {{ $t('recivedRequests') }}
            </button>
        </div>
        
        <!--Requests i send-->
        <div v-if="pages" class="friendrequestspage">
            <div v-if="loading" v-for="n in 4" :key="n" class="search-user-container">
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
            <div v-for="(user, index) in users" :key="index" class="search-user-container">
                <div class="search-user-information-container" v-if="user.request_status == 0">
                    <div>
                        <img src="/images/icon_profile.svg" alt="User image" class="search-user-information-image">
                    </div>
                    <div class="search-user-information">
                        <b><p class="search-user-information-name">{{ user.reciver.name }}</p></b>
                        <p class="search-user-information-username">@{{ user.reciver.username }}</p>
                    </div>
                </div>
                <div v-if="user.request_status == 0">
                    <button @click="deleteFriend(user.id)" class="secondary-button">{{ $t('cancelFriendRequest') }}</button>
                </div>
            </div>
            <div v-if="users.length < 1 && !loading" id="notfoundsearcherror">
                <h2>{{$t('withoutrequests')}}</h2>
            </div>
        </div>
        <!--Requests i recive-->
        <div v-if="!pages" class="friendrequestspage">
            <div v-if="loading" v-for="n in 4" :key="n" class="search-user-container">
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
            <div v-for="(user, index) in users" :key="index" class="search-user-container">
                
                <div class="search-user-information-container" v-if="user.request_status == 0">
                    <div>
                        <img src="/images/icon_profile.svg" alt="User image" class="search-user-information-image">
                    </div>
                    <div class="search-user-information">
                        <b><p class="search-user-information-name">{{ user.sender.name }}</p></b>
                        <p class="search-user-information-username">@{{ user.sender.username }}</p>
                    </div>
                </div>

                <div v-if="user.request_status == 0">
                    <button @click="acceptRequest(user.id)" class="secondary-button">{{ $t('acceptFriendRequest') }}</button>
                </div>

            </div>
            <div v-if="users.length < 1 && !loading" id="notfoundsearcherror">
                <h2>{{$t('withoutrequests')}}</h2>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { authStore } from "../../store/auth";
import axios from 'axios';

    const auth = authStore();
    const user_id = ref(auth.user?.id);

    const loading = ref(false);
    const users = ref([]);
    const pages = ref(true);

    async function cargarRequests() 
    {
        loading.value = true;
        await axios.get('http://127.0.0.1:8000/api/friends/myFriends?user='+user_id.value)
        .then(response => {
            users.value = response.data;
            //users.value = response.data.map(request => request.sender);
            loading.value = false;
        })
        .catch(error => {
            console.error("[SearchView.vue] Error:", error);
            loading.value = false;
        });
    }

    async function LoadRequestsSend() {
        loading.value = true;
        await axios.get('http://127.0.0.1:8000/api/friends/requestsSend?user='+user_id.value)
        .then(response => {
            users.value = response.data;

            loading.value = false;
        })
        .catch(error => {
            console.error("[FriendsView.vue] Error:", error);
            loading.value = false;
        })
    }

    function page1() {
        pages.value = true;
        users.value = [];
        LoadRequestsSend();
    }

    function page2() {
        pages.value = false;
        users.value = [];
        cargarRequests();
    }

    async function acceptRequest(id_friendship) {
        try {
            await axios.post('http://127.0.0.1:8000/api/friends/accept', {
                "id": id_friendship,
            })
            .then(response => {
                console.log(response);
                users.value = [];
                cargarRequests();
            })
            .catch(error => {
                console.error("[SearchView.vue] Error:", error);
            });

        } catch (error) {
            console.log(error);
        }
    }

    async function deleteFriend(id_friendship) {
        try {
            await axios.post('http://127.0.0.1:8000/api/friends/delete', {
                "friend_id": id_friendship,
            })
            .then(response => {
                console.log(response);
                users.value = [];
                LoadRequestsSend();
            })
            .catch(error => {
                console.error("[SearchView.vue] Error:", error);
            });
        } catch (error) {
            console.log(error);
        }
    }

    LoadRequestsSend();
</script>