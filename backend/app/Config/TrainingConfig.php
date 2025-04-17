<?php

declare(strict_types=1);

namespace App\Config;

readonly class TrainingConfig
{
    public function __construct(private array $config)
    {
    }

    public function getFilesPath(): string
    {
        return $this->config['files_path'];
    }
}
