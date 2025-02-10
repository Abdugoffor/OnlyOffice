<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\DocumentHistory;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        $this->logHistory($document, 'created');
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        $this->logHistory($document, 'updated');
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        //
    }
    private function logHistory(Document $document, $action)
    {
        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => auth()->user()->id,
            'path' => $document->path,
            'action' => $action,
        ]);
    }
}
