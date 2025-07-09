<?php

/**
 * TCU API Client - Database Configuration Examples
 * 
 * Demonstrates different database configurations including MySQL, PostgreSQL,
 * and SQLite for development and testing environments.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;

echo "=== TCU API Client - Database Configuration Examples ===\n\n";

// ====================================================================
// 1. MySQL Configuration (Default)
// ====================================================================
echo "1. MySQL Configuration:\n";
echo "========================\n";

$mysqlConfig = new Configuration([
    'username' => 'UDSM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30,
    'enable_database_logging' => true,
    'database_config' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'table_prefix' => 'tcu_api_'
    ]
]);

echo "MySQL Configuration:\n";
echo "```php\n";
echo "[\n";
echo "    'driver' => 'mysql',\n";
echo "    'host' => 'localhost',\n";
echo "    'port' => 3306,\n";
echo "    'database' => 'tcu_api_logs',\n";
echo "    'username' => 'root',\n";
echo "    'password' => 'password',\n";
echo "    'charset' => 'utf8mb4',\n";
echo "    'table_prefix' => 'tcu_api_'\n";
echo "]\n";
echo "```\n\n";

// ====================================================================
// 2. PostgreSQL Configuration
// ====================================================================
echo "2. PostgreSQL Configuration:\n";
echo "==============================\n";

$postgresConfig = new Configuration([
    'username' => 'UDSM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30,
    'enable_database_logging' => true,
    'database_config' => [
        'driver' => 'pgsql',
        'host' => 'localhost',
        'port' => 5432,
        'database' => 'tcu_api_logs',
        'username' => 'postgres',
        'password' => 'password',
        'charset' => 'utf8',
        'table_prefix' => 'tcu_api_'
    ]
]);

echo "PostgreSQL Configuration:\n";
echo "```php\n";
echo "[\n";
echo "    'driver' => 'pgsql',\n";
echo "    'host' => 'localhost',\n";
echo "    'port' => 5432,\n";
echo "    'database' => 'tcu_api_logs',\n";
echo "    'username' => 'postgres',\n";
echo "    'password' => 'password',\n";
echo "    'charset' => 'utf8',\n";
echo "    'table_prefix' => 'tcu_api_'\n";
echo "]\n";
echo "```\n\n";

// ====================================================================
// 3. SQLite Configuration (for testing)
// ====================================================================
echo "3. SQLite Configuration (Testing):\n";
echo "===================================\n";

$sqliteConfig = new Configuration([
    'username' => 'TEST_USER',
    'security_token' => 'TEST_TOKEN',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30,
    'enable_database_logging' => true,
    'database_config' => [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/../storage/database/tcu_api_logs.sqlite',
        'table_prefix' => 'tcu_api_'
    ]
]);

echo "SQLite Configuration:\n";
echo "```php\n";
echo "[\n";
echo "    'driver' => 'sqlite',\n";
echo "    'database' => '/path/to/database/tcu_api_logs.sqlite',\n";
echo "    'table_prefix' => 'tcu_api_'\n";
echo "]\n";
echo "```\n\n";

// ====================================================================
// 4. Environment-based Configuration
// ====================================================================
echo "4. Environment-based Configuration:\n";
echo "====================================\n";

function getDatabaseConfigFromEnvironment(): array
{
    $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
    
    $baseConfig = [
        'driver' => $driver,
        'database' => $_ENV['DB_DATABASE'] ?? 'tcu_api_logs',
        'table_prefix' => $_ENV['DB_PREFIX'] ?? 'tcu_api_'
    ];
    
    switch ($driver) {
        case 'mysql':
            return array_merge($baseConfig, [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => (int)($_ENV['DB_PORT'] ?? 3306),
                'username' => $_ENV['DB_USERNAME'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            ]);
            
        case 'pgsql':
            return array_merge($baseConfig, [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => (int)($_ENV['DB_PORT'] ?? 5432),
                'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8',
            ]);
            
        case 'sqlite':
            return [
                'driver' => 'sqlite',
                'database' => $_ENV['DB_DATABASE'] ?? ':memory:',
                'table_prefix' => $_ENV['DB_PREFIX'] ?? 'tcu_api_'
            ];
            
        default:
            throw new InvalidArgumentException("Unsupported database driver: {$driver}");
    }
}

echo "Environment Configuration Example:\n";
echo "```php\n";
echo "// .env file:\n";
echo "DB_DRIVER=pgsql\n";
echo "DB_HOST=localhost\n";
echo "DB_PORT=5432\n";
echo "DB_DATABASE=tcu_api_logs\n";
echo "DB_USERNAME=postgres\n";
echo "DB_PASSWORD=secret\n";
echo "DB_PREFIX=tcu_api_\n";
echo "\n";
echo "// PHP configuration:\n";
echo "\$config = new Configuration([\n";
echo "    'database_config' => getDatabaseConfigFromEnvironment()\n";
echo "]);\n";
echo "```\n\n";

// ====================================================================
// 5. Docker Configuration Examples
// ====================================================================
echo "5. Docker Configuration Examples:\n";
echo "===================================\n";

echo "Docker Compose with PostgreSQL:\n";
echo "```yaml\n";
echo "version: '3.8'\n";
echo "services:\n";
echo "  postgres:\n";
echo "    image: postgres:15\n";
echo "    environment:\n";
echo "      POSTGRES_DB: tcu_api_logs\n";
echo "      POSTGRES_USER: tcu_user\n";
echo "      POSTGRES_PASSWORD: secure_password\n";
echo "    ports:\n";
echo "      - \"5432:5432\"\n";
echo "    volumes:\n";
echo "      - postgres_data:/var/lib/postgresql/data\n";
echo "\n";
echo "  app:\n";
echo "    build: .\n";
echo "    environment:\n";
echo "      DB_DRIVER: pgsql\n";
echo "      DB_HOST: postgres\n";
echo "      DB_PORT: 5432\n";
echo "      DB_DATABASE: tcu_api_logs\n";
echo "      DB_USERNAME: tcu_user\n";
echo "      DB_PASSWORD: secure_password\n";
echo "    depends_on:\n";
echo "      - postgres\n";
echo "\n";
echo "volumes:\n";
echo "  postgres_data:\n";
echo "```\n\n";

echo "Docker Compose with MySQL:\n";
echo "```yaml\n";
echo "version: '3.8'\n";
echo "services:\n";
echo "  mysql:\n";
echo "    image: mysql:8.0\n";
echo "    environment:\n";
echo "      MYSQL_DATABASE: tcu_api_logs\n";
echo "      MYSQL_USER: tcu_user\n";
echo "      MYSQL_PASSWORD: secure_password\n";
echo "      MYSQL_ROOT_PASSWORD: root_password\n";
echo "    ports:\n";
echo "      - \"3306:3306\"\n";
echo "    volumes:\n";
echo "      - mysql_data:/var/lib/mysql\n";
echo "\n";
echo "  app:\n";
echo "    build: .\n";
echo "    environment:\n";
echo "      DB_DRIVER: mysql\n";
echo "      DB_HOST: mysql\n";
echo "      DB_PORT: 3306\n";
echo "      DB_DATABASE: tcu_api_logs\n";
echo "      DB_USERNAME: tcu_user\n";
echo "      DB_PASSWORD: secure_password\n";
echo "    depends_on:\n";
echo "      - mysql\n";
echo "\n";
echo "volumes:\n";
echo "  mysql_data:\n";
echo "```\n\n";

// ====================================================================
// 6. Laravel Integration Examples
// ====================================================================
echo "6. Laravel Integration Examples:\n";
echo "=================================\n";

echo "Laravel 11/12 Configuration:\n";
echo "```php\n";
echo "// config/database.php\n";
echo "'connections' => [\n";
echo "    'tcu_logs' => [\n";
echo "        'driver' => env('TCU_DB_DRIVER', 'mysql'),\n";
echo "        'host' => env('TCU_DB_HOST', '127.0.0.1'),\n";
echo "        'port' => env('TCU_DB_PORT', '3306'),\n";
echo "        'database' => env('TCU_DB_DATABASE', 'tcu_logs'),\n";
echo "        'username' => env('TCU_DB_USERNAME', 'forge'),\n";
echo "        'password' => env('TCU_DB_PASSWORD', ''),\n";
echo "        'charset' => 'utf8mb4',\n";
echo "        'collation' => 'utf8mb4_unicode_ci',\n";
echo "        'prefix' => env('TCU_DB_PREFIX', 'tcu_api_'),\n";
echo "        'strict' => true,\n";
echo "    ],\n";
echo "],\n";
echo "\n";
echo "// Laravel Service Provider\n";
echo "use MBLogik\\TCUAPIClient\\Client\\TCUAPIClient;\n";
echo "use MBLogik\\TCUAPIClient\\Config\\Configuration;\n";
echo "\n";
echo "\$config = new Configuration([\n";
echo "    'username' => config('services.tcu.username'),\n";
echo "    'security_token' => config('services.tcu.token'),\n";
echo "    'enable_database_logging' => true,\n";
echo "    'database_config' => config('database.connections.tcu_logs')\n";
echo "]);\n";
echo "\n";
echo "\$client = new TCUAPIClient(\$config);\n";
echo "```\n\n";

// ====================================================================
// 7. Migration Commands for Different Databases
// ====================================================================
echo "7. Migration Commands:\n";
echo "=======================\n";

echo "Run migrations:\n";
echo "```bash\n";
echo "# MySQL\n";
echo "DB_DRIVER=mysql composer run migrate\n";
echo "\n";
echo "# PostgreSQL\n";
echo "DB_DRIVER=pgsql composer run migrate\n";
echo "\n";
echo "# SQLite (testing)\n";
echo "DB_DRIVER=sqlite composer run migrate\n";
echo "```\n\n";

// ====================================================================
// 8. Connection Testing
// ====================================================================
echo "8. Connection Testing:\n";
echo "=======================\n";

echo "Test database connections:\n";
echo "```php\n";
echo "function testDatabaseConnection(\$config) {\n";
echo "    try {\n";
echo "        \$client = new TCUAPIClient(\$config);\n";
echo "        \$logger = \$client->getLogger();\n";
echo "        \n";
echo "        if (\$logger) {\n";
echo "            // Test connection by getting stats\n";
echo "            \$stats = \$logger->getApiCallStats();\n";
echo "            echo \"✅ Database connection successful\\n\";\n";
echo "            return true;\n";
echo "        } else {\n";
echo "            echo \"ℹ️ Database logging disabled\\n\";\n";
echo "            return true;\n";
echo "        }\n";
echo "    } catch (Exception \$e) {\n";
echo "        echo \"❌ Database connection failed: \" . \$e->getMessage() . \"\\n\";\n";
echo "        return false;\n";
echo "    }\n";
echo "}\n";
echo "\n";
echo "// Test all configurations\n";
echo "testDatabaseConnection(\$mysqlConfig);\n";
echo "testDatabaseConnection(\$postgresConfig);\n";
echo "testDatabaseConnection(\$sqliteConfig);\n";
echo "```\n\n";

echo "=== Database Compatibility Features ===\n";
echo "✅ **MySQL Support**: Full compatibility with MySQL 5.7+ and MariaDB 10.3+\n";
echo "✅ **PostgreSQL Support**: Full compatibility with PostgreSQL 12+\n";
echo "✅ **SQLite Support**: For testing and development environments\n";
echo "✅ **Laravel 10-12 Compatible**: Uses Illuminate Database v10-12\n";
echo "✅ **Cross-Platform**: Works on Windows, macOS, and Linux\n";
echo "✅ **Docker Ready**: Example Docker configurations provided\n";
echo "✅ **Environment Configuration**: Supports .env files and environment variables\n";
echo "✅ **Migration Support**: Database-agnostic migrations using Laravel Schema Builder\n";
echo "✅ **Connection Pooling**: Supports connection pooling and read/write splits\n";
echo "✅ **SSL/TLS Support**: Secure connections for production environments\n";