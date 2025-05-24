<script setup>
import { ref } from "vue";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import SettingField from "@/components/SettingField.vue";

const toast = useToast();

const color = ref("");
const new_username = ref("");
const new_description = ref("");

function showMessage(message, type) {
    let adapt_type = type === "good" ? "success" : type === "bad" ? "error" : "warn";
    if (adapt_type === "warn") console.log(message);

    toast.add({ severity: adapt_type, summary: 'Info', detail: message, life: 3000 });
}

const handleFileChange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("image", file);

    try {
        const response = await axios.post("http://127.0.0.1:8000/api/users/updateimg", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        console.log(response);
        showMessage("Image updated", "good");
    } catch (err) {
        console.error(err);
        showMessage("Error while uploading image", "bad");
    }
};

const updateUsername = async () => {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/users/updateusername", {
            username: new_username.value,
        });
        showMessage("Name updated", "good");
        console.log(response);
    } catch (error) {
        showMessage("Error while updating name", "bad");
        console.log(error);
    }
};

const updateDescription = async () => {
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/users/updatedescription", {
            desc: new_description.value,
        });
        showMessage("Description updated", "good");
        console.log(response);
    } catch (error) {
        showMessage("Error while updating description", "bad");
        console.log(error);
    }
};
</script>

<template>
  <div>
    <!-- Foto de perfil -->
    <SettingField
      title="Modificar foto de perfil"
      description="Cambiar imagen con la que los otros usuarios pueden asociarte."
      type="file"
      accept="image/*"
      @change="handleFileChange"
    />
    <hr />

    <!-- Nombre -->
    <SettingField
      v-model="new_username"
      title="Modificar nombre de perfil"
      description="Cambiar nombre (no el @username) con la que los otros usuarios pueden asociarte."
      type="input"
      placeholder="New name"
      icon="pi-pen-to-square"
      @submitInput="updateUsername"
    />
    <hr />

    <!-- Descripción -->
    <SettingField
      v-model="new_description"
      title="Modificar descripcion de usuario"
      description="Cambiar descripcion para que la vean otros usuarios."
      type="input"
      placeholder="New description"
      icon="pi-pen-to-square"
      @submitInput="updateDescription"
    />
    <hr />

    <!-- Contraseña -->
    <SettingField
      title="Modificar contraseña"
      description="Cambiar contraseña de la cuenta."
      type="button"
      icon="pi-pen-to-square"
      @click="() => showMessage('Cambiar contraseña no implementado aún', 'warn')"
    />
    <hr />

    <!-- Color 
    <SettingField
      v-model="color"
      title="Modificar color de cuenta"
      description="Color de identificación de tu cuenta para los demás."
      type="color"
    />

    <hr />
-->
    <Toast />
  </div>
</template>
