<?php

declare(strict_types=1);

namespace EKDSend\Api;

use EKDSend\Http\HttpClient;
use EKDSend\Models\Email;
use EKDSend\Models\PaginatedResponse;

/**
 * Email API
 */
class Emails
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send an email
     *
     * @param array{
     *   from: string,
     *   to: string|array<string>,
     *   subject: string,
     *   html?: string,
     *   text?: string,
     *   cc?: string|array<string>,
     *   bcc?: string|array<string>,
     *   reply_to?: string,
     *   attachments?: array<array{filename: string, content: string, content_type?: string}>,
     *   headers?: array<string, string>,
     *   tags?: array<string>,
     *   metadata?: array<string, string>,
     *   scheduled_at?: string
     * } $params Email parameters
     * @return Email
     */
    public function send(array $params): Email
    {
        // Normalize recipients to arrays
        if (isset($params['to']) && is_string($params['to'])) {
            $params['to'] = [$params['to']];
        }
        if (isset($params['cc']) && is_string($params['cc'])) {
            $params['cc'] = [$params['cc']];
        }
        if (isset($params['bcc']) && is_string($params['bcc'])) {
            $params['bcc'] = [$params['bcc']];
        }

        $response = $this->client->post('/emails', $params);
        return new Email($response['data']);
    }

    /**
     * Get an email by ID
     *
     * @param string $emailId The email ID
     * @return Email
     */
    public function get(string $emailId): Email
    {
        $response = $this->client->get("/emails/{$emailId}");
        return new Email($response['data']);
    }

    /**
     * List emails with pagination and filtering
     *
     * @param array{
     *   limit?: int,
     *   offset?: int,
     *   status?: string,
     *   from_date?: string,
     *   to_date?: string,
     *   tags?: array<string>
     * } $params Query parameters
     * @return PaginatedResponse<Email>
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
        if (isset($params['tags'])) {
            $query['tags'] = implode(',', $params['tags']);
        }

        $response = $this->client->get('/emails', $query);

        return new PaginatedResponse(
            array_map(fn($item) => new Email($item), $response['data']),
            $response['total'],
            $response['limit'],
            $response['offset']
        );
    }

    /**
     * Cancel a scheduled email
     *
     * @param string $emailId The email ID
     * @return Email
     */
    public function cancel(string $emailId): Email
    {
        $response = $this->client->delete("/emails/{$emailId}");
        return new Email($response['data']);
    }
}
