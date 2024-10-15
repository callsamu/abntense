<?php

use App\Http\Controllers\ProfileController;
use App\Models\Document;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
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

Route::get('/dashboard', function () {
    $id = Auth::id();

    $documents = Document::with('users')
        ->find($id)
        ->get()
        ->map(fn ($document) => [
            'title' => $document->title,
            'collaborators' => $document->users->pluck('name')->toArray(),
            'updated_at' => $document->updated_at->diffForHumans(),
        ]);

    return Inertia::render('Dashboard', [
        'documents' => $documents
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
