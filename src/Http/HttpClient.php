<?php

declare(strict_types=1);

namespace EKDSend\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use EKDSend\Exceptions\EKDSendException;
use EKDSend\Exceptions\AuthenticationException;
use EKDSend\Exceptions\ValidationException;
use EKDSend\Exceptions\RateLimitException;
use EKDSend\Exceptions\NotFoundException;

/**
 * HTTP client for making API requests
 */
class HttpClient
{
    private Client $client;
    private int $maxRetries;
    private bool $debug;

    public function __construct(
        string $apiKey,
        string $baseUrl,
        int $timeout,
        int $maxRetries,
        bool $debug
    ) {
        $this->maxRetries = $maxRetries;
        $this->debug = $debug;

        $this->client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'timeout' => $timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'ekdsend-php/1.1.0',
            ],
        ]);
    }

    /**
     * Make a request to the API
     *
     * @param string $method HTTP method
     * @param string $path API endpoint path
     * @param array<string, mixed> $options Request options
     * @return array<string, mixed> Response data
     * @throws EKDSendException
     */
    public function request(string $method, string $path, array $options = []): array
    {
        $lastException = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            try {
                if ($this->debug) {
                    echo "[EKDSend] {$method} {$path}\n";
                    if (isset($options['json'])) {
                        echo "[EKDSend] Request: " . json_encode($options['json']) . "\n";
                    }
                }

                $response = $this->client->request($method, ltrim($path, '/'), $options);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true) ?? [];

                if ($this->debug) {
                    echo "[EKDSend] Response: {$body}\n";
                }

                return $data;
            } catch (RequestException $e) {
                $response = $e->getResponse();
                $statusCode = $response?->getStatusCode() ?? 500;
                $body = $response?->getBody()->getContents() ?? '';
                $data = json_decode($body, true) ?? [];
                $requestId = $response?->getHeader('x-request-id')[0] ?? null;

                $exception = $this->handleError($statusCode, $data, $requestId);

                // Don't retry auth errors or validation errors
                if ($exception instanceof AuthenticationException || $exception instanceof ValidationException) {
                    throw $exception;
                }

                // Retry rate limits with wait
                if ($exception instanceof RateLimitException && $attempt < $this->maxRetries) {
                    if ($this->debug) {
                        echo "[EKDSend] Rate limited. Waiting {$exception->getRetryAfter()}s...\n";
                    }
                    sleep($exception->getRetryAfter());
                    $lastException = $exception;
                    continue;
                }

                // Retry other errors with exponential backoff
                if ($attempt < $this->maxRetries) {
                    $waitTime = pow(2, $attempt);
                    if ($this->debug) {
                        echo "[EKDSend] Request failed. Retrying in {$waitTime}s...\n";
                    }
                    sleep($waitTime);
                    $lastException = $exception;
                    continue;
                }

                throw $exception;
            } catch (GuzzleException $e) {
                if ($attempt < $this->maxRetries) {
                    $waitTime = pow(2, $attempt);
                    sleep($waitTime);
                    $lastException = new EKDSendException($e->getMessage(), 500);
                    continue;
                }
                throw new EKDSendException($e->getMessage(), 500);
            }
        }

        throw $lastException ?? new EKDSendException('Request failed', 500);
    }

    /**
     * Handle error responses
     *
     * @param int $statusCode HTTP status code
     * @param array<string, mixed> $data Response data
     * @param string|null $requestId Request ID
     * @return EKDSendException
     */
    private function handleError(int $statusCode, array $data, ?string $requestId): EKDSendException
    {
        $error = $data['error'] ?? [];
        $message = $error['message'] ?? 'API request failed';
        $code = $error['code'] ?? 'UNKNOWN_ERROR';

        return match ($statusCode) {
            400 => new ValidationException($message, $error['details'] ?? [], $requestId),
            401 => new AuthenticationException($message, $requestId),
            404 => new NotFoundException($message, $code, $requestId),
            429 => new RateLimitException(
                $message,
                (int) ($error['retry_after'] ?? 60),
                $requestId
            ),
            default => new EKDSendException($message, $statusCode, $code, $requestId),
        };
    }

    /**
     * Make a GET request
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /**
     * Make a POST request
     */
    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    /**
     * Make a DELETE request
     */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }
}
