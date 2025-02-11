<?php

namespace App\Services;
use App\Repositorys\DocumentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class DocumentService
{
    public $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function storeDocument($file, $userId)
    {
        $name = $file->getClientOriginalName();

        $path = $file->storeAs('uploaded', time() . Str::random(40) . '.' . $file->getClientOriginalExtension(), 'public');

        return $this->documentRepository->create([
            'name' => $name,
            'path' => $path,
            'format' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'user_id' => $userId,
        ]);
    }

    public function editDocument($document)
    {
        $config = [
            'document' => [
                'fileType' => pathinfo($document->path, PATHINFO_EXTENSION),
                'key' => md5($document->id . time()),
                'title' => $document->name,
                'url' => asset('storage/' . $document->path),
            ],
            'documentType' => $this->getDocumentType($document->path),
            'editorConfig' => [
                'mode' => 'edit',
                'callbackUrl' => route('documents.callback', ['id' => $document->id]),
            ],
        ];

        return $config;
    }

    public function newDocument(string $fileType, $userId)
    {
        $allowedTypes = ['docx', 'xlsx', 'pptx'];

        if (!in_array($fileType, $allowedTypes)) {
            throw new \Exception('Noto‘g‘ri fayl turi!');
        }

        $sourcePath = public_path("document.$fileType");

        if (!file_exists($sourcePath)) {
            throw new \Exception("document.$fileType fayli topilmadi!");
        }

        $fileName = "document.$fileType";
        $newFileName = time() . Str::random(40) . ".$fileType";
        $newPath = "uploaded/$newFileName";

        copy($sourcePath, public_path("storage/$newPath"));

        return $this->documentRepository->create([
            'name' => $fileName,
            'path' => $newPath,
            'format' => $fileType,
            'size' => filesize($sourcePath),
            'user_id' => $userId,
        ]);
    }

    public function callback($document, $data)
    {
        if (isset($data['status']) && $data['status'] == 2) {

            $fileUrl = $data['url'];

            if ($fileUrl) {

                $fileContent = Http::get($fileUrl)->body();

                $newPath = time() . Str::random(40) . '.' . pathinfo($document->path, PATHINFO_EXTENSION);

                Storage::disk('public')->put('uploaded/' . $newPath, $fileContent);

                Storage::disk('public')->delete('uploaded/' . $document->path);

                $this->documentRepository->update($document, [
                    'path' => 'uploaded/' . $newPath,
                    'size' => strlen($fileContent),
                    'format' => pathinfo($newPath, PATHINFO_EXTENSION),
                ]);
            }
        }
    }

    public function deleteDocument($document)
    {
        $this->documentRepository->delete($document);
    }

    private function getDocumentType($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        switch ($extension) {
            case 'docx':
            case 'doc':
                return 'word';
            case 'xlsx':
            case 'xls':
                return 'spreadsheet';
            case 'pptx':
            case 'ppt':
                return 'presentation';
            default:
                return 'word';
        }
    }
}
