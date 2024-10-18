<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Models\Document;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', function () {
       $id = Auth::id();
       $documents = Document::with('users')
           ->find($id)
           ->get();

       $docs = $documents
           ->map(fn ($document) => [
               'title' => $document->title,
               'users' => $document
                   ->users
                   ->pluck('name')
                   ->map(fn ($name) => ['name' => $name]),
               'updated_at' => $document->updated_at->diffForHumans(),
           ]);

        return Inertia::render('Dashboard', [
            'documents' => $docs,
        ]);
    })->name('dashboard');

    Route::get('/document/{id}', [DocumentController::class, 'edit'])
        ->name('document.edit');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
