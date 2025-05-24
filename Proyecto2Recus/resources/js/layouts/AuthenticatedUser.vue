<template>
    <div>
        <Header/>
        <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>
        <Navbar/>
    </div>
</template>

<script setup>
import Navbar from './Nav.vue';
import Header from './Header.vue';
</script>
