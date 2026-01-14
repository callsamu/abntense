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
       $documents = Document::find($id)
         ->orderBy('updated_at', 'desc')
         ->get();

       $docs = $documents
           ->map(fn ($document) => [
               'id' => $document->id,
               'title' => $document->title,
               'users' => $document->users->map(fn ($user) => $user->name),
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

        Route::patch('/{id}', [DocumentController::class, 'update'])
            ->whereNumber('id')
            ->name('document.update');

        Route::post('/{id}/references', [DocumentController::class, 'addReference'])
            ->whereNumber('id')
            ->name('document.references');

        Route::get('/compile/{id}', [DocumentController::class, 'compile'])
            ->whereNumber('id')
            ->name('document.compile');
    });

    Route::prefix('references')->group(function () {
        Route::get('/website', [\App\Http\Controllers\ReferenceController::class, 'from_website']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
