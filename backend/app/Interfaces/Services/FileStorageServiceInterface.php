<?php

declare(strict_types=1);

namespace App\Interfaces\Services;

use Illuminate\Http\UploadedFile;

interface FileStorageServiceInterface
{
    public function store(UploadedFile $file, string $path): string;

    public function delete(string $filePath): bool;

    public function exists(string $filePath): bool;
}
