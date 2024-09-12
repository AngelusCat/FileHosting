import './bootstrap';
import {createApp} from "vue";
import formVue from "../vue-components/form.vue"
import fileOrForm from "../vue-components/fileOrForm.vue"

createApp({
    components: {
        formVue
    }
}).mount('#app');

createApp({
    components: {
        fileOrForm
    }
}).mount('#app1');
