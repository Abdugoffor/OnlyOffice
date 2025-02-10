<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hujjatni tahrirlash</title>
    {{-- <script src="http://45.92.173.139:8089/web-apps/apps/api/documents/api.js"></script> --}}
    {{-- <script src="http://localhost/web-apps/apps/api/documents/api.js"></script> --}}
    <script src="https://d744-45-92-173-139.ngrok-free.app/web-apps/apps/api/documents/api.js"></script>
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
