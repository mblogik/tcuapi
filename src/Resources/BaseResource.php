<?php

/**
 * TCU API Client - Base Resource Class
 *
 * Abstract base class for all resource classes in the TCU API Client.
 * Provides common functionality for making HTTP requests and handling
 * communication with the TCU API endpoints.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Abstract base class for all resource classes with common HTTP functionality
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;

abstract class BaseResource
{
    protected TCUAPIClient $client;

    public function __construct(TCUAPIClient $client)
    {
        $this->client = $client;
    }

    protected function post(string $endpoint, array $data = []): array
    {
        return $this->client->makeRequest($endpoint, $data, 'POST');
    }

    protected function get(string $endpoint, array $data = []): array
    {
        return $this->client->makeRequest($endpoint, $data, 'GET');
    }
}
