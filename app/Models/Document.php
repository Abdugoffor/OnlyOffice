<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'format',
        'size',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function history()
    {
        return $this->hasMany(DocumentHistory::class, 'document_id');
    }
}
