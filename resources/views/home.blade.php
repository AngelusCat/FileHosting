<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Файлообменник - uppu</title>
</head>
<body>
    <form action="/uploadFile" method="post" enctype="multipart/form-data" id="form">
        @csrf
        <input type="file" name="file">
        <input type="text" name="description">
        <p>
            Выберите статус файла.
        </p>
        <p>
            <input type="radio" name="viewingStatus" value="public" id="publicRadio">Публичный<br>
            <input type="radio" name="viewingStatus" value="private" id="privateRadio">Приватный
        <div style="display: none" id="visibilityPassword">
            <p>Введите пароль: </p>
            <input type="text" name="visibilityPassword" form="form" id="passwordInput">
            <button type="button" id="generatePassword">Сгенерировать пароль</button>
        </div>
        </p>
        <button type="submit">Отправить</button>
    </form>
    <script>
        let publicRadio = document.getElementById('publicRadio');
        let privateRadio = document.getElementById('privateRadio');
        let div = document.getElementById('visibilityPassword');
        let generatePassword = document.getElementById('generatePassword');
        let passwordInput = document.getElementById('passwordInput');

        publicRadio.addEventListener('click', function () {
            div.setAttribute('style', 'display: none');
        });
        privateRadio.addEventListener('click', function () {
            div.setAttribute('style', 'display: run-in');
        });
        generatePassword.addEventListener('click', function () {
            fetch('/generatePassword').then(response => response.json()).then(password => passwordInput.setAttribute('value', password.password));
        });
    </script>
</body>
</html>
