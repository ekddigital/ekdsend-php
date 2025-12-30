<?php

declare(strict_types=1);

namespace EKDSend;

use EKDSend\Api\Emails;
use EKDSend\Api\Sms;
use EKDSend\Api\Voice;
use EKDSend\Http\HttpClient;

/**
 * EKDSend PHP SDK
 *
 * @package EKDSend
 *
 * @property-read Emails $emails Email API
 * @property-read Sms $sms SMS API
 * @property-read Voice $calls Voice API
 */
class EKDSend
{
    public const VERSION = '1.1.0';
    public const DEFAULT_BASE_URL = 'https://es.ekddigital.com/v1';

    private HttpClient $httpClient;
    private Emails $emails;
    private Sms $sms;
    private Voice $calls;

    /**
     * Create a new EKDSend client
     *
     * @param string $apiKey Your EKDSend API key (ek_live_xxx or ek_test_xxx)
     * @param array{
     *   base_url?: string,
     *   timeout?: int,
     *   max_retries?: int,
     *   debug?: bool
     * } $options Configuration options
     *
     * @throws \InvalidArgumentException If API key is invalid
     */
    public function __construct(string $apiKey, array $options = [])
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API key is required');
        }

        if (!str_starts_with($apiKey, 'ek_live_') && !str_starts_with($apiKey, 'ek_test_')) {
            throw new \InvalidArgumentException(
                "Invalid API key format. Must start with 'ek_live_' or 'ek_test_'"
            );
        }

        $baseUrl = $options['base_url'] ?? self::DEFAULT_BASE_URL;
        $timeout = $options['timeout'] ?? 30;
        $maxRetries = $options['max_retries'] ?? 3;
        $debug = $options['debug'] ?? false;

        $this->httpClient = new HttpClient($apiKey, $baseUrl, $timeout, $maxRetries, $debug);
        $this->emails = new Emails($this->httpClient);
        $this->sms = new Sms($this->httpClient);
        $this->calls = new Voice($this->httpClient);
    }

    /**
     * Get the Email API
     */
    public function emails(): Emails
    {
        return $this->emails;
    }

    /**
     * Get the SMS API
     */
    public function sms(): Sms
    {
        return $this->sms;
    }

    /**
     * Get the Voice API
     */
    public function calls(): Voice
    {
        return $this->calls;
    }

    /**
     * Magic getter for API resources
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'emails' => $this->emails,
            'sms' => $this->sms,
            'calls' => $this->calls,
            default => throw new \InvalidArgumentException("Unknown property: {$name}"),
        };
    }
}
