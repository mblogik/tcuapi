<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Config\DatabaseConfig;

// Example 1: Basic configuration
$basicConfig = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token'
]);

// Example 2: Advanced configuration with all options
$advancedConfig = new Configuration([
    'base_url' => 'https://api.tcu.go.tz',
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'timeout' => 45,
    'retry_attempts' => 5,
    'enable_logging' => true,
    'log_path' => '/var/log/tcu_api.log',
    'enable_cache' => true,
    'cache_expiration' => 600, // 10 minutes
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'api_user',
        'password' => 'secure_password',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'table_prefix' => 'tcu_api_'
    ]
]);

// Example 3: Production configuration
$productionConfig = new Configuration([
    'username' => $_ENV['TCU_USERNAME'] ?? 'your_username',
    'security_token' => $_ENV['TCU_SECURITY_TOKEN'] ?? 'your_token',
    'timeout' => 30,
    'retry_attempts' => 3,
    'enable_logging' => true,
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'database' => $_ENV['DB_DATABASE'] ?? 'tcu_api_logs',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'port' => (int)($_ENV['DB_PORT'] ?? 3306),
        'table_prefix' => 'tcu_api_'
    ]
]);

// Example 4: Development configuration (no database logging)
$devConfig = new Configuration([
    'username' => 'dev_user',
    'security_token' => 'dev_token',
    'timeout' => 60,
    'retry_attempts' => 1,
    'enable_logging' => true,
    'log_path' => __DIR__ . '/logs/dev.log',
    'enable_cache' => false,
    'enable_database_logging' => false
]);

// Example 5: Testing configuration
$testConfig = new Configuration([
    'base_url' => 'https://test-api.tcu.go.tz',
    'username' => 'test_user',
    'security_token' => 'test_token',
    'timeout' => 10,
    'retry_attempts' => 1,
    'enable_logging' => false,
    'enable_cache' => false,
    'enable_database_logging' => false
]);

// Validate configurations
$configs = [
    'basic' => $basicConfig,
    'advanced' => $advancedConfig,
    'production' => $productionConfig,
    'development' => $devConfig,
    'testing' => $testConfig
];

foreach ($configs as $name => $config) {
    echo "=== {$name} Configuration ===\n";
    $errors = $config->validate();
    if (empty($errors)) {
        echo "✓ Configuration is valid\n";
    } else {
        echo "✗ Configuration errors:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    echo "\n";
}

// Example 6: Database configuration only
$dbConfig = new DatabaseConfig([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'tcu_api_logs',
    'username' => 'root',
    'password' => 'password',
    'port' => 3306,
    'charset' => 'utf8mb4',
    'table_prefix' => 'tcu_api_'
]);

echo "=== Database Configuration ===\n";
echo "Driver: " . $dbConfig->getDriver() . "\n";
echo "Host: " . $dbConfig->getHost() . "\n";
echo "Database: " . $dbConfig->getDatabase() . "\n";
echo "Table Prefix: " . $dbConfig->getTablePrefix() . "\n";
echo "Config Array: " . json_encode($dbConfig->toArray(), JSON_PRETTY_PRINT) . "\n";