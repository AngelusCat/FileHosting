<script setup>
import {ref} from "vue";

let fileFromDropArea = ref();
let divStyle = ref("display: none");
let requiredPassword = ref(null);
let privatePassword = ref();
let props = defineProps(['modifyPassword']);
function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    e.preventDefault();
    const files = e.dataTransfer.files;
    if (files.length) {
        fileFromDropArea.value = files;
    }
}

function clickPublic()
{
    divStyle.value = "display: none";
    requiredPassword.value = null;
}

function clickPrivate()
{
    divStyle.value = "display: run-in";
    requiredPassword.value = true;
}

function generatePassword()
{
    fetch('/generatePassword').then(response => response.json()).then(password => privatePassword.value = password.password);
}

</script>

<template>
    <input type="file" name="file" v-bind:files="fileFromDropArea"><br><br>
    <div id="drop-area" @dragover="preventDefaults" @dragenter="preventDefaults" @dragleave="preventDefaults" @drop="handleDrop">
        Перетащите файл сюда
    </div>
    <br>
    <input type="text" name="description"> Описание файла
    <p>
        Выберите статус файла.
    </p>
    <p>
        <input type="radio" name="viewingStatus" value="public" @click="clickPublic">Публичный<br>
        <input type="radio" name="viewingStatus" value="private" @click="clickPrivate">Приватный
        <div v-bind:style="divStyle">
            <p>Введите пароль (позволяет только просматривать приватный файл): </p>
            <input type="text" name="visibilityPassword" form="form" v-bind:required="requiredPassword" v-bind:value="privatePassword">&nbsp
            <button type="button" @click="generatePassword">Сгенерировать пароль</button>
        </div>
    </p>
    <p>
        Система поддерживает 2 пароля: один позволяет только видеть приватный файл, второй позволяет видеть приватный файл, редактировать его метаданные и удалять файл.
        Пароль, чтобы получать все права над файлом:
        <input type="text" name="modifyPassword" v-bind:value="props.modifyPassword">
    </p>
    <button type="submit">Отправить</button>
</template>

<style scoped>
body {
    margin: 0;
    padding: 20px;
    font-family: sans-serif;
}

#drop-area {
    width: 400px;
    height: 200px;
    text-align: center;
    line-height: 200px;
    border: 2px dashed #ccc;
    cursor: pointer;
}
</style>
