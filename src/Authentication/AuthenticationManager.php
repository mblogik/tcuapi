<?php

/**
 * TCU API Client - Authentication Manager
 * 
 * This file contains the AuthenticationManager class for managing TCU API authentication.
 * It handles token creation, validation, and refresh operations with enterprise-level
 * security and logging capabilities.
 * 
 * @package    MBLogik\TCUAPIClient\Authentication
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Enterprise-level authentication management with token validation,
 *             refresh capabilities, and comprehensive security logging.
 */

namespace MBLogik\TCUAPIClient\Authentication;

use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\AuthenticationException;
use MBLogik\TCUAPIClient\Http\Logger\DatabaseLogger;
use Psr\Log\LoggerInterface;

class AuthenticationManager
{
    private Configuration $config;
    private ?DatabaseLogger $logger;
    private ?LoggerInterface $systemLogger;
    
    /**
     * AuthenticationManager constructor
     */
    public function __construct(Configuration $config, ?LoggerInterface $systemLogger = null)
    {
        $this->config = $config;
        $this->systemLogger = $systemLogger;
        
        // Initialize database logger if enabled
        if ($config->isDatabaseLoggingEnabled()) {
            $this->logger = new DatabaseLogger($config->getDatabaseConfig());
        }
    }
    
    /**
     * Create authentication token
     */
    public function createToken(string $username, string $sessionToken): UsernameToken
    {
        $startTime = microtime(true);
        
        try {
            // Validate credentials
            $this->validateCredentials($username, $sessionToken);
            
            // Create token
            $token = new UsernameToken($username, $sessionToken);
            
            // Log authentication attempt
            $this->logAuthenticationAttempt($username, 'create', true, $startTime);
            
            return $token;
            
        } catch (\Exception $e) {
            // Log failed authentication
            $this->logAuthenticationAttempt($username, 'create', false, $startTime, $e->getMessage());
            
            throw new AuthenticationException(
                'Failed to create authentication token: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
    
    /**
     * Validate authentication token
     */
    public function validateToken(UsernameToken $token): bool
    {
        $startTime = microtime(true);
        
        try {
            // Basic validation
            if (!$token->isValid()) {
                throw new AuthenticationException('Invalid token structure');
            }
            
            // Check if token is expired
            if ($token->isExpired($this->config->getTokenExpiryHours())) {
                throw new AuthenticationException('Token has expired');
            }
            
            // Additional validation logic can be added here
            // For example, checking against TCU API for token validity
            
            // Log successful validation
            $this->logAuthenticationAttempt($token->getUsername(), 'validate', true, $startTime);
            
            return true;
            
        } catch (\Exception $e) {
            // Log failed validation
            $this->logAuthenticationAttempt($token->getUsername(), 'validate', false, $startTime, $e->getMessage());
            
            return false;
        }
    }
    
    /**
     * Refresh authentication token
     */
    public function refreshToken(): UsernameToken
    {
        $startTime = microtime(true);
        
        try {
            // Create new token with existing credentials
            $token = $this->createToken(
                $this->config->getUsername(),
                $this->config->getSecurityToken()
            );
            
            // Log token refresh
            $this->logAuthenticationAttempt($token->getUsername(), 'refresh', true, $startTime);
            
            return $token;
            
        } catch (\Exception $e) {
            // Log failed refresh
            $this->logAuthenticationAttempt($this->config->getUsername(), 'refresh', false, $startTime, $e->getMessage());
            
            throw new AuthenticationException(
                'Failed to refresh authentication token: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
    
    /**
     * Validate credentials format
     */
    private function validateCredentials(string $username, string $sessionToken): void
    {
        // Validate username
        if (empty($username)) {
            throw new AuthenticationException('Username cannot be empty');
        }
        
        if (strlen($username) > 50) {
            throw new AuthenticationException('Username exceeds maximum length of 50 characters');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new AuthenticationException('Username contains invalid characters');
        }
        
        // Validate session token
        if (empty($sessionToken)) {
            throw new AuthenticationException('Session token cannot be empty');
        }
        
        if (strlen($sessionToken) < 10) {
            throw new AuthenticationException('Session token must be at least 10 characters');
        }
        
        if (strlen($sessionToken) > 255) {
            throw new AuthenticationException('Session token exceeds maximum length of 255 characters');
        }
    }
    
    /**
     * Log authentication attempt
     */
    private function logAuthenticationAttempt(
        string $username,
        string $operation,
        bool $success,
        float $startTime,
        ?string $errorMessage = null
    ): void {
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        // Log to database if enabled
        if ($this->logger) {
            try {
                $this->logger->logAuthenticationAttempt([
                    'username' => $username,
                    'operation' => $operation,
                    'success' => $success,
                    'execution_time' => $executionTime,
                    'error_message' => $errorMessage,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } catch (\Exception $e) {
                // Log database logging error to system logger
                if ($this->systemLogger) {
                    $this->systemLogger->error('Failed to log authentication attempt to database', [
                        'username' => $username,
                        'operation' => $operation,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Log to system logger if available
        if ($this->systemLogger) {
            $context = [
                'username' => $username,
                'operation' => $operation,
                'execution_time' => $executionTime,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            if ($success) {
                $this->systemLogger->info("Authentication {$operation} successful", $context);
            } else {
                $context['error'] = $errorMessage;
                $this->systemLogger->warning("Authentication {$operation} failed", $context);
            }
        }
    }
    
    /**
     * Check if credentials are configured
     */
    public function hasCredentials(): bool
    {
        return !empty($this->config->getUsername()) && !empty($this->config->getSecurityToken());
    }
    
    /**
     * Get token expiry time
     */
    public function getTokenExpiryHours(): int
    {
        return $this->config->getTokenExpiryHours();
    }
    
    /**
     * Create token from configuration
     */
    public function createTokenFromConfig(): UsernameToken
    {
        if (!$this->hasCredentials()) {
            throw new AuthenticationException('Authentication credentials not configured');
        }
        
        return $this->createToken(
            $this->config->getUsername(),
            $this->config->getSecurityToken()
        );
    }
}