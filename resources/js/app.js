import './bootstrap';
import {createApp} from "vue";
import viewingOrEditing from "../vue_components/viewingOrEditing.vue"
import viewing from "../vue_components/viewing.vue";
import editing from "../vue_components/editing.vue";
// import formVue from "../vue-components/form.vue"
// import fileOrForm from "../vue-components/fileOrForm.vue"
//
// createApp({
//     components: {
//         formVue
//     }
// }).mount('#app');
//
// createApp({
//     components: {
//         fileOrForm
//     }
// }).mount('#app1');

createApp({
}).component("viewing", viewing).component("editing", editing).component("viewingOrEditing", viewingOrEditing).mount("#app");
