<?php

declare(strict_types=1);

namespace EKDSend\Api;

use EKDSend\Http\HttpClient;
use EKDSend\Models\Sms as SmsModel;
use EKDSend\Models\PaginatedResponse;

/**
 * SMS API
 */
class Sms
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send an SMS message
     *
     * @param array{
     *   to: string,
     *   message: string,
     *   from?: string,
     *   scheduled_at?: string,
     *   webhook_url?: string,
     *   metadata?: array<string, string>
     * } $params SMS parameters
     * @return SmsModel
     */
    public function send(array $params): SmsModel
    {
        $response = $this->client->post('/sms', $params);
        return new SmsModel($response['data']);
    }

    /**
     * Get an SMS by ID
     *
     * @param string $smsId The SMS ID
     * @return SmsModel
     */
    public function get(string $smsId): SmsModel
    {
        $response = $this->client->get("/sms/{$smsId}");
        return new SmsModel($response['data']);
    }

    /**
     * List SMS messages with pagination and filtering
     *
     * @param array{
     *   limit?: int,
     *   offset?: int,
     *   status?: string,
     *   from_date?: string,
     *   to_date?: string
     * } $params Query parameters
     * @return PaginatedResponse<SmsModel>
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

        $response = $this->client->get('/sms', $query);

        return new PaginatedResponse(
            array_map(fn($item) => new SmsModel($item), $response['data']),
            $response['total'],
            $response['limit'],
            $response['offset']
        );
    }

    /**
     * Cancel a scheduled SMS
     *
     * @param string $smsId The SMS ID
     * @return SmsModel
     */
    public function cancel(string $smsId): SmsModel
    {
        $response = $this->client->delete("/sms/{$smsId}");
        return new SmsModel($response['data']);
    }
}
