<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hujjatni tahrirlash</title>
    <script src="{{ config('services.onlyoffice.url') }}/web-apps/apps/api/documents/api.js"></script>
</head>

<body bgcolor="grey" style="height: 100vh">

    <h1>Hujjatni tahrirlash: {{ $config['document']['title'] }}</h1>
    <div id="editor"></div>
    <script>
        const config = @json($config);

        const editor = new DocsAPI.DocEditor("editor", config);
    </script>
</body>

</html>
