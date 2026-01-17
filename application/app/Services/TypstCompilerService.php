<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TypstCompilerService
{
    public function __construct()
    {
    }

    public function makeTempDirectory() {
        $typst_base = is_writable('/dev/shm') ?
            '/dev/shm/typst' :
            storage_path('app/typst');

        if (!is_dir(($typst_base))) {
            mkdir($typst_base, 0755, true);
        }

        $hash = bin2hex(random_bytes(16));
        $document_base = $typst_base . '/' . $hash;
        mkdir($document_base, 0700, true);

        return $document_base;
    }

    public function cleanupDirectory(string $dir) {
        if (!is_dir($dir)) return;

        $files = glob("$dir/*");
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }

        rmdir($dir);
    }

    /**
     * @throws \Exception
     */
    public function compile(string $typst)
    {
        $dir = $this->makeTempDirectory();

        $input_path = "$dir/main.typ";
        file_put_contents($input_path, $typst);

        try {
            Process::path($dir)->run([
                'typst',
                'compile',
                $input_path,
            ])->throw();

            return file_get_contents("$dir/main.pdf");
        } catch (ProcessFailedException $e) {
            Log::error("Typst Compilation Failed", [
                'stderr' => $e->getMessage(),
            ]);

            throw new \Exception("Typst Compilation Failed: " . $e->getMessage());
        } finally  {
            $this->cleanupDirectory($dir);
        }
    }
}
