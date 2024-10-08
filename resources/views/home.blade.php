@vite(['resources/js/app.js'])
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
    <div id="app">
        <form action="{{ route("files.upload") }}" method="post" enctype="multipart/form-data" id="form">
            @csrf
            <form-vue :modify-password='@json($modifyPassword)'></form-vue>
        </form>
        @if($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</body>
