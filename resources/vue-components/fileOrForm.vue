<script setup>
    import {ref} from "vue";

    let isFileBeingEdited = ref(false);

    let props = defineProps(['originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId']);

    let action = "http://file/files/" + props.fileId;

    let passwordShow = ref(false);

    function changeVisibility()
    {
        let link = "http://file/w/" + props.fileId;
        fetch(link).then(response => response.json()).then(function (text) {
            if (text.isAuthorized) {
                isFileBeingEdited.value = true;
            } else {
                passwordShow.value = true;
            }
        });
    }

    function authorize(event)
    {
        event.preventDefault();
    }


</script>

<template>
    <div v-if="isFileBeingEdited">
            <form :action="action" method="POST" id="form">
                <input type="hidden" name="_token" :value="props.csrfToken" />
                <input type="hidden" name="_method" value="PATCH">
            </form>
        <h1>Файл - <input :value="props.originalName" name="name" form="form"></h1>
        <p>Размер: {{ props.size }}</p>
        <p>Дата загрузки: {{ props.uploadDate }}</p>
        <p>Описание: <input :value="props.description" name="description" form="form"></p>
        <p>Статус проверки на virus total: {{ props.securityStatus }}</p>
        <a :href="props.downloadLink">Скачать файл</a><br><br>
        <button type="submit" form="form">Сохранить изменения</button><br><br>
        <button @click="true">Выйти из редактирования</button>
    </div>
    <div v-else>
        <h1>Файл - {{ props.originalName }}</h1>
        <p>Размер: {{ props.size }}</p>
        <p>Дата загрузки: {{ props.uploadDate }}</p>
        <p>Описание: {{ props.description }}</p>
        <p>Статус проверки на virus total: {{ props.securityStatus }}</p>
        <a :href="props.downloadLink">Скачать файл</a><br><br>
        <button @click="changeVisibility">Редактировать</button>
        <div v-if="passwordShow">
            <form method="post">
                <input type="hidden" name="_token" :value="props.csrfToken" />
                <p>
                    Пароль: <input type="text" name="password">
                </p>
                <button type="submit" @click="authorize(event)">submit</button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
