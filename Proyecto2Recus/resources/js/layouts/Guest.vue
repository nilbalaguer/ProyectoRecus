<template>
    <div>
        <Header v-if="showLayout" />
        <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>
        <Navbar v-if="showLayout" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from './Nav.vue';
import Header from './Header.vue';

const route = useRoute(); // Obtiene la ruta actual

const showLayout = computed(() => {
    return route.path !== "/login" && route.path !== "/register"; // Usa && en lugar de ||
});
</script>
