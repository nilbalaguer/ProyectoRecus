<template>
    <nav>

        <!--
            <ul class="navbar-nav mt-2 mt-lg-0 me-auto mb-2 mb-lg-0">
                <LocaleSwitcher />
            </ul>
            -->

        <div class="navMenu" id="nav">
            <div>
                <div @click="handleNav('/profile')" style="cursor: pointer;">
                    <img src="/images/icon_profile.svg" alt="Profile" class="icon">
                    {{ $t('profile') }}
                </div>
            </div>
            <!--
            <div>
                <router-link to="/"> 
                    <img src="/images/feed.svg" alt="Feet image" class="icon">
                    <p>Feed</p>
                </router-link>
            </div>
            -->
            <div class="nav-item">
                <router-link to="/home" aria-current="page">
                    <img src="/images/emoji_map.webp" alt="{{ $t('home') }}">
                </router-link>
            </div>
            <div>
                <router-link to="/search">
                    <img src="/images/icon_search.svg" alt="Profile" class="icon">
                    {{ $t('search') }}
                </router-link>
            </div>
            
            <!--
                <template v-if="!authStore().user?.name">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/login"
                        >{{ $t('login') }}</router-link
                        >
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/register">{{ $t('register') }}</router-link>
                    </li>
                </template>
<li v-if="authStore().user?.name" class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ authStore().user?.name }}
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><router-link class="dropdown-item" to="/admin">Admin</router-link></li>
        <li><router-link to="/admin/posts" class="dropdown-item">Post</router-link></li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="javascript:void(0)" @click="logout">Logout</a></li>
    </ul>
</li>
-->
        </div>
    </nav>
</template>

<script setup>

import useAuth from "@/composables/auth";
import LocaleSwitcher from "../components/LocaleSwitcher.vue";
import { authStore } from "../store/auth";

const { processing, logout } = useAuth();

import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

//No enseñar en la presentacion, era la unica forma de que funcionara
function handleNav(targetPath) {
    console.log(route.path)
    if (route.path.includes(targetPath)) {
        window.location.href = "/profile";
    } else {
        router.push(targetPath);
    }
}
</script>
