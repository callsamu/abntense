<?php

namespace App\Services;

use Illuminate\Support\Str;

class TypstService
{
    public function fromTiptap(Array $content)
    {
        function textify($node) {
            [
                'text' => $text,
                'marks' => $marks,
            ] = $node;

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

        function recurse($node) {
            [
                'type' => $type,
                'attrs' => $attrs,
                'content' => $contents
            ] = $node;

            if ($type === 'text') {
                return textify($node);
            }

            $template = match ($type) {
                'doc' => "$$",
                'heading' => sprintf("#heading(level: %d)[$$]\n", $attrs['level']),
                'paragraph' => "#par[$$]\n",
            };

            $text_content = "";

            foreach ($contents as $child) {
                $text_content .= recurse($child);
            }

            return str_replace("$$", $text_content, $template);
        }

        return Str::trim(recurse($content));
    }
}
