<script setup>
import { ref } from 'vue';
import Friendsgroups from './pages/friendsgroups.vue';
import GeneralSettingsView from './pages/GeneralSettingsView.vue';
import AccountSettingsView from './pages/AccountSettingsView.vue';
import MarkerListSettingsView from './pages/MarkerListSettingsView.vue';

import useAuth from "@/composables/auth";

const { logout } = useAuth();

const conf_page = ref("general");
const show_pages = ref(true);

function changePage(page) {

    conf_page.value = page;
}

const items = ref([
    {
        label: 'General',
        icon: 'pi pi-pencil',
        command: () => {
            toast.add({ severity: 'info', summary: 'Add', detail: 'Data Added', life: 3000 });
        },
    }
]);
</script>

<template>
    <div class="settings-background">
        <div class="settings-side-menu">

            <Button class="secondary-button" @click="changePage('general')" icon="pi pi-align-justify"
                :label="$t('generalSettingsButton')" />
            <Button class="secondary-button" @click="changePage('account')" icon="pi pi-user"
                :label="$t('accountSettingsButton')" />
            <Button class="secondary-button" @click="changePage('groups')" icon="pi pi-users"
                :label="$t('groupsSettingsButton')" />
            <Button class="secondary-button" @click="changePage('markers')" icon="pi pi-map-marker"
                :label="$t('markersSettingsButton')" />
            <hr>
            <Button class="secondary-button danger-button-hover" style="padding: 8px !important;" @click="logout"
                icon="pi pi-sign-out" :label="$t('signoutbutton')" />
        </div>

        <div v-if="show_pages" class="settings-pages">
            <div v-if="conf_page == 'general'">
                <GeneralSettingsView />
            </div>

            <div v-if="conf_page == 'account'">
                <AccountSettingsView />
            </div>

            <div v-if="conf_page == 'groups'">
                <Friendsgroups />
            </div>

            <div v-if="conf_page == 'markers'">
                <MarkerListSettingsView />
            </div>
        </div>
    </div>
</template>

<style>
/* Cuando el ancho de la pantalla sea menor a 512px, ocultamos el texto */
@media (max-width: 512px) {
    .secondary-button .p-button-label {
        display: none;
    }

    .settings-side-menu button {
        width: 42px !important;
        height: 42px !important;
        margin: 0 !important;
        padding: 0px !important;
        border-radius: 50% !important;
    }
}
</style>