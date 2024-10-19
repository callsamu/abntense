<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required',
        ]);


        $document = Document::factory()->createOne($data);
        $document->users()->attach(Auth::id(), ['role' => Document::ROLE_OWNER]);

        Log::info('Document created', ['document' => $document]);
        return to_route('document.edit', [$document]);
    }

    public function edit(string $id): Response
    {
        $document = Document::findOrFail($id);

        return Inertia::render('Document/EditorView', [
            'document' => [
                'title' => $document->title,
                'content' => $document->content,
            ],
        ]);
    }
}
