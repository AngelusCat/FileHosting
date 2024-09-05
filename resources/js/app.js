import './bootstrap';
import {createApp} from "vue";
import formVue from "../vue-components/form.vue"

createApp({
    components: {
        formVue
    }
}).mount('#app');
