<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Models\Document;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
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
               'id' => $document->id,
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

    Route::prefix("documents")->group(function () {
        Route::get('/{id}', [DocumentController::class, 'edit'])
            ->whereNumber('id')
            ->name('document.edit');

        Route::inertia('/create', 'Document/Create')
            ->name('document.create');

        Route::post('/create', [DocumentController::class, 'create'])
            ->name('document.create');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
