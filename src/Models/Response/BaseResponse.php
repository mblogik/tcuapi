<?php

/**
 * TCU API Client - Base Response Model
 * 
 * Abstract base class for all API response models in the TCU API Client.
 * Provides common functionality for handling API responses including status codes,
 * messages, data extraction, and error handling.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Abstract base class for all API response models with common functionality
 */

namespace MBLogik\TCUAPIClient\Models\Response;

use MBLogik\TCUAPIClient\Models\BaseModel;
use MBLogik\TCUAPIClient\Enums\ResponseCode;

abstract class BaseResponse extends BaseModel
{
    protected array $fillable = [
        'status_code',
        'status_description',
        'message',
        'data',
        'errors',
        'timestamp'
    ];
    
    public function isSuccess(): bool
    {
        $statusCode = $this->get('status_code');
        return $statusCode >= 200 && $statusCode < 300;
    }
    
    public function isError(): bool
    {
        return !$this->isSuccess();
    }
    
    public function getStatusCode(): ?int
    {
        return $this->get('status_code');
    }
    
    public function getStatusDescription(): ?string
    {
        return $this->get('status_description');
    }
    
    public function getMessage(): ?string
    {
        return $this->get('message');
    }
    
    public function getData(): mixed
    {
        return $this->get('data');
    }
    
    public function getErrors(): array
    {
        return $this->get('errors', []);
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->getErrors());
    }
    
    public function getTimestamp(): ?string
    {
        return $this->get('timestamp');
    }
}