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
            <input type="text" name="visibilityPassword" form="form">
        </div>
        </p>
        <button>Отправить</button>
    </form>
    <script>
        let publicRadio = document.getElementById('publicRadio');
        let privateRadio = document.getElementById('privateRadio');
        let div = document.getElementById('visibilityPassword');

        publicRadio.addEventListener('click', function () {
            div.setAttribute('style', 'display: none');
        });
        privateRadio.addEventListener('click', function () {
            div.setAttribute('style', 'display: run-in');
        });
    </script>
</body>
</html>
