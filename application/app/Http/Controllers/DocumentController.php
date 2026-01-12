<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\TypstService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required',
            'metadata' => 'required|array',
        ]);

        $document = Document::factory()->createOne($data);
        $document->users()->attach(Auth::id(), ['role' => Document::ROLE_OWNER]);

        Log::info('Document created', ['document' => $document]);
        return to_route('document.edit', [$document]);
    }

    public function edit(string $id)
    {
        $document = Document::findOrFail($id);

        return Inertia::render('Document/EditorView', [
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'content' => $document->content,
                    'metadata' => $document->metadata
            ],
        ]);
    }

    public function update(string $id, Request $request)
    {
        $document = Document::findOrFail($id);

        if ($request->has('title')) {
            $document->title = $request->input('title');
        }

        if ($request->has('metadata')) {
            $document->metadata = $request->input('metadata');
        }

        if ($request->has('content')) {
            $document->content = $request->input('content');
        }

        $document->save();

        return $document;
    }

    public function compile(string $id, TypstService $typst) {
        $document = Document::findOrFail($id);
        $typst_content = $typst->fromTiptap($document->content);

        try {
            $pdf = $typst->compile($document, $typst_content);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->file($pdf);
    }
}
