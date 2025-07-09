<?php

/**
 * TCU API Client - Validation Exception
 * 
 * Exception class for validation-related errors in the TCU API Client.
 * Handles validation failures with detailed error information
 * for request data validation and field-level error reporting.
 * 
 * @package    MBLogik\TCUAPIClient\Exceptions
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Exception class for validation errors with detailed error information
 */

namespace MBLogik\TCUAPIClient\Exceptions;

class ValidationException extends TCUAPIException
{
    private array $errors = [];
    
    public function __construct(string $message = "Validation failed", array $errors = [], int $code = 422, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
        $this->errors = $errors;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}