<?php

/**
 * TCU API Client - Configuration Management
 * 
 * This file contains the configuration management class for the TCU API Client.
 * It handles API credentials, connection settings, database configuration, and
 * other client-specific options with validation and secure storage.
 * 
 * @package    MBLogik\TCUAPIClient\Config
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Centralized configuration management with validation, defaults,
 *             and secure credential handling for enterprise environments.
 */

namespace MBLogik\TCUAPIClient\Config;

class Configuration
{
    private const DEFAULT_BASE_URL = 'https://api.tcu.go.tz';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_RETRY_ATTEMPTS = 3;
    
    private string $baseUrl;
    private string $username;
    private string $securityToken;
    private int $timeout;
    private int $retryAttempts;
    private bool $enableLogging;
    private ?string $logPath;
    private bool $enableCache;
    private int $cacheExpiration;
    private bool $enableDatabaseLogging;
    private ?DatabaseConfig $databaseConfig;
    
    public function __construct(array $config = [])
    {
        $this->baseUrl = $config['base_url'] ?? self::DEFAULT_BASE_URL;
        $this->username = $config['username'] ?? '';
        $this->securityToken = $config['security_token'] ?? '';
        $this->timeout = $config['timeout'] ?? self::DEFAULT_TIMEOUT;
        $this->retryAttempts = $config['retry_attempts'] ?? self::DEFAULT_RETRY_ATTEMPTS;
        $this->enableLogging = $config['enable_logging'] ?? false;
        $this->logPath = $config['log_path'] ?? null;
        $this->enableCache = $config['enable_cache'] ?? false;
        $this->cacheExpiration = $config['cache_expiration'] ?? 300; // 5 minutes
        $this->enableDatabaseLogging = $config['enable_database_logging'] ?? false;
        $this->databaseConfig = isset($config['database']) ? new DatabaseConfig($config['database']) : null;
    }
    
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function getSecurityToken(): string
    {
        return $this->securityToken;
    }
    
    public function getTimeout(): int
    {
        return $this->timeout;
    }
    
    public function getRetryAttempts(): int
    {
        return $this->retryAttempts;
    }
    
    public function isLoggingEnabled(): bool
    {
        return $this->enableLogging;
    }
    
    public function getLogPath(): ?string
    {
        return $this->logPath;
    }
    
    public function isCacheEnabled(): bool
    {
        return $this->enableCache;
    }
    
    public function getCacheExpiration(): int
    {
        return $this->cacheExpiration;
    }
    
    public function isDatabaseLoggingEnabled(): bool
    {
        return $this->enableDatabaseLogging;
    }
    
    public function getDatabaseConfig(): ?DatabaseConfig
    {
        return $this->databaseConfig;
    }
    
    public function setCredentials(string $username, string $securityToken): void
    {
        $this->username = $username;
        $this->securityToken = $securityToken;
    }
    
    public function validate(): array
    {
        $errors = [];
        
        if (empty($this->username)) {
            $errors[] = 'Username is required';
        }
        
        if (empty($this->securityToken)) {
            $errors[] = 'Security token is required';
        }
        
        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid base URL format';
        }
        
        if ($this->timeout <= 0) {
            $errors[] = 'Timeout must be greater than 0';
        }
        
        return $errors;
    }
}