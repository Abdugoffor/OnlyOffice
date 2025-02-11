<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use App\Services\TelegramServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Log;
use Str;

class DocumentController extends Controller
{
    protected $documentService;
    protected $telegramService;

    public function __construct(DocumentService $documentService, TelegramServices $telegramService)
    {
        $this->documentService = $documentService;

        $this->telegramService = $telegramService;
    }
    public function createDocuments()
    {
        return view('documents.create-documents');
    }
    public function documents()
    {
        if (auth()->user()->role == 'admin') {

            $models = Document::paginate(10);

            return view('documents.documents', ['models' => $models]);
        }

        $models = Document::where('user_id', auth()->user()->id)->paginate(10);

        return view('documents.documents', ['models' => $models]);
    }

    public function storeDocuments(Request $request)
    {
        try {

            $request->validate([
                'document' => 'required|mimes:doc,docx,xls,xlsx',
            ]);

            $this->documentService->storeDocument($request->file('document'), auth()->user()->id);

            return redirect()->route('documents');

        } catch (\Throwable $th) {

            $this->telegramService->send($th->getMessage());

            return back()->withErrors('Xatolik yuz berdi');
        }
    }

    public function editDocument($id)
    {
        try {

            $document = Document::findOrFail($id);

            $config = $this->documentService->editDocument($document);

            return view('documents.edit', compact('config'));
        } catch (\Throwable $th) {

            $this->telegramService->send($th->getMessage());
        }
    }

    public function newDocument(string $fileType)
    {
        try {
            $allowedTypes = ['docx', 'xlsx', 'pptx'];

            if (!in_array($fileType, $allowedTypes)) {
                return response()->json(['error' => 'Noto‘g‘ri fayl turi!'], 400);
            }

            $sourcePath = public_path("document.$fileType");

            if (!file_exists($sourcePath)) {
                return response()->json(['error' => "document.$fileType fayli topilmadi!"], 404);
            }

            $newFileName = time() . Str::random(40) . ".$fileType";

            $newPath = public_path("storage/uploded/$newFileName");

            $targetDirectory = public_path('storage/uploded');

            if (!is_dir($targetDirectory)) {

                mkdir($targetDirectory, 0755, true);
            }

            $fileCopied = copy($sourcePath, $newPath);

            if ($fileCopied) {
                $document = Document::create([
                    'name' => "document.$fileType",
                    'path' => "uploded/$newFileName",
                    'format' => $fileType,
                    'size' => filesize($sourcePath),
                    'user_id' => auth()->user()->id,
                ]);

                $config = $this->documentService->editDocument($document);

                return view('documents.edit', compact('config'));
            } else {

                $errorMessage = "Fayl nusxalanmadi: $sourcePath -> $newPath";

                $this->telegramService->send($errorMessage);

                abort(500);
            }

        } catch (\Throwable $th) {

            $this->telegramService->send($th->getMessage());

            abort(500);
        }
    }


    public function callback(Request $request, $id)
    {
        try {
            $data = $request->all();

            $document = Document::findOrFail($id);

            $this->documentService->callback($document, $data);

            return response()->json(['error' => 0]);

        } catch (\Throwable $th) {

            $this->telegramService->send($th->getMessage());
        }
    }

    public function deleteDocument(Document $document)
    {
        try {

            $this->documentService->deleteDocument($document);

            return redirect()->route('documents');

        } catch (\Throwable $th) {

            $this->telegramService->send($th->getMessage());
        }
    }
}