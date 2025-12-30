<?php

declare(strict_types=1);

namespace EKDSend\Models;

/**
 * Recording model for voice call recordings
 */
class Recording
{
    private string $url;
    private int $duration;
    private string $createdAt;

    public function __construct(array $data)
    {
        $this->url = $data['url'] ?? '';
        $this->duration = $data['duration'] ?? 0;
        $this->createdAt = $data['created_at'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'duration' => $this->duration,
            'created_at' => $this->createdAt,
        ];
    }
}
