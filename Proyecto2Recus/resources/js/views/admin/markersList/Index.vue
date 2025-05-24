<template>
    <div class="grid">
        <router-link class="link-panel-admin" to="/admin">← Volver al panel ←</router-link>
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent ps-0 pe-0">
                    <h5 class="float-start mb-0">Listas de Marcadores</h5>
                </div>

                <DataTable v-model:filters="filters" :value="markerLists" paginator :rows="5"
                           :globalFilterFields="['id','name','emoji_identifier','created_at']"
                           stripedRows dataKey="id" size="small">

                    <template #header>
                        <Toolbar pt:root:class="toolbar-table">
                            <template #start>
                                <IconField>
                                    <InputIcon class="pi pi-search" />
                                    <InputText v-model="filters['global'].value" placeholder="Buscar" />
                                </IconField>

                                <Button type="button" icon="pi pi-filter-slash" label="Limpiar" class="ml-1" outlined @click="initFilters()" />
                                <Button type="button" icon="pi pi-refresh" class="h-100 ml-1" outlined @click="fetchMarkerLists()" />
                            </template>
                            <template #end>
                                <Button v-if="can('marker-list-create')" icon="pi pi-plus" label="Crear Lista" 
                                        @click="$router.push('marker-lists/create')" class="float-end" />
                            </template>
                        </Toolbar>
                    </template>

                    <template #empty> No se encontraron listas de marcadores. </template>

                    <Column field="id" header="ID" sortable />
                    <Column field="name" header="Nombre" sortable />
                    <Column header="Icono">
                        <template #body="slotProps">
                            {{ getEmojiById(slotProps.data.emoji_identifier) }}
                        </template>
                    </Column>
                    <Column field="created_at" header="Creado el" sortable />

                    <Column class="pe-0 me-0 icon-column-2">
                        <template #body="slotProps">
                            
                            <router-link v-if="can('post-edit')" 
                                        :to="{ name: 'markersLists.edit', params: { id: slotProps.data.id } }">
                                <Button icon="pi pi-pencil" severity="info" size="small" class="mr-1" />
                            </router-link>
                            

                            <Button v-if="can('post-delete')" 
                                    icon="pi pi-trash" severity="danger" 
                                    @click.prevent="deleteMarkerList(slotProps.data.id, slotProps.index)" 
                                    size="small" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAbility } from '@casl/vue';
import { FilterMatchMode } from "@primevue/core/api";
import { 
    getAllMarkerLists, 
    getEmojiById,
    deleteMarkerList as deleteMarkerListApi 
} from '../../../composables/useMarkerList';

const { can } = useAbility();

const markerLists = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

const fetchMarkerLists = async () => {
    markerLists.value = await getAllMarkerLists();
};

const deleteMarkerList = async (id, index) => {
    try {
        await deleteMarkerListApi(id);
        markerLists.value.splice(index, 1);
    } catch (error) {
        console.error('Error deleting marker list:', error);
    }
};

onMounted(() => {
    fetchMarkerLists();
});
</script>

<style scoped>
* {
    color: black;
}
</style>