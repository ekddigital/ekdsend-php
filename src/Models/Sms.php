<?php

declare(strict_types=1);

namespace EKDSend\Models;

/**
 * SMS model
 */
class Sms
{
    public readonly string $id;
    public readonly string $status;
    public readonly string $to;
    public readonly ?string $from;
    public readonly string $message;
    public readonly int $segments;
    /** @var array<string, string>|null */
    public readonly ?array $metadata;
    public readonly string $createdAt;
    public readonly ?string $sentAt;
    public readonly ?string $deliveredAt;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->status = $data['status'];
        $this->to = $data['to'];
        $this->from = $data['from'] ?? null;
        $this->message = $data['message'];
        $this->segments = $data['segments'] ?? 1;
        $this->metadata = $data['metadata'] ?? null;
        $this->createdAt = $data['created_at'];
        $this->sentAt = $data['sent_at'] ?? null;
        $this->deliveredAt = $data['delivered_at'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'to' => $this->to,
            'from' => $this->from,
            'message' => $this->message,
            'segments' => $this->segments,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'sent_at' => $this->sentAt,
            'delivered_at' => $this->deliveredAt,
        ];
    }
}
