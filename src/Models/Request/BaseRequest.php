<?php

/**
 * TCU API Client - Base Request Model
 * 
 * Abstract base class for all API request models in the TCU API Client.
 * Provides common functionality for preparing request data, handling endpoints,
 * HTTP methods, and data serialization for API calls.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Request
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Abstract base class for all API request models with common functionality
 */

namespace MBLogik\TCUAPIClient\Models\Request;

use MBLogik\TCUAPIClient\Models\BaseModel;

abstract class BaseRequest extends BaseModel
{
    protected string $endpoint;
    protected string $method = 'POST';
    
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getRequestData(): array
    {
        return $this->toArray();
    }
    
    public function prepareForApi(): array
    {
        $data = $this->getRequestData();
        
        // Remove null values
        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }
}