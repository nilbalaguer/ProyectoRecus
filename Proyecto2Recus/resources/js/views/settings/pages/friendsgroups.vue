<script setup>
import axios from 'axios';
import { ref, computed } from 'vue';
import { authStore } from '../../../store/auth';
import { useToast } from "primevue/usetoast";

const toast = useToast();

const groupname = ref("");
const myGroups = ref([]);
const joinedGroups = ref([]);
const users = ref([]);

const auth = authStore();
const user_id = ref(auth.user?.id);

const addingFriendToGroup = ref(null);
const friendsInGroup = ref([]);

const friendsNotInGroup = computed(() => {
    return users.value.filter(user => 
        !friendsInGroup.value.some(friend => friend.id === user.id)
    );
});

function showMessage(message, type) {
    const typeMap = {
        good: 'success',
        bad: 'error',
    };
    toast.add({ severity: typeMap[type] ?? 'warn', summary: 'Info', detail: message, life: 3000 });
}

async function CreateGroup(name) {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/friends/createGroup", {
            name: name
        });
        showMessage(response.data.message + ": " + groupname.value, response.data.type);
        ShowMyGroups();
    } catch (error) {
        console.log(error);
        showMessage("Error while creating group " + error, "bad");
    }
}

async function dropGroup(id) {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/friends/dropGroup", {
            id_group: id
        });
        showMessage(response.data.message, response.data.type);
        ShowMyGroups();
    } catch (error) {
        showMessage("Error while dropping group " + error, "bad");
    }
}

async function ShowMyGroups() {
    try {
        const response = await axios.get("http://127.0.0.1:8000/api/friends/showMyGroups");
        myGroups.value = response.data;
    } catch (error) {
        console.log(error);
        showMessage("Error while showing groups " + error, "bad");
    }
}

async function showMyFriends() {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/friends/allFriends?user_id=' + user_id.value);
        users.value = response.data;

        users.value.forEach(user => {
            console.log("Amigo disponible:", user.username);
        });

    } catch (error) {
        console.error("[GroupComponentConfigurationView] Error:", error);
    }
}

function friendAddMenu(id_group) {
    addingFriendToGroup.value = id_group;
    showMyFriends();
    showFriendsInGroup();
}

async function addFriendToGroup(friend_id) {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/friends/addToGroup", {
            id_group: addingFriendToGroup.value,
            id_target_user: friend_id,
        });
        showMessage(response.data.message, response.data.type);
        showMyFriends();
        showFriendsInGroup();
    } catch (error) {
        console.log(error);
    }
}

async function showFriendsInGroup() {
    try {
        const response = await axios.get("http://127.0.0.1:8000/api/friends/friendsInGroup?id_group=" + addingFriendToGroup.value);
        friendsInGroup.value = response.data.users;

        const names = friendsInGroup.value.map(friend => friend.name);
        console.log("Amigos en el grupo:", names);

    } catch (error) {
        console.log("Error loading friends in group: " + error);
    }
}

async function expulseFriendFromGroup(deleting_user_id) {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/friends/kickFromGroup", {
            id_user: deleting_user_id,
            id_group: addingFriendToGroup.value,
        });
        showMessage(response.data.message, response.data.type);
    } catch (error) {
        console.log(error);
        showMessage(error, "bad");
    }
    showMyFriends();
    showFriendsInGroup();
}

ShowMyGroups();
</script>

<template>
    <div class="groups-background">
        <div class="created-joined-groups">
            <div class="groups-divs">
                <div v-for="(item, index) in myGroups" :key="index" class="search-group-container">
                    <div>
                        <b><p>{{ item.name }}</p></b>
                    </div>
                    <div class="friend-groups-admin-delete-button">
                        <button @click="friendAddMenu(item.id)" class="secondary-button">Admin</button>
                        <button @click="dropGroup(item.id)" class="secondary-button danger-button-hover">
                            {{ $t('deletebutton') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add friend to group menu -->
        <transition name="fade">
            <div v-if="addingFriendToGroup != null" class="friendlistgroups">
                <h3>{{ $t('friends_in_this_group') }}</h3>

                <div>
                    <div v-for="(user, index) in friendsInGroup" :key="index" class="search-user-container">
                        <div class="search-user-information-container">
                            <div>
                                <img src="/images/icon_profile.svg" alt="User image" class="search-user-information-image">
                            </div>
                            <div class="search-user-information">
                                <b><p class="search-user-information-name">{{ user.name }}</p></b>
                                <p class="search-user-information-username">{{ user.username }}</p>
                            </div>
                        </div>
                        <div>
                            <button @click="expulseFriendFromGroup(user.id)" class="secondary-button danger-button danger-button-hover">
                                {{ $t('kickuserfromgroup') }}
                            </button>
                        </div>
                    </div>
                </div>

                <hr>
                <h3>{{ $t('do_you_want_to_add_someone') }}</h3>

                <div>
                    <div v-for="(user, index) in friendsNotInGroup" :key="index" class="search-user-container">
                        <div class="search-user-information-container">
                            <div>
                                <img src="/images/icon_profile.svg" alt="User image" class="search-user-information-image">
                            </div>
                            <div class="search-user-information">
                                <b><p class="search-user-information-name">{{ user.name }}</p></b>
                                <p class="search-user-information-username">{{ user.username }}</p>
                            </div>
                        </div>
                        <div>
                            <button @click="addFriendToGroup(user.id)" class="secondary-button">
                                {{ $t('addFriendText') }}
                            </button>
                        </div>
                    </div>
                </div>

                <button class="adminGroupCloseButton secondary-button" @click="addingFriendToGroup = null">Close</button>
            </div>
        </transition>

        <Toast />

        <div class="create-group-button-configuration">
            <div>
                <input placeholder="Group name..." class="secondary-button" v-model="groupname" />
                <button class="secondary-button" @click="CreateGroup(groupname)">Create Group</button>
            </div>
        </div>
    </div>
</template>
