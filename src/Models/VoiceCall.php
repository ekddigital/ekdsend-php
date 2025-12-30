<?php

declare(strict_types=1);

namespace EKDSend\Models;

/**
 * Voice call model
 */
class VoiceCall
{
    public readonly string $id;
    public readonly string $status;
    public readonly string $to;
    public readonly string $from;
    public readonly ?string $ttsMessage;
    public readonly ?string $audioUrl;
    public readonly string $voice;
    public readonly string $language;
    public readonly bool $record;
    public readonly bool $machineDetection;
    public readonly ?int $duration;
    public readonly ?string $recordingUrl;
    /** @var array<string, string>|null */
    public readonly ?array $metadata;
    public readonly string $createdAt;
    public readonly ?string $answeredAt;
    public readonly ?string $endedAt;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->status = $data['status'];
        $this->to = $data['to'];
        $this->from = $data['from'];
        $this->ttsMessage = $data['tts_message'] ?? null;
        $this->audioUrl = $data['audio_url'] ?? null;
        $this->voice = $data['voice'] ?? 'alloy';
        $this->language = $data['language'] ?? 'en-US';
        $this->record = $data['record'] ?? false;
        $this->machineDetection = $data['machine_detection'] ?? false;
        $this->duration = $data['duration'] ?? null;
        $this->recordingUrl = $data['recording_url'] ?? null;
        $this->metadata = $data['metadata'] ?? null;
        $this->createdAt = $data['created_at'];
        $this->answeredAt = $data['answered_at'] ?? null;
        $this->endedAt = $data['ended_at'] ?? null;
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
            'tts_message' => $this->ttsMessage,
            'audio_url' => $this->audioUrl,
            'voice' => $this->voice,
            'language' => $this->language,
            'record' => $this->record,
            'machine_detection' => $this->machineDetection,
            'duration' => $this->duration,
            'recording_url' => $this->recordingUrl,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'answered_at' => $this->answeredAt,
            'ended_at' => $this->endedAt,
        ];
    }
}
