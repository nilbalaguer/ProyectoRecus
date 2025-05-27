<!-- SCRIPT -->
<script setup>
import { onMounted, ref } from 'vue';
import { authStore } from '../../store/auth';
import useUsers from '../../composables/users';
import { useRoute } from 'vue-router'
import Popover from 'primevue/popover';
import ConfirmButtonPopup from '../../components/ConfirmButtonPopup.vue';
import { showAllMarkersFromUserId } from "../../composables/useMarkers.js";
import { getEmojiById, getMarkerListById } from "../../composables/useMarkerList.js";
import { GetMapImageUrlFromCoordsAndZoom } from "../../composables/MapUtils.js";

const { updateImg } = useUsers();
const route = useRoute();

const userPFP = ref("");
const requestedUserData = ref({});
const requestedUserFriendList = ref([]);
const requestMarkerData = ref();
const markersDividedByList = ref([]);

// Inicializa todas las funciones
async function loadDataFromRequestUser() {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/user/showUserByUsername?username=' + route.params.username);
        if (response.data) {
            requestedUserData.value = response.data;
            let foto = (response.data.media_url ? response.data.media_url.split("localhost/")[1] : "");
            if (response.data.media_url == null) {
                userPFP.value = "/images/default_pf.jpg";
            } else {
                userPFP.value = "/" + foto;
            }

            getFriendsFromRequestedUser();
            checkFriendStatus();

            requestMarkerData.value = await showAllMarkersFromUserId(requestedUserData.value.id);
            loadMarkers();

        } else {
            requestedUserData.value = {};
        }
    } catch (error) {
        console.error("[ProfileView.vue] Error:", error);
        requestedUserData.value = {};
    }
}

async function loadMarkers() {
    markersDividedByList.value = await requestMarkerData.value.markers.reduce(async (accPromise, marker) => {
        const acc = await accPromise;
        const listId = marker.marker_list_id;

        if (!acc[listId]) {
            acc[listId] = { marker_list: await getMarkerListById(listId), markers: [] };
        }

        acc[listId].markers.push(marker);
        return acc;
    }, Promise.resolve({}));
}


async function getFriendsFromRequestedUser() {
    if (!requestedUserData.value.id) return;

    axios.get('http://127.0.0.1:8000/api/friends/allFriends?user_id=' + requestedUserData.value.id)
        .then(response => {
            requestedUserFriendList.value = response.data || [];
        })
        .catch(error => {
            console.error("[ProfileView.vue] Error:", error);
            requestedUserFriendList.value = [];
        });
}

async function deleteFriend(friend_id) {
    try {
        const resp = await axios.post('http://127.0.0.1:8000/api/friends/delete', {
            friend_id:friend_id
        });

        console.log(resp)
    } catch (error) {
        console.log(error)
    }

    
}

async function deleteRequestAsSender(friend_id) {
    if (!friend_id) {
        console.error("Friend_id is not defined on deleteRequestAsSender(friend_id)");
        return;
    }
    axios.get(`http://127.0.0.1:8000/api/friends/destroyRequestAsSender?id_sender=${authStore().user.id}&id_receiver=${friend_id}`)
        .then(response => {
            requestedUserFriendList.value = requestedUserFriendList.value.filter(friend => friend.user.id !== Number(friend_id));

            if (friend_id == requestedUserData.value.id)
                friendRequestStatus.value = false;
        })
        .catch(error => {
            console.error('There was an error deleting the sender friend request:', error.response?.data || error.message);
        });
}

async function sendRequest(id_reciver) {
    await axios.post('http://127.0.0.1:8000/api/friend', {
        "id_sender": authStore().user.id,
        "id_receiver": id_reciver
    }).then(response => {
        if (id_reciver == requestedUserData.value.id)
            friendRequestStatus.value = true;
    }).catch(error => {
        console.error(error);
    });
}

onMounted(async () => {
    await loadDataFromRequestUser();
})

// PrimeVue Popover template code
const op = ref();
const toggle_showFriends = (event) => {
    op.value.toggle(event);
}

const friendRequestStatus = ref(null);
function checkFriendStatus() {
    axios.get(`/api/friends/getRequestStatus?friend_id=${requestedUserData.value.id}`)
        .then(response => {
            friendRequestStatus.value = response.data.value;
            console.log("friendRequestStatus = " + friendRequestStatus.value);
        })
        .catch(error => {
            console.error('There was an error deleting the friendship:', error.response?.data || error.message);
        });
}

