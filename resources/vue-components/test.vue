<script setup>
    import {ref} from "vue";

    let IsFileBeingEdited = ref(false);

    // const props = defineProps(['name'])

    let props = defineProps(['originalName']);

</script>

<template>
    <div v-if="IsFileBeingEdited">

    </div>
    <div v-else>
<!--        <p>For example - {{ props.name }}</p>-->
        <p>Test: {{ props.originalName }}</p>
    </div>
</template>

<style scoped>

</style>
