import './bootstrap';
import {createApp} from "vue";
import formVue from "../vue-components/form.vue"
import Test from "../vue-components/test.vue"

createApp({
    components: {
        formVue
    }
}).mount('#app');

createApp({
    components: {
        Test
    }
}).mount('#test');
