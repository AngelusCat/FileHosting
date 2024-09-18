<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<h1>Введите пароль, чтобы просматривать приватный файл:</h1>
<form action="{{ route("viewingPassword.checkPassword", ["file" => $fileId]) }}" method="post">
    @csrf
    <input type="password" name="passwordW" required>
    <input type="submit" value="Отправить">
</form>
</body>
</html>
