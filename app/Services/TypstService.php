<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Self_;

class TypstService
{
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

    private static function convert_node($node) {
        $type = $node['type'];

        if ($type === 'text') {
            return Self::textify_node($node);
        }

        $attrs = $node['attrs'] ?? [];
        $contents = $node['content'];

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

    public function fromTiptap(Array $content)
    {
        $document = Self::convert_node($content);
        return Str::trim($document);
    }

    public function compile($id, $document) {
        $typ_file = $id . ".typ";
        Storage::put($typ_file, $document);
        $path = Storage::path($typ_file);

        $command = sprintf("typst compile --format pdf %s -", $path, $id);
        $result = Process::run($command);

        if ($result->failed()) {
            throw new \Exception(
                "Unable to compile document: " .
                $result->errorOutput()
            );
        }

        $pdf_file= $id . ".pdf";
        Storage::put($pdf_file, $result->output());
        $pdf_path = Storage::path($pdf_file);

        return $pdf_path;
    }
}
