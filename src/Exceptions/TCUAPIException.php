<?php

/**
 * TCU API Client - Base TCU API Exception
 * 
 * Base exception class for all TCU API-related exceptions.
 * Provides common functionality for handling API errors including
 * context information and enhanced error reporting capabilities.
 * 
 * @package    MBLogik\TCUAPIClient\Exceptions
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Base exception class for all TCU API-related errors with context support
 */

namespace MBLogik\TCUAPIClient\Exceptions;

use Exception;
use Throwable;

class TCUAPIException extends Exception
{
    protected array $context = [];
    
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    
    public function getContext(): array
    {
        return $this->context;
    }
    
    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}