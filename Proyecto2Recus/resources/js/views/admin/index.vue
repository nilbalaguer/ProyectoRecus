<template>

    <div class="grid">
        <h1>WhereEverNever ADMIN</h1>
        <h2>Que quiere administrar ?</h2>

        <div class="index-panel-admin">
            <router-link to="/admin/users">Usuarios</router-link>
            <router-link to="/admin/roles">Roles</router-link>
            <router-link to="/admin/permissions">Permissions</router-link>
            <router-link to="/admin/markers">Markers</router-link>
            <router-link to="/admin/markersLists">Markers Lists</router-link>
        </div>
        
    </div>
</template>

<script setup>
console.log("Bienvendio admin");
</script>

<style scoped>
.index-panel-admin {
    display: flex;
    flex-direction: column;
}

.index-panel-admin a {
    border: 2px solid gray;
    padding: 10px;
    margin: 10px;
    border-radius: 10px;
}

.index-panel-admin a:hover {
    transition: 300ms;
    background-color: aliceblue;
}
</style>
