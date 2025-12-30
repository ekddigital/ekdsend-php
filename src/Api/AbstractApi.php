<?php

declare(strict_types=1);

namespace EKDSend\Api;

use EKDSend\Http\HttpClient;
use EKDSend\Models\PaginatedResponse;

/**
 * Abstract base class for API resources
 * 
 * Provides common functionality for all API endpoints following DRY principle.
 */
abstract class AbstractApi
{
    protected HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the API endpoint path (e.g., '/emails', '/sms', '/calls')
     */
    abstract protected function getEndpoint(): string;

    /**
     * Create a model instance from response data
     */
    abstract protected function createModel(array $data): object;

    /**
     * Build query parameters for list requests
     *
     * @param array $params User-provided parameters
     * @return array Query parameters with defaults
     */
    protected function buildListQuery(array $params): array
    {
        $query = [
            'limit' => $params['limit'] ?? 20,
            'offset' => $params['offset'] ?? 0,
        ];

        // Add common filter parameters
        $filterKeys = ['status', 'from_date', 'to_date'];
        foreach ($filterKeys as $key) {
            if (isset($params[$key])) {
                $query[$key] = $params[$key];
            }
        }

        // Handle tags array
        if (isset($params['tags']) && is_array($params['tags'])) {
            $query['tags'] = implode(',', $params['tags']);
        }

        return $query;
    }

    /**
     * Get a single resource by ID
     *
     * @param string $id Resource ID
     * @return object Model instance
     */
    public function get(string $id): object
    {
        $response = $this->client->get("{$this->getEndpoint()}/{$id}");
        return $this->createModel($response['data']);
    }

    /**
     * List resources with pagination and filtering
     *
     * @param array $params Query parameters
     * @return PaginatedResponse
     */
    public function list(array $params = []): PaginatedResponse
    {
        $query = $this->buildListQuery($params);
        $response = $this->client->get($this->getEndpoint(), $query);

        return new PaginatedResponse(
            array_map(fn($item) => $this->createModel($item), $response['data']),
            $response['total'],
            $response['limit'],
            $response['offset']
        );
    }

    /**
     * Cancel/delete a resource by ID
     *
     * @param string $id Resource ID
     * @return object Model instance
     */
    public function cancel(string $id): object
    {
        $response = $this->client->delete("{$this->getEndpoint()}/{$id}");
        return $this->createModel($response['data']);
    }
}
