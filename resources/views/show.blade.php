<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $file->getOriginalName() }}</title>
</head>
<body>
    <h1>Файл - {{ $file->getOriginalName() }}</h1>
    <p>Рзамер: {{ $file->getSize() }}</p>
    <p>Дата загрузки: {{ $file->getUploadDate() }}</p>
    <p>Описание: {{ $file->getDescription() }}</p>
    <p>Статус проверки на virus total: {{ $file->getSecurityStatus()->value }}</p>
    <a href="{{ $downloadLink }}">Скачать файл</a>
</body>
</html>