function ProfileIsVisible() {
    if (requestedUserData.value.id === authStore().user.id)
        return true;
    else if (friendRequestStatus.value == true)
        return true;
    else
        return false;
}

</script>

<template>
    <div v-if="requestedUserData.id && friendRequestStatus !== null" class="profile-background">

        <div class="profile-info-container" style="background: linear-gradient(#99de45, #000000);">
            <img :src="userPFP" alt="Profile Image" class="profile-info-pfp">

            <h1 class="profile-info-name">{{ requestedUserData.name }}</h1>
            <h3 class="profile-info-username">@{{ requestedUserData.username }}</h3>
            <p>{{ requestedUserData.desc }}</p>

            <span v-if="authStore().user.id != requestedUserData.id" class="m-1">

                <Button v-if="friendRequestStatus === false" @click="sendRequest(requestedUserData.id)"
                    class="primary-button" label="Add Friend"
                    style="padding: 8px !important; padding-left: 12px !important; padding-right: 12px !important;" />

                <Button v-else @click="deleteRequestAsSender(requestedUserData.id)" class="secondary-button danger-button-hover"
                    label="UnFriend" />
            </span>

            <span class="m-1">
                <button v-if="false" class="secondary-button m-1">🗺️ {{ $t('viewfriendmap') }}</button>

                <button v-ripple @click="toggle_showFriends" class="secondary-button m-1"
                    style="--p-ripple-background: black">
                    <b>{{ requestedUserFriendList.length }}</b>
                    {{ $t('friendscounter') }}
                </button>
            </span>
        </div>

        <div v-if="ProfileIsVisible()" class="profile-markers-list m-3">
            <h4>📍 ALL MARKERS</h4>
            <div v-if="requestMarkerData" class="d-flex gap-3 w-100" style="overflow-x: scroll;">
                <div v-for="marker in requestMarkerData.markers">
                    <p class="m-0">{{ marker.name }}</p>
                    <img :src="GetMapImageUrlFromCoordsAndZoom({ lng: marker.lng, lat: marker.lat })">
                </div>
            </div>

            <div v-for="(markerList, index) in markersDividedByList" :key="index">
                <h4 class="mt-5">{{ getEmojiById(markerList.marker_list.emoji_identifier) }} {{
                    markerList.marker_list.name }}</h4>
                <div v-if="requestMarkerData" class="d-flex gap-3 w-100" style="overflow-x: scroll;">
                    <div v-for="marker in markerList.markers" :key="marker.id">
                        <p class="m-0">{{ marker.name }}</p>
                        <img :src="GetMapImageUrlFromCoordsAndZoom({ lng: marker.lng, lat: marker.lat })">
                    </div>
                </div>
            </div>

        </div>

        <div style="height: 64px;"></div>

        <!-- Friends Popup -->
        <Popover ref="op">
            <div class="flex flex-col gap-4" style="
            overflow-y: scroll; height: 25vh; scrollbar-width: thin; scrollbar-color: black white;">
                <div>
                    <ul class="list-none p-0 m-0 flex flex-col">
                        <li>
                            <h4 class="m-0 p-0" style="color: #000000;">
                                Friends
                            </h4>
                        </li>

                        <li v-for="user in requestedUserFriendList" :key="user.name"
                            class="flex items-center gap-2 px-2 py-3 hover:bg-emphasis cursor-pointer rounded-2 popover-li-hover">
                            <div>
                                <div class="d-flex flex-column">
                                    <span class="search-user-information-name" style="color: #000000;">
                                        <a :href="'/profile/' + user.user.username" style="color: black !important;">
                                            {{ user.user.name }}
                                        </a>
                                    </span>
                                    <div class="d-flex justify-content-between align-items-center">

                                        <span class="search-user-information-username"
                                            style="color: white; background-color: #000000; width: fit-content;">
                                            <a :href="'/profile/' + user.user.username"
                                                style="color: white !important;">
                                                @{{ user.user.username }}
                                            </a>
                                        </span>


                                        <ConfirmButtonPopup v-if="authStore().user.id == requestedUserData.id"
                                            name="Delete" header="Delete Friend" positive_option="Delete Friend"
                                            positive_severity="danger" button_class="danger-button border-0"
                                            @confirmed="(result) => { if (result) { deleteFriend(user.id) } }" />

                                    </div>
                                </div>
                            </div>

                        </li>
                    </ul>

                </div>
            </div>
        </Popover>


    </div>
</template>
