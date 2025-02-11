<?php

namespace App\Repositorys;

use App\Models\Document;

class DocumentRepository
{
    public function create(array $data)
    {
        return Document::create($data);
    }

    public function findById($id)
    {
        return Document::findOrFail($id);
    }

    public function delete(Document $document)
    {
        $document->delete();
    }

    public function update(Document $document, array $data)
    {
        $document->update($data);
    }
}
