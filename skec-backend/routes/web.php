<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminNoteController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/notes/{id}/view', [AdminNoteController::class, 'view'])->name('notes.view');
