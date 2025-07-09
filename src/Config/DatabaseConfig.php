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
        $this->username = $config['username'] ?? ($this->driver === 'pgsql' ? 'postgres' : 'root');
        $this->password = $config['password'] ?? '';
        
        // Set default port based on driver
        $defaultPort = match($this->driver) {
            'pgsql' => 5432,
            'mysql' => 3306,
            'sqlite' => null,
            default => 3306
        };
        $this->port = $config['port'] ?? $defaultPort;
        
        // Set default charset based on driver
        $defaultCharset = match($this->driver) {
            'pgsql' => 'utf8',
            'mysql' => 'utf8mb4',
            'sqlite' => 'utf8',
            default => 'utf8mb4'
        };
        $this->charset = $config['charset'] ?? $defaultCharset;
        
        $this->tablePrefix = $config['table_prefix'] ?? 'tcu_api_';
    }
    
    public function toArray(): array
    {
        $config = [
            'driver' => $this->driver,
            'host' => $this->host,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => $this->charset,
            'prefix' => $this->tablePrefix,
            'strict' => true,
        ];
        
        // Add port only if not null (for SQLite compatibility)
        if ($this->port !== null) {
            $config['port'] = $this->port;
        }
        
        // Add driver-specific configurations
        switch ($this->driver) {
            case 'mysql':
                $config['collation'] = $this->charset . '_unicode_ci';
                $config['engine'] = null;
                break;
                
            case 'pgsql':
                $config['schema'] = 'public';
                $config['sslmode'] = 'prefer';
                break;
                
            case 'sqlite':
                // For SQLite, database is the file path
                $config['database'] = $this->database;
                unset($config['host'], $config['username'], $config['password']);
                break;
        }
        
        return $config;
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