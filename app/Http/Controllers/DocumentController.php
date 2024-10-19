<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
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
