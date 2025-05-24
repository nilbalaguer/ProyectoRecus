import { ref } from "vue";
import { defineStore } from "pinia";

export const langStore = defineStore("langStore", () => {
    // Verifica que window.config.locales tenga todos tus idiomas
    // Ejemplo: { ca: "Català", es: "Español", en: "English" }
    const locale = ref(window.config.locale || 'ca'); // Valor por defecto
    const locales = ref(window.config.locales || {
        ca: "Català",
        es: "Español",
        en: "English"
        // Añade más si es necesario
    });

    function setLocale(l) {
        locale.value = l;
    }

    return { locale, locales, setLocale };
}, { persist: true });