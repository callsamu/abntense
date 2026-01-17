<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TypstConversorService
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

        $template = match ($type) {
            'doc' => "$$",
            'heading' => sprintf("#heading(level: %d)[$$]\n", $attrs['level']),
            'paragraph' => "#par[$$]\n",
        };

        $text_content = "";

        foreach ($contents as $child) {
            $text_content .= Self::convert_node($child);
        }

        return str_replace("$$", $text_content, $template);
    }

    static function pretextual(string $name, Array $nodes, int &$idx) {
        $content = "";

        while (($idx + 1) < count($nodes)) {
            $node = $nodes[$idx + 1];

            if (
                ($idx + 1 >= count($nodes)) ||
                ($node['type'] === 'heading' && $node['content'])
            ) {
                $text = $node['content'][0]['text'];
                if ($text !== $name) break;
            }

            $content .= Self::convert_node($node);
            $idx++;
        }

        return sprintf("#pretextual(\"%s\")[\n%s]\n", $name, $content);
    }

    public function convertTiptap(Array $document): string  {
        $text = "";
        $in_pretext = true;

        if (!isset( $document['content'])) {
            return $text;
        }

        $nodes = $document['content'];

        for ($i = 0; $i < count($nodes); $i++) {
            $node = $nodes[$i];

            if ($in_pretext && $node['type'] === 'heading' && $node['content']) {
                $node_text = $node['content'][0]['text'];

                if (in_array($node_text, $this->pretextual_elements)) {
                    $text .= Self::pretextual($node_text, $nodes, $i);
                } else {
                    $in_pretext = false;
                    $text .= Self::convert_node($node);
                }
            } else {
                $text .= Self::convert_node($node);
            }
        }

        return $text;
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
