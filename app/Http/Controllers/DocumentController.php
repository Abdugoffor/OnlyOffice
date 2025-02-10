<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;
use Str;

class DocumentController extends Controller
{
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
    }

    public function editDocument($id)
    {
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
                'callbackUrl' => route('documents.callback', ['id' => $document->id]),  
            ],
        ];
        return view('documents.edit', compact('config'));

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
                return 'word'; // Default qiymat
        }
    }

    public function callback(Request $request, $id)
    {
        $data = $request->all();
        $document = Document::findOrFail($id);

        if (isset($data['status']) && $data['status'] == 2) {
            $fileUrl = $data['url'];
            if ($fileUrl) {
                $fileContent = Http::get($fileUrl)->body();
                Log::info($fileContent);
                // Storage::disk('public')->put($document->path, $fileContent);
            }
        }

        return response()->json(['error' => 0]);
    }


}

