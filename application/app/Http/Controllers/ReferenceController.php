<?php

namespace App\Http\Controllers;


use App\Exceptions\WebsiteMetadataException;
use App\Services\WebsiteMetadataExtractor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function from_website(Request $request, WebsiteMetadataExtractor $extractor)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:2048'
        ]);

        try {
            $data = $extractor->extract($validated['url']);
            return $data;
        } catch (WebsiteMetadataException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        }
    }
    //
}
