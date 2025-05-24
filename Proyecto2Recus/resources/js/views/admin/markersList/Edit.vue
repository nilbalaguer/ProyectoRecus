<template>
    <div class="grid">
        <router-link to="/admin/markersLists">← Volver a listas de marcadores ←</router-link>
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="form-container">
                        <div class="form-section">
                            <h5 class="section-title">Editar Lista de Marcadores</h5>
                            
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input v-model="markerList.name" type="text" class="form-control" id="name">
                                <div class="text-danger mt-1">
                                    <div v-for="message in validationErrors?.name">
                                        {{ message }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="emoji">Icono</label>
                                <Dropdown 
                                    v-model="markerList.emoji_identifier" 
                                    :options="emojiOptions" 
                                    optionLabel="emoji" 
                                    optionValue="id"
                                    placeholder="Selecciona un icono"
                                    class="w-100">
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value">
                                            <span>{{ getEmojiById(slotProps.value) }}</span>
                                        </div>
                                        <span v-else>
                                            {{ slotProps.placeholder }}
                                        </span>
                                    </template>
                                    <template #option="slotProps">
                                        <div>
                                            <span>{{ slotProps.option.emoji }}</span>
                                            <span class="ml-2">{{ slotProps.option.name }}</span>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea v-model="markerList.description" class="form-control" id="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="user">Usuario</label>
                                <Dropdown 
                                    v-model="markerList.user_id" 
                                    :options="userList" 
                                    optionLabel="name" 
                                    optionValue="id"
                                    placeholder="Selecciona un usuario"
                                    class="w-100" />
                            </div>

                            <div class="text-right mt-4">
                                <button :disabled="isLoading" class="btn btn-primary" @click="submitForm">
                                    <span v-if="isLoading">Guardando...</span>
                                    <span v-else>Guardar Cambios</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Toast />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute } from "vue-router";
import { useToast } from 'primevue/usetoast';
import {useMarkerList} from "@/composables/useMarkerList";
import useUsers from "@/composables/users";

const toast = useToast();
const route = useRoute();
const { emojiDictionary, getMarkerListById, updateMarkerListById, validationErrors, isLoading } = useMarkerList();
const { getUsers, userList } = useUsers();

const markerList = reactive({
    id: null,
    name: '',
    description: '',
    emoji_identifier: null,
    user_id: null
});

const emojiOptions = ref(emojiDictionary);

const getEmojiById = (id) => {
    const found = emojiOptions.value.find(emoji => emoji.id === id);
    return found ? found.emoji : "❓";
};

onMounted(async () => {
    await getUsers();
    const listData = await getMarkerListById(route.params.id);
    
    if (listData) {
        markerList.id = listData.id;
        markerList.name = listData.name;
        markerList.description = listData.description;
        markerList.emoji_identifier = listData.emoji_identifier;
        markerList.user_id = listData.user_id;
    }
});

const submitForm = async () => {
    try {
        await updateMarkerListById(markerList['id'], markerList['name'], markerList['emoji_identifier']);
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Lista actualizada correctamente',
            life: 3000
        });
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo actualizar la lista',
            life: 3000
        });
    }
};
</script>

<style scoped>
.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.section-title {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

textarea.form-control {
    min-height: 100px;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    padding: 0.5rem 1.5rem;
}

.btn-primary:hover {
    background-color: var(--primary-color-dark);
    border-color: var(--primary-color-dark);
}

.text-danger {
    color: #dc3545;
    font-size: 0.875rem;
}

* {
    color: white;
}

</style>