<script setup>
import axios from 'axios';
import { ref } from 'vue';
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

const showMessageBool = ref(false);
const popupMessage = ref("");
const messageType = ref("");

function showMessage(message, type) {
    let adapt_type = "";

    if (type == "good") {
        adapt_type = "success";
    } else if (type == "bad") {
        adapt_type = "error";
    } else {
        adapt_type = "warn";
        console.log(message);
    }

    toast.add({ severity: adapt_type, summary: 'Info', detail: message, life: 3000 });

    /*
    clearTimeout();

    showMessageBool.value = true;

    messageType.value = type;
    popupMessage.value = message;

    setTimeout(() => {
        showMessageBool.value = false;
    }, 3000);
    */
}

async function CreateGroup(name) {
    try {
        let response = await axios.post("http://127.0.0.1:8000/api/friends/createGroup",{
            name: name
        });

        console.log(response);

        showMessage(response.data.message + ": " + groupname.value, response.data.type);

        ShowMyGroups();

    } catch (error) {
        console.log(error);
        showMessage("Error while creating group " + error, "bad");
    }
}

async function dropGroup(id) {
    try {
        let response = await axios.post("http://127.0.0.1:8000/api/friends/dropGroup", {
            id_group: id
        })

        console.log(response);

        showMessage(response.data.message, response.data.type);

        ShowMyGroups();
    } catch (error) {
        showMessage("Error while droping group "+error, "bad");
    }    
}

async function ShowMyGroups() {
    try {
        let response = await axios.get("http://127.0.0.1:8000/api/friends/showMyGroups");

        myGroups.value = response.data;

        console.log(myGroups.value);
    } catch (error) {
        console.log(error);
        showMessage("Error while showing groups "+error, "bad");
    }
}

async function showMyFriends() {
    axios.get('http://127.0.0.1:8000/api/friends/allFriends?user_id='+user_id.value)
    .then(response => {
        users.value = response.data;
    })
    .catch(error => {
        console.error("[GroupComponentConfigurationView] Error:", error);
    })
}

/*
async function showJoinedGroups() {
    try {
        let response = await axios.get("http://127.0.0.1:8000/api/friends/showJoinedGroups");

        joinedGroups.value = response.data;

        console.log(joinedGroups.value);
    } catch (error) {
        console.log(error);
        showMessage("Error while showing joined groups "+error, "bad");
    }
}
*/

function friendAddMenu(id_group) {
    addingFriendToGroup.value = id_group;
    showMyFriends();
    showFriendsInGroup();
}

//Add friend to a group
async function addFriendToGroup(friend_id) {
    try {
        let response = await axios.post("http://127.0.0.1:8000/api/friends/addToGroup", {
            "id_group": addingFriendToGroup.value,
            "id_target_user": friend_id,
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
        let response = await axios.get("http://127.0.0.1:8000/api/friends/friendsInGroup?id_group="+addingFriendToGroup.value);

        friendsInGroup.value = response.data.users
        console.log(response);
        console.log(friendsInGroup.value);

    } catch (error) {
        console.log(error + "Group: " + addingFriendToGroup.value);
    }
}

async function expulseFriendFromGroup(deleting_user_id) {
    console.log(deleting_user_id);
    try {
        let response = await axios.post("http://127.0.0.1:8000/api/friends/kickFromGroup", {
            "id_user": deleting_user_id,
            "id_group": addingFriendToGroup.value,
        });

        showMessage(response.data.message, response.data.type);
    } catch (error) {

        console.log(error);
        showMessage(error, "bad");
    }

    showMyFriends();
    showFriendsInGroup();
}

//showJoinedGroups();
ShowMyGroups();
</script>

<template>
    <div class="groups-background">
        <!--
        <div>
            <h2>Create Group</h2>
            <button @click="CreateGroup(groupname)">Create Group</button>
            <input v-model="groupname" placeholder="Group Name">
        </div>
        -->
        <div class="created-joined-groups">
            <div class="groups-divs">
                <div v-for="(item, index) in myGroups" :key="index" class="search-group-container">
                    <div>
                        <b><p>{{ item.name }}</p></b>
                    </div>
                    <div class="friend-groups-admin-delete-button">
                        <button @click="friendAddMenu(item.id)" class="secondary-button">Admin</button>
                        <!-- Button trigger modal -->
                        <button @click="dropGroup(item.id)" class="secondary-button danger-button-hover">
                            {{ $t('deletebutton') }}
                        </button>
                    </div>
                </div>
            </div>
            <!--
            <div class="groups-divs">
                <h3>Groups i joined</h3>
                <div v-for="(item, index) in joinedGroups" :key="index" class="search-group-container">
                    <div>
                        <p>{{ item.name }}</p>
                    </div>
                </div>
                {{ joinedGroups }}
            </div>
            -->
        </div>
        <!--Add friend to group menu-->
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
                            <button @click="expulseFriendFromGroup(user.id)" class="secondary-button danger-button-hover">{{ $t('kickuserfromgroup') }}</button>
                        </div>
                    </div>
                </div>

                <hr>
                <h3>{{ $t('do_you_want_to_add_someone') }}</h3>
                
                <div>
                    <div v-for="(user, index) in users" :key="index">
                        <div class="search-user-container" v-if="user.request_status == 1 && !friendsInGroup.some(f => f.username === user.user.username)">
                            <div class="search-user-information-container" v-if="user.request_status == 1 && !friendsInGroup.some(f => f.username === user.user.username)">
                                <div>
                                    <img src="/images/icon_profile.svg" alt="User image" class="search-user-information-image">
                                </div>
                                <div class="search-user-information">
                                    <b><p class="search-user-information-name">{{ user.user.name }}</p></b>
                                    <p class="search-user-information-username">{{ user.user.username }}</p>
                                </div>
                            </div>
                            <div v-if="user.request_status == 1 && !friendsInGroup.some(f => f.username === user.user.username)">
                                <button @click="addFriendToGroup(user.user.id)" class="secondary-button">{{ $t('addFriendText') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="adminGroupCloseButton secondary-button" @click="addingFriendToGroup = null" >Close</button>
            </div>
        </transition>
        
        <!--Atention message-->
        <!-- <div v-if="showMessageBool" :class="[messageType == 'good' ? 'search-popup-message-good' : 'search-popup-message-bad']">
            <h3>Info:</h3>
            <p>{{ popupMessage }}</p>
        </div> -->
        <Toast />

        <div class="create-group-button-configuration">
            <div>
                <input placeholder="Group name..." class="secondary-button" v-model="groupname"><button class="secondary-button" @click="CreateGroup(groupname)">Create Group</button>
            </div>
        </div>
    </div>
</template>