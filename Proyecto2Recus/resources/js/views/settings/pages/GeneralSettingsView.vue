<script setup>
import { ref, onMounted, watch } from "vue";
import LocaleSwitcher from "@/components/LocaleSwitcher.vue";
import {SetSatelliteMapStyleCookie, GetSatelliteMapStyleCookie} from "@/composables/SettingsCookies";

const satelliteChecked = ref();

onMounted(() => {
    satelliteChecked.value = GetSatelliteMapStyleCookie();
});

watch(satelliteChecked, (newVal) => {
    SetSatelliteMapStyleCookie(newVal);
});

</script>

<template>
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="w-90">
                <p class="settings-option-title">Language</p>
                <p class="settings-option-description">Change the lenguaje of all page.</p>
            </span>
            <LocaleSwitcher />
        </div>

        <hr>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="w-90">
                <p class="settings-option-title">Hide my Real-Time position</p>
                <p class="settings-option-description">Hiddes your real time position so people can't track you</p>
            </span>
            <ToggleSwitch v-model="checked" />
        </div>

        <hr>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="w-90">
                <p class="settings-option-title">Use Satellite Map Style</p>
                <p class="settings-option-description">Can change map style between Satellite and Standard</p>
            </span>
            <ToggleSwitch v-model="satelliteChecked" />
        </div>

    </div>

</template>