import { createI18n } from 'vue-i18n'
import { langStore } from "@/store/lang";

let i18n;

export async function installI18n(app) {
    const store = langStore();
    i18n = createI18n({
        legacy: false,
        globalInjection: true,
        locale: store.locale,
        fallbackLocale: 'ca',
        messages: {}
    });
    
    app.use(i18n);
    await loadMessages(store.locale);
    return i18n;
}

export async function loadMessages(locale) {
    try {
        const messages = await import(`../lang/${locale}.json`);
        i18n.global.setLocaleMessage(locale, messages.default || messages);
        i18n.global.locale.value = locale;
    } catch (error) {
        console.error(`Failed to load messages for ${locale}:`, error);
    }
};

export default i18n;