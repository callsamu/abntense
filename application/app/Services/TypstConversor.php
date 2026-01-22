<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TypstConversor
{
    private $pretextual_elements = [
        'Agradecimentos',
        'Resumo'
    ];

    private static function textify_node($node) {
        ['text' => $text] = $node;
        $marks = $node['marks'] ?? [];

        $open = "";

        foreach ($marks as $mark) {
            $open .= match ($mark['type']) {
                'bold' => "*",
                'italic' => "_",
            };
       }

        $close = Str::reverse($open);

        return $open . $text . $close;
    }

    static function convert_node($node) {
        $type = $node['type'];

        if ($type === 'text') {
            return Self::textify_node($node);
        }

        $attrs = $node['attrs'] ?? [];
        $contents = $node['content'] ?? [];
        if (empty($contents)) {
            return "";
        }

        $text_contents = array_map(Self::convert_node(...), $contents);
        $text = array_reduce($text_contents, fn ($a, $b) => $a . $b, '');

        return match ($type) {
            'heading' => "#heading(level: {$attrs['level']})[$text]\n",
            'footnote' => "#footnote[$text]\n",
            'paragraph' => "#par[$text]\n",
            'pretextual_element' => (function() use ($text_contents) {
                [$summary, $contents] = $text_contents;
                return "#pretextual(\"$summary\")[\n$contents]\n";
            })(),
            default => $text,
        };
    }

    public function convertTiptap(Array $document): string  {
        $text = "";

        if (!isset($document['content'])) {
            return $text;
        }

        return TypstConversor::convert_node($document);
    }
    public function convert(Document $document) {
        $title = $document->title;
        $metadata = $document->metadata;

        $authors = $metadata['authors'] ?? [];
        if ($authors) {
            $authors = implode(", ", $authors);
        } else {
            $authors = $document->users->first()->name;
        }

        $content = $this->convertTiptap($document->content);

        return view('abnt', [
            'title' => $title,
            'authors' => $authors,
            'local' => $metadata['local'] ?? "",
            'institution' => $metadata['institution'] ?? "",
            'description' => $metadata['description'] ?? "",
            'year' => $metadata['year'] ?? "",
            'location' => $metadata['location'] ?? "",
            'content' => $content,
        ])->render();
    }
}
