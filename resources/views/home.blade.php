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
    <form action="/uploadFile" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file">
        <input type="text" name="description">
        <p>
            Выберите статус файла.
        </p>
        <p>
            <input type="radio" name="viewingStatus" value="public">Публичный<br>
            <input type="radio" name="viewingStatus" value="private">Приватный
        </p>
        <button>Отправить</button>
    </form>
</body>
</html>
