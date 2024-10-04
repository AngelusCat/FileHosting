<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<h1>Введите пароль:</h1>
<form action="{{ route("checkPassword", ["file" => $fileId]) }}" method="post">
    @csrf
    <input type="password" name="password">
    @if($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <input type="submit" value="Отправить">
</form>
</body>
</html>
