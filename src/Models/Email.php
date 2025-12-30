<?php

declare(strict_types=1);

namespace EKDSend\Models;

/**
 * Email model
 */
class Email
{
    public readonly string $id;
    public readonly string $status;
    public readonly string $from;
    /** @var array<string> */
    public readonly array $to;
    public readonly string $subject;
    public readonly ?string $html;
    public readonly ?string $text;
    /** @var array<string>|null */
    public readonly ?array $cc;
    /** @var array<string>|null */
    public readonly ?array $bcc;
    public readonly ?string $replyTo;
    /** @var array<string>|null */
    public readonly ?array $tags;
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
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->subject = $data['subject'];
        $this->html = $data['html'] ?? null;
        $this->text = $data['text'] ?? null;
        $this->cc = $data['cc'] ?? null;
        $this->bcc = $data['bcc'] ?? null;
        $this->replyTo = $data['reply_to'] ?? null;
        $this->tags = $data['tags'] ?? null;
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
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'html' => $this->html,
            'text' => $this->text,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'reply_to' => $this->replyTo,
            'tags' => $this->tags,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'sent_at' => $this->sentAt,
            'delivered_at' => $this->deliveredAt,
        ];
    }
}
