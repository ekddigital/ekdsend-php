<?php

declare(strict_types=1);

namespace EKDSend\Api;

use EKDSend\Http\HttpClient;
use EKDSend\Models\VoiceCall;
use EKDSend\Models\PaginatedResponse;

/**
 * Voice API
 */
class Voice
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a voice call
     *
     * @param array{
     *   to: string,
     *   from: string,
     *   tts_message?: string,
     *   audio_url?: string,
     *   voice?: string,
     *   language?: string,
     *   record?: bool,
     *   machine_detection?: bool,
     *   webhook_url?: string,
     *   metadata?: array<string, string>
     * } $params Call parameters
     * @return VoiceCall
     * @throws \InvalidArgumentException If neither tts_message nor audio_url is provided
     */
    public function create(array $params): VoiceCall
    {
        if (!isset($params['tts_message']) && !isset($params['audio_url'])) {
            throw new \InvalidArgumentException('Either tts_message or audio_url is required');
        }

        // Set defaults
        $params['voice'] = $params['voice'] ?? 'alloy';
        $params['language'] = $params['language'] ?? 'en-US';
        $params['record'] = $params['record'] ?? false;
        $params['machine_detection'] = $params['machine_detection'] ?? false;

        $response = $this->client->post('/calls', $params);
        return new VoiceCall($response['data']);
    }

    /**
     * Get a call by ID
     *
     * @param string $callId The call ID
     * @return VoiceCall
     */
    public function get(string $callId): VoiceCall
    {
        $response = $this->client->get("/calls/{$callId}");
        return new VoiceCall($response['data']);
    }

    /**
     * List calls with pagination and filtering
     *
     * @param array{
     *   limit?: int,
     *   offset?: int,
     *   status?: string,
     *   from_date?: string,
     *   to_date?: string
     * } $params Query parameters
     * @return PaginatedResponse<VoiceCall>
     */
    public function list(array $params = []): PaginatedResponse
    {
        $query = [
            'limit' => $params['limit'] ?? 20,
            'offset' => $params['offset'] ?? 0,
        ];

        if (isset($params['status'])) {
            $query['status'] = $params['status'];
        }
        if (isset($params['from_date'])) {
            $query['from_date'] = $params['from_date'];
        }
        if (isset($params['to_date'])) {
            $query['to_date'] = $params['to_date'];
        }

        $response = $this->client->get('/calls', $query);

        return new PaginatedResponse(
            array_map(fn($item) => new VoiceCall($item), $response['data']),
            $response['total'],
            $response['limit'],
            $response['offset']
        );
    }

    /**
     * Hang up an active call
     *
     * @param string $callId The call ID
     * @return VoiceCall
     */
    public function hangup(string $callId): VoiceCall
    {
        $response = $this->client->delete("/calls/{$callId}");
        return new VoiceCall($response['data']);
    }

    /**
     * Get recording for a call
     *
     * @param string $callId The call ID
     * @return array{url: string, duration: int, created_at: string}
     */
    public function getRecording(string $callId): array
    {
        $response = $this->client->get("/calls/{$callId}/recording");
        return $response['data'];
    }
}
