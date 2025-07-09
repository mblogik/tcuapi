<?php

/**
 * TCU API Client - Database Configuration
 * 
 * This file contains the database configuration class for the TCU API Client.
 * It manages database connection settings, table configurations, and database
 * logging options with support for multiple database drivers.
 * 
 * @package    MBLogik\TCUAPIClient\Config
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Database configuration management for API call logging and
 *             data persistence with multi-driver support.
 */

namespace MBLogik\TCUAPIClient\Config;

class DatabaseConfig
{
    private string $driver;
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private int $port;
    private string $charset;
    private string $tablePrefix;
    
    public function __construct(array $config = [])
    {
        $this->driver = $config['driver'] ?? 'mysql';
        $this->host = $config['host'] ?? '127.0.0.1';
        $this->database = $config['database'] ?? 'tcu_api_logs';
        $this->username = $config['username'] ?? 'root';
        $this->password = $config['password'] ?? '';
        $this->port = $config['port'] ?? 3306;
        $this->charset = $config['charset'] ?? 'utf8mb4';
        $this->tablePrefix = $config['table_prefix'] ?? 'tcu_api_';
    }
    
    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => $this->charset,
            'collation' => $this->charset . '_unicode_ci',
            'prefix' => $this->tablePrefix,
            'strict' => true,
            'engine' => null,
        ];
    }
    
    public function getDriver(): string
    {
        return $this->driver;
    }
    
    public function getHost(): string
    {
        return $this->host;
    }
    
    public function getDatabase(): string
    {
        return $this->database;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function getPort(): int
    {
        return $this->port;
    }
    
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }
}