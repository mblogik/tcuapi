<?php

/**
 * TCU API Client - Authentication Exception
 * 
 * Exception class for authentication-related errors in the TCU API Client.
 * Handles authentication failures, invalid credentials, and authorization
 * issues when communicating with the TCU API.
 * 
 * @package    MBLogik\TCUAPIClient\Exceptions
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Exception class for authentication and authorization errors
 */

namespace MBLogik\TCUAPIClient\Exceptions;

class AuthenticationException extends TCUAPIException
{
    public function __construct(string $message = "Authentication failed", int $code = 401, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}