#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use MBLogik\TCUAPIClient\Database\MigrationRunner;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

function showUsage() {
    echo "TCU API Client Migration Tool\n";
    echo "Usage: php migrate.php [command] [options]\n\n";
    echo "Commands:\n";
    echo "  migrate      Run pending migrations\n";
    echo "  rollback     Rollback last migration batch\n";
    echo "  reset        Rollback all migrations\n";
    echo "  status       Show migration status\n";
    echo "  help         Show this help message\n\n";
    echo "Options:\n";
    echo "  --config=file    Path to configuration file\n";
    echo "  --steps=n        Number of migration steps for rollback (default: 1)\n";
    echo "  --force          Force operation without confirmation\n\n";
    echo "Examples:\n";
    echo "  php migrate.php migrate\n";
    echo "  php migrate.php rollback --steps=2\n";
    echo "  php migrate.php status\n";
}

function loadConfig($configPath = null): DatabaseConfig {
    if ($configPath && file_exists($configPath)) {
        $config = require $configPath;
        return new DatabaseConfig($config);
    }
    
    // Default configuration - can be overridden by environment variables
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
$options = getopt('', ['config:', 'steps:', 'force']);
$command = $argv[1] ?? 'help';

try {
    // Load configuration
    $config = loadConfig($options['config'] ?? null);
    
    // Initialize migration runner
    $runner = new MigrationRunner($config);
    
    switch ($command) {
        case 'migrate':
            echo "Running migrations...\n";
            $result = $runner->migrate();
            echo "\n" . $result['message'] . "\n";
            if (!empty($result['executed'])) {
                echo "Executed migrations:\n";
                foreach ($result['executed'] as $migration) {
                    echo "  - {$migration}\n";
                }
            }
            break;
            
        case 'rollback':
            $steps = (int)($options['steps'] ?? 1);
            if (!isset($options['force']) && !confirm("Are you sure you want to rollback {$steps} migration(s)?")) {
                echo "Rollback cancelled.\n";
                exit(1);
            }
            
            echo "Rolling back {$steps} migration(s)...\n";
            $result = $runner->rollback($steps);
            echo "\n" . $result['message'] . "\n";
            if (!empty($result['rolled_back'])) {
                echo "Rolled back migrations:\n";
                foreach ($result['rolled_back'] as $migration) {
                    echo "  - {$migration}\n";
                }
            }
            break;
            
        case 'reset':
            if (!isset($options['force']) && !confirm("Are you sure you want to rollback ALL migrations?")) {
                echo "Reset cancelled.\n";
                exit(1);
            }
            
            echo "Resetting all migrations...\n";
            $result = $runner->reset();
            echo "\n" . $result['message'] . "\n";
            if (!empty($result['rolled_back'])) {
                echo "Rolled back migrations:\n";
                foreach ($result['rolled_back'] as $migration) {
                    echo "  - {$migration}\n";
                }
            }
            break;
            
        case 'status':
            echo "Migration Status:\n";
            echo "================\n";
            $status = $runner->status();
            
            foreach ($status as $item) {
                $statusIcon = $item['status'] === 'executed' ? '✓' : '✗';
                $statusText = $item['status'] === 'executed' ? 'EXECUTED' : 'PENDING';
                echo sprintf("  %s %s [%s]\n", $statusIcon, $item['migration'], $statusText);
            }
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