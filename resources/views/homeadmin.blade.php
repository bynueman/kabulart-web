<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/homeadmin.css">
    <title>Document</title>
</head>
<body>
    <div class="sidebar">
        <a class="active" href="/">Home</a>
        <a href="{{ route('postsinformasi.index') }}">ADD INFORMASI</a>
        <a href="{{ route('postsgalery.index') }}">ADD GALLERY</a>
        <a href="{{ route('posttestimoni.index') }}">ADD TESTIMONI</a>
        <a class="btn" href="/logout">LOGOUT</a>
    </div>

    <div class="content">
        <h2>JIKA INGIN DITAMBAHKAN FITUR UNTUK ADMIN</h2>
    </div>
</body>
</html>