#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use MBLogik\TCUAPIClient\Database\Seeds\DefaultConfigSeeder;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

function showUsage() {
    echo "TCU API Client Seeder Tool\n";
    echo "Usage: php seed.php [seeder] [options]\n\n";
    echo "Seeders:\n";
    echo "  default      Seed default configuration\n";
    echo "  all          Run all seeders\n";
    echo "  help         Show this help message\n\n";
    echo "Options:\n";
    echo "  --config=file    Path to configuration file\n";
    echo "  --force          Force seeding without confirmation\n\n";
    echo "Examples:\n";
    echo "  php seed.php default\n";
    echo "  php seed.php all --force\n";
}

function loadConfig($configPath = null): DatabaseConfig {
    if ($configPath && file_exists($configPath)) {
        $config = require $configPath;
        return new DatabaseConfig($config);
    }
    
    // Default configuration
    return new DatabaseConfig([
        'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => (int)($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_DATABASE'] ?? 'tcu_api_logs',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
        'table_prefix' => $_ENV['DB_TABLE_PREFIX'] ?? 'tcu_api_'
    ]);
}

function confirm($message): bool {
    echo $message . " (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return strtolower(trim($line)) === 'y';
}

// Parse command line arguments
$options = getopt('', ['config:', 'force']);
$seeder = $argv[1] ?? 'help';

try {
    // Load configuration
    $config = loadConfig($options['config'] ?? null);
    
    switch ($seeder) {
        case 'default':
            if (!isset($options['force']) && !confirm("Are you sure you want to seed default configuration?")) {
                echo "Seeding cancelled.\n";
                exit(1);
            }
            
            echo "Seeding default configuration...\n";
            $defaultSeeder = new DefaultConfigSeeder($config);
            $defaultSeeder->run();
            echo "Default configuration seeded successfully.\n";
            break;
            
        case 'all':
            if (!isset($options['force']) && !confirm("Are you sure you want to run all seeders?")) {
                echo "Seeding cancelled.\n";
                exit(1);
            }
            
            echo "Running all seeders...\n";
            
            // Run default config seeder
            echo "1. Seeding default configuration...\n";
            $defaultSeeder = new DefaultConfigSeeder($config);
            $defaultSeeder->run();
            
            echo "All seeders completed successfully.\n";
            break;
            
        case 'help':
        default:
            showUsage();
            break;
    }
    
} catch (TCUAPIException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Details: " . $e->getPrevious()->getMessage() . "\n";
    }
    exit(1);
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}