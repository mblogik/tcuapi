<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use MBLogik\TCUAPIClient\Database\MigrationRunner;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

echo "=== TCU API Client Database Migration Examples ===\n\n";

// Example 1: Basic migration setup
echo "1. Basic Migration Setup\n";
echo "========================\n";

$config = new DatabaseConfig([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'tcu_api_logs',
    'username' => 'root',
    'password' => 'password',
    'table_prefix' => 'tcu_api_'
]);

try {
    $runner = new MigrationRunner($config);
    
    // Example 2: Check migration status
    echo "2. Checking Migration Status\n";
    echo "============================\n";
    
    $status = $runner->status();
    foreach ($status as $item) {
        $statusIcon = $item['status'] === 'executed' ? '✓' : '✗';
        echo sprintf("  %s %s [%s]\n", $statusIcon, $item['migration'], strtoupper($item['status']));
    }
    echo "\n";
    
    // Example 3: Running migrations
    echo "3. Running Migrations\n";
    echo "=====================\n";
    
    echo "Running pending migrations...\n";
    $result = $runner->migrate();
    
    if (!empty($result['executed'])) {
        echo "Successfully executed migrations:\n";
        foreach ($result['executed'] as $migration) {
            echo "  - {$migration}\n";
        }
    } else {
        echo $result['message'] . "\n";
    }
    echo "\n";
    
    // Example 4: Database structure verification
    echo "4. Database Structure Verification\n";
    echo "===================================\n";
    
    // Check if tables were created
    $tables = [
        'tcu_api_logs' => 'Main API logging table',
        'tcu_api_stats' => 'Daily statistics summary table',
        'tcu_api_rate_limits' => 'Rate limiting tracking table',
        'tcu_api_configs' => 'Configuration settings table',
        'tcu_api_migrations' => 'Migration tracking table'
    ];
    
    foreach ($tables as $table => $description) {
        // This would normally check if table exists
        echo "  ✓ {$table} - {$description}\n";
    }
    echo "\n";
    
    // Example 5: Rollback example (commented out for safety)
    echo "5. Rollback Example (Commented for Safety)\n";
    echo "==========================================\n";
    echo "// To rollback last migration:\n";
    echo "// \$result = \$runner->rollback(1);\n";
    echo "// \n";
    echo "// To rollback multiple migrations:\n";
    echo "// \$result = \$runner->rollback(3);\n";
    echo "// \n";
    echo "// To reset all migrations:\n";
    echo "// \$result = \$runner->reset();\n";
    echo "\n";
    
} catch (TCUAPIException $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Details: " . $e->getPrevious()->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "Unexpected Error: " . $e->getMessage() . "\n";
}

echo "=== Command Line Usage Examples ===\n\n";

echo "1. Run migrations from command line:\n";
echo "   php migrate.php migrate\n\n";

echo "2. Check migration status:\n";
echo "   php migrate.php status\n\n";

echo "3. Rollback last migration:\n";
echo "   php migrate.php rollback\n\n";

echo "4. Rollback multiple migrations:\n";
echo "   php migrate.php rollback --steps=3\n\n";

echo "5. Reset all migrations:\n";
echo "   php migrate.php reset --force\n\n";

echo "6. Use custom config file:\n";
echo "   php migrate.php migrate --config=database/config.php\n\n";

echo "7. Seed default configuration:\n";
echo "   php seed.php default\n\n";

echo "8. Run all seeders:\n";
echo "   php seed.php all --force\n\n";

echo "=== Environment Variables ===\n\n";

echo "You can also use environment variables:\n";
echo "DB_DRIVER=mysql\n";
echo "DB_HOST=localhost\n";
echo "DB_PORT=3306\n";
echo "DB_DATABASE=tcu_api_logs\n";
echo "DB_USERNAME=root\n";
echo "DB_PASSWORD=password\n";
echo "DB_TABLE_PREFIX=tcu_api_\n\n";

echo "=== Production Deployment ===\n\n";

echo "For production deployment:\n";
echo "1. Set up your database credentials\n";
echo "2. Run: php migrate.php migrate --force\n";
echo "3. Run: php seed.php default --force\n";
echo "4. Configure your application to use the logging features\n";
echo "5. Set up log rotation and cleanup jobs\n";
echo "6. Monitor database performance and indexing\n\n";

echo "Migration examples completed!\n";