<html style="height: 100%;">

<head>
    <title>Open DOCX for Editing</title>
</head>

<body style="height: 100%; margin: 0;">
    <div id="placeholder" style="height: 100%"></div>
    <script type="text/javascript" src="{{ config('services.onlyoffice.url') }}/web-apps/apps/api/documents/api.js">
    </script>
    <script type="text/javascript">
        window.docEditor = new DocsAPI.DocEditor("placeholder", {
            "document": {
                "fileType": "{{ strtolower(pathinfo($document->name, PATHINFO_EXTENSION)) }}",
                "key": "{{ $document->id }}",
                "title": "{{ $document->name }}",
                "url": "{{ asset('storage/' . $document->path) }}"
            },
            "documentType": "word",
            "editorConfig": {
                "callbackUrl": "{{ route('documents.callback', $document) }}"
            },
            "height": "100%",
            "width": "100%"
        });
    </script>
</body>

</html>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ONLYOFFICE Document Editor Code Sample</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body {
            height: 100vh;
            margin: 0
        }
    </style>
    <script src="https://api.docs.onlyoffice.com/web-apps/apps/api/documents/api.js"></script>
</head>

<body>
    <div id="placeholder"></div>
    <script type="module">
        const config = {
            document: {
                fileType: "docx",
                key: "{{ $document->id }}",
                title: "{{ $document->name }}",
                url: "{{ asset('storage/'.$document->path) }}",
                token: "{{ $jwtToken }}" // JWT tokenini qo'shamiz
            },
            documentType: "word",
            editorConfig: {
                callbackUrl: "{{ url('/documents/callback') }}",
                customization: {
                    anonymous: {
                        request: false,
                        label: "Guest"
                    },
                    integrationMode: "embed"
                }
            },
            height: "700px",
            width: "100%"
        }
        const editor = new DocsAPI.DocEditor("placeholder", config)
    </script>
</body>

</html>
