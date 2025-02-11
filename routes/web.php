<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OnlyOfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/create-documents', [DocumentController::class, 'createDocuments'])->name('create.documents');
    Route::get('/documents', [DocumentController::class, 'documents'])->name('documents');


    Route::post('/store-documents', [DocumentController::class, 'storeDocuments'])->name('store.documents');
    Route::get('/documents-edit/{id}', [DocumentController::class, 'editDocument'])->name('documents.edit');
    Route::post('/documents/callback/{id}', [DocumentController::class, 'callback'])->name('documents.callback');
    Route::get('/new-document/{document}', [DocumentController::class, 'newDocument'])->name('new.document');
    Route::delete('/delete-document/{document}', [DocumentController::class, 'deleteDocument'])->name('delete.document');

});

require __DIR__ . '/auth.php';
