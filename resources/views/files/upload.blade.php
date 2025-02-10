<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fayl Yuklash</title>
</head>

<body>
    <h1>Fayl Yuklash</h1>
    <form action="/files/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Yuklash</button>
    </form>
</body>

</html>
