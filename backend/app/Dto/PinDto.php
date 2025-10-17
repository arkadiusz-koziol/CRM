<?php

namespace App\Dto;

use Illuminate\Http\UploadedFile;

readonly class PinDto
{
    public function __construct(
        private int $userId,
        private float $x,
        private float $y,
        private ?UploadedFile $photo
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'x' => $this->getX(),
            'y' => $this->getY(),
            'photo' => $this->getPhoto(),
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getPhoto(): ?UploadedFile
    {
        return $this->photo;
    }
}
