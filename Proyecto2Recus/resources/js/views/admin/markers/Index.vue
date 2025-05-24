<template>
    <div class="grid">
        <router-link class="link-panel-admin" to="/admin">← Volver al panel ←</router-link>
        <div class="col-12">
            <div class="card">

                <div class="card-header bg-transparent ps-0 pe-0">
                    <h5 class="float-start mb-0">Markers</h5>
                </div>

                <DataTable v-model:filters="filters" :value="markerItems" paginator :rows="5"
                           :globalFilterFields="['id','name','description','lng','lat','pitch','bearing','zoom','marker_list_id','user_id','created_at']"
                           stripedRows dataKey="id" size="small">

                    <template #header>
                        <Toolbar pt:root:class="toolbar-table">
                            <template #start>
                                <IconField>
                                    <InputIcon class="pi pi-search" />
                                    <InputText v-model="filters['global'].value" placeholder="Buscar" />
                                </IconField>

                                <Button type="button" icon="pi pi-filter-slash" label="Limpiar" class="ml-1" outlined @click="initFilters()" />
                                <Button type="button" icon="pi pi-refresh" class="h-100 ml-1" outlined @click="getMarkers()" />
                            </template>
                            <template #end>
                                <Button v-if="can('marker-create')" icon="pi pi-external-link" label="Crear Marker" @click="$router.push('markers/create')" class="float-end" />
                            </template>
                        </Toolbar>
                    </template>

                    <template #empty> No se encontraron marcadores. </template>

                    <Column field="id" header="ID" sortable />
                    <Column field="name" header="Nombre" sortable />
                    <Column field="description" header="Descripción" sortable />
                    <Column field="lng" header="Longitud" sortable />
                    <Column field="lat" header="Latitud" sortable />
                    <Column field="pitch" header="Pitch" sortable />
                    <Column field="bearing" header="Bearing" sortable />
                    <Column field="zoom" header="Zoom" sortable />
                    <Column field="marker_list_id" header="Lista" sortable />
                    <Column field="user_id" header="Usuario" sortable />
                    <Column field="created_at" header="Creado el" sortable />

                    <Column class="pe-0 me-0 icon-column-2">
                        <template #body="slotProps">
                            <router-link v-if="can('marker-edit')" :to="{ name: 'markers.edit', params: { id: slotProps.data.id } }">
                                <Button icon="pi pi-pencil" severity="info" size="small" class="mr-1" />
                            </router-link>

                            <Button icon="pi pi-trash" severity="danger" @click.prevent="deleteMarker(slotProps.data.id, slotProps.index)" size="small" />
                        </template>
                    </Column>

                </DataTable>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import useMarkers from "../../../composables/markers";
import { useAbility } from '@casl/vue';
import { FilterMatchMode } from "@primevue/core/api";

// Usamos el composable de markers
const { markers, getMarkers, deleteMarker } = useMarkers();
const { can } = useAbility();

// Computed para prevenir errores si markers.value no está listo
const markerItems = computed(() => markers.value?.data ?? []);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

onMounted(() => {
    getMarkers();
});
</script>

<style scoped>
* {
    color: black;
}
</style>
