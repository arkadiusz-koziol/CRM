<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function store(UploadedFile $file, string $path): string
    {
        return $file->store($path);
    }

    public function delete(string $filePath): bool
    {
        return Storage::delete($filePath);
    }
}
