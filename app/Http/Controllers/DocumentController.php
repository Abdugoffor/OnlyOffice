<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\TelegramServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Log;
use Str;

class DocumentController extends Controller
{
    protected $telegramService;
    public function __construct(TelegramServices $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function createDocuments()
    {
        return view('documents.create-documents');
    }
    public function documents()
    {
        if (auth()->user()->role == 'admin') {
            $models = Document::all();
            return view('documents.documents', ['models' => $models]);
        }

        $models = Document::where('user_id', auth()->user()->id)->get();

        return view('documents.documents', ['models' => $models]);
    }

    public function storeDocuments(Request $request)
    {
        try {
            $request->validate([
                'document' => 'required|mimes:doc,docx,xls,xlsx',
            ]);

            $file = $request->file('document');
            $name = $file->getClientOriginalName();
            $path = $file->storeAs('uploded', time() . Str::random(40) . '.' . $file->getClientOriginalExtension(), 'public');

            Document::create([
                'name' => $name,
                'path' => $path,
                'format' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'user_id' => auth()->user()->id,
            ]);
            return redirect()->route('documents');
        } catch (\Throwable $th) {
            $this->telegramService->send($th->getMessage());
        }

    }

    public function editDocument($id)
    {
        try {
            $document = Document::findOrFail($id);

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
                    'method' => 'POST',
                    'callbackUrl' => route('documents.callback', ['id' => $document->id]),

                ],
            ];
            return view('documents.edit', compact('config'));
        } catch (\Throwable $th) {
            $this->telegramService->send($th->getMessage());
        }

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

    public function callback(Request $request, $id)
    {
        try {
            $data = $request->all();
            $document = Document::findOrFail($id);

            Log::info($data);

            if (isset($data['status']) && $data['status'] == 2) {
                $fileUrl = $data['url'];

                if ($fileUrl) {
                    $fileContent = Http::get($fileUrl)->body();

                    $name = $document->name;
                    $newPath = time() . Str::random(40) . '.' . pathinfo($document->path, PATHINFO_EXTENSION);

                    Storage::disk('public')->put('uploded/' . $newPath, $fileContent);

                    Storage::disk('public')->delete('uploded/' . $document->path);

                    $document->update([
                        'path' => 'uploded/' . $newPath,
                        'size' => strlen($fileContent),
                        'format' => pathinfo($newPath, PATHINFO_EXTENSION),
                    ]);

                    Log::info("Fayl yangilandi: " . $newPath);
                }
            }

            return response()->json(['error' => 0]);

        } catch (\Throwable $th) {
            $this->telegramService->send($th->getMessage());
        }
    }
}

