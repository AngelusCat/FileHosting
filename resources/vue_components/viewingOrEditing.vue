<script setup>
import {ref} from "vue";

const fileId = ref(1);
const test = ref("viewing");

function askServerIfCurrentUserCanChangeFileMetadata()
{
    let response = fetch("http://file/authorized/" + fileId.value).then(text =>text.json()).then(example => console.log(example));
}

</script>

<template>
    <KeepAlive>
        <component :is="test"></component>
    </KeepAlive>
    <button @click="askServerIfCurrentUserCanChangeFileMetadata">Редактировать</button>
</template>

<style scoped>

</style>
