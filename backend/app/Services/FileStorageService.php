<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Services\FileStorageServiceInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;

final class FileStorageService implements FileStorageServiceInterface
{
    public function __construct(
        private Filesystem $filesystem
    ) {}

    public function store(UploadedFile $file, string $path): string
    {
        return $file->store($path, 'public');
    }

    public function delete(string $filePath): bool
    {
        return $this->filesystem->delete($filePath);
    }

    public function exists(string $filePath): bool
    {
        return $this->filesystem->exists($filePath);
    }
}
