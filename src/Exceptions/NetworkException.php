<?php

/**
 * TCU API Client - Network Exception
 * 
 * Exception class for network-related errors in the TCU API Client.
 * Handles network connectivity issues, timeouts, and HTTP-related
 * errors when communicating with the TCU API.
 * 
 * @package    MBLogik\TCUAPIClient\Exceptions
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Exception class for network and HTTP communication errors
 */

namespace MBLogik\TCUAPIClient\Exceptions;

class NetworkException extends TCUAPIException
{
    public function __construct(string $message = "Network error occurred", int $code = 0, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}