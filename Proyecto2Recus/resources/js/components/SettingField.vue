<template>
    <div class="d-flex justify-content-between align-items-center my-3">
        <span :class="contentClass">
            <p class="settings-option-title">{{ title }}</p>
            <p class="settings-option-description">{{ description }}</p>
        </span>

        <!-- Tipo de input según prop -->
        <div v-if="type === 'button'">
            <Button class="btn secondary-button px-4" @click="onClick">
                <i v-if="icon" :class="['pi', icon]"></i>
                {{ buttonText }}
            </Button>
        </div>

        <div v-else-if="type === 'file'">
            <Button class="btn secondary-button px-4" @click="triggerFileInput">
                <i class="pi pi-upload"></i>
            </Button>
            <input type="file" ref="fileInput" :accept="accept" @change="onChange" style="display: none;" />
        </div>

        <div v-else-if="type === 'input'">
            <Button class="btn secondary-button px-4" @click="triggerDialog">
                <i v-if="icon" :class="['pi', icon]"></i>
                {{ buttonText }}
            </Button>
        </div>

        <div v-else-if="type === 'color'">
            <ColorPicker :value="modelValue" @input="updateModelValue" format="hex" />
        </div>
    </div>

    <!-- Dialog que contiene el campo de entrada y el botón de enviar -->
    <Dialog v-model:visible="showDialog" :header="title" @hide="hideDialog">
        <p>{{ description }}</p>
        <div class="d-flex flex-column gap-2" style="height: 100%; justify-content: flex-end;">
            <!-- Aquí está el input de texto -->
            <input v-model="inputValue" :placeholder="placeholder" class="form-control" type="text"
                style="color: black !important;" />

            <!-- El botón ahora estará al final -->
            <Button class="btn secondary-button px-4" @click="submitInput" style="margin-top: auto;">
                <i v-if="icon" :class="['pi', icon]"></i>
                {{ buttonText }}
            </Button>
        </div>
    </Dialog>


</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import Button from 'primevue/button';
import ColorPicker from 'primevue/colorpicker';
import Dialog from 'primevue/dialog';

const props = defineProps({
    title: String,
    description: String,
    type: { type: String, default: 'button' },
    modelValue: [String, Object],
    placeholder: String,
    accept: String,
    icon: String,
    buttonText: { type: String, default: 'Change' },
    contentClass: { type: String, default: 'w-75' },
});

const emit = defineEmits(['update:modelValue', 'change', 'click']);

const showDialog = ref(false); // Control de visibilidad del dialog
const inputValue = ref(props.modelValue); // Vinculamos el valor inicial con modelValue

// Observa cambios en modelValue y actualiza inputValue
watch(() => props.modelValue, (newValue) => {
    inputValue.value = newValue; // Asegura que inputValue se sincronice con modelValue
});

// Muestra el dialog
const triggerDialog = () => {
    inputValue.value = props.modelValue || ''; // Limpiar o asignar valor al abrir el dialog
    showDialog.value = true;
};

// Oculta el dialog
const hideDialog = () => {
    showDialog.value = false;
};

const fileInput = ref(null);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const onChange = (e) => {
    emit('change', e);
};

const onClick = () => {
    emit('click', props.modelValue);
};

// Actualización del modelo de valor para el v-model
const updateModelValue = (value) => {
    emit('update:modelValue', value);
};

// Función para manejar el envío del valor del input
const submitInput = () => {
    emit('update:modelValue', inputValue.value); // Emitir el valor actualizado
    showDialog.value = false; // Cerrar el dialog
    emit('submitInput'); // Emitir el evento a la vista
};

</script>

<style scoped>
/* Estilos comunes */
.setting-field-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.setting-field-button {
    display: flex;
    justify-content: flex-end;
}

.p-dialog {
    position: fixed !important;
    width: 75vw !important;
    max-width: 512px !important;
    max-height: 80vh;
    overflow-y: auto;
}

.p-dialog-header {
    background: none !important;
    padding: 10px;
    text-align: center;
}

.p-dialog-content {
    padding: 15px;
    overflow: hidden;
}

.setting-field-input {
    width: 100%;
}
</style>
