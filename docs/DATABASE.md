# Database System Documentation

## Overview

The TCU API Client includes a comprehensive database system for logging, monitoring, and configuration management. This system provides enterprise-level tracking of all API interactions with detailed performance metrics and error handling.

## Database Schema

### Tables

#### 1. `tcu_api_logs` - Main Logging Table
Primary table for storing all API call details.

**Columns:**
- `id` - Primary key
- `endpoint` - API endpoint called
- `method` - HTTP method (POST, GET, etc.)
- `request_headers` - JSON of request headers
- `request_body` - Full request payload
- `response_code` - HTTP response code
- `response_headers` - JSON of response headers
- `response_body` - Full response data
- `execution_time` - Request execution time in seconds
- `username` - TCU API username
- `session_id` - Session identifier
- `ip_address` - Client IP address
- `user_agent` - User agent string
- `status` - Request status (pending, completed, error, timeout)
- `error_message` - Error details if failed
- `request_id` - Unique request identifier
- `created_at` - Timestamp of request start
- `updated_at` - Timestamp of last update

**Indexes:**
- `endpoint, created_at` - Endpoint performance queries
- `username, created_at` - User activity tracking
- `status, created_at` - Error monitoring
- `response_code, created_at` - HTTP status analysis
- `created_at, execution_time` - Performance monitoring

#### 2. `tcu_api_stats` - Daily Statistics
Aggregated daily statistics for reporting and monitoring.

**Columns:**
- `id` - Primary key
- `endpoint` - API endpoint
- `username` - TCU API username
- `date` - Statistics date
- `total_calls` - Total API calls
- `successful_calls` - Successful calls count
- `failed_calls` - Failed calls count
- `timeout_calls` - Timeout calls count
- `avg_execution_time` - Average execution time
- `max_execution_time` - Maximum execution time
- `min_execution_time` - Minimum execution time
- `total_data_sent` - Total bytes sent
- `total_data_received` - Total bytes received
- `created_at` - Record creation time
- `updated_at` - Last update time

**Indexes:**
- `endpoint, username, date` - Unique constraint
- `date, endpoint` - Daily reporting
- `username, date` - User statistics

#### 3. `tcu_api_rate_limits` - Rate Limiting
Track API rate limiting and usage patterns.

**Columns:**
- `id` - Primary key
- `username` - TCU API username
- `endpoint` - API endpoint
- `ip_address` - Client IP address
- `requests_count` - Current request count
- `requests_limit` - Rate limit threshold
- `window_start` - Rate limit window start
- `window_end` - Rate limit window end
- `is_blocked` - Whether user is blocked
- `blocked_until` - Block expiration time
- `created_at` - Record creation time
- `updated_at` - Last update time

**Indexes:**
- `username, endpoint, window_end` - Rate limiting queries
- `ip_address, window_end` - IP-based limiting
- `is_blocked, blocked_until` - Block management

#### 4. `tcu_api_configs` - Configuration Management
Store application configuration settings.

**Columns:**
- `id` - Primary key
- `key` - Configuration key
- `value` - Configuration value
- `type` - Data type (string, integer, boolean, json)
- `description` - Configuration description
- `group` - Configuration group
- `is_encrypted` - Whether value is encrypted
- `is_active` - Whether config is active
- `sort_order` - Display order
- `created_at` - Record creation time
- `updated_at` - Last update time

**Indexes:**
- `key` - Unique constraint
- `group, is_active` - Group filtering

#### 5. `tcu_api_migrations` - Migration Tracking
Track database migrations and schema changes.

**Columns:**
- `id` - Primary key
- `migration` - Migration name
- `batch` - Migration batch number
- `executed_at` - Execution timestamp

**Indexes:**
- `migration` - Unique constraint
- `batch, executed_at` - Batch tracking

## Setup and Installation

### 1. Database Configuration

Create a configuration file or set environment variables:

```php
// database/config.php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'tcu_api_logs',
    'username' => 'root',
    'password' => 'password',
    'charset' => 'utf8mb4',
    'table_prefix' => 'tcu_api_',
];
```

### 2. Environment Variables

```bash
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tcu_api_logs
DB_USERNAME=root
DB_PASSWORD=password
DB_TABLE_PREFIX=tcu_api_
```

### 3. Run Migrations

```bash
# Run all pending migrations
php migrate.php migrate

# Check migration status
php migrate.php status

# Rollback last migration
php migrate.php rollback

# Reset all migrations
php migrate.php reset
```

### 4. Seed Default Configuration

```bash
# Seed default configuration
php seed.php default

# Run all seeders
php seed.php all
```

## Usage Examples

### Enable Database Logging

```php
use MBLogik\TCUAPIClient\Client\TCUAPIClient;use MBLogik\TCUAPIClient\Config\Configuration;

$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_token',
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password',
        'table_prefix' => 'tcu_api_'
    ]
]);

$client = new TCUAPIClient($config);
```

### Query Logging Data

```php
// Get API call statistics
$stats = $client->getLogger()->getApiCallStats();

// Get recent errors
$errors = $client->getLogger()->getRecentErrors(10);

// Get statistics for specific endpoint
$endpointStats = $client->getLogger()->getApiCallStats('/applicants/checkStatus');
```

### Performance Monitoring

```php
// Monitor API performance
$logger = $client->getLogger();

// Get daily statistics
$dailyStats = $logger->getApiCallStats(null, '2025-01-01', '2025-01-31');

// Monitor execution times
$slowRequests = $logger->getSlowRequests(5.0); // Requests taking > 5 seconds
```

## Composer Scripts

The package includes convenient Composer scripts:

```bash
# Migration commands
composer migrate              # Run migrations
composer migrate:rollback     # Rollback migrations  
composer migrate:reset        # Reset all migrations
composer migrate:status       # Check migration status

# Seeding commands
composer seed                 # Seed default config
composer seed:all             # Run all seeders
```

## Maintenance

### Log Cleanup

Set up automated log cleanup to prevent database bloat:

```php
// Clean logs older than 90 days
$logger->cleanupLogs(90);
```

### Index Maintenance

Monitor and maintain database indexes for optimal performance:

```sql
-- Check index usage
SHOW INDEX FROM tcu_api_logs;

-- Analyze table performance
ANALYZE TABLE tcu_api_logs;
```

### Backup Strategy

Regular backups are recommended:

```bash
# Full database backup
mysqldump -u root -p tcu_api_logs > backup_$(date +%Y%m%d).sql

# Table-specific backup
mysqldump -u root -p tcu_api_logs tcu_api_logs > logs_backup.sql
```

## Security Considerations

1. **Sensitive Data**: Configure whether to log sensitive request/response data
2. **Encryption**: Enable encryption for sensitive log entries
3. **Access Control**: Restrict database access to authorized users only
4. **Audit Trail**: Monitor who accesses the logging database
5. **Data Retention**: Implement proper log retention policies

## Performance Optimization

1. **Indexing**: Ensure proper indexes are maintained
2. **Partitioning**: Consider table partitioning for large datasets
3. **Archiving**: Move old logs to archive tables
4. **Connection Pooling**: Use connection pooling for high-volume applications
5. **Query Optimization**: Monitor and optimize slow queries

## Troubleshooting

### Common Issues

1. **Migration Failures**: Check database permissions and connectivity
2. **Table Not Found**: Ensure migrations have been run
3. **Performance Issues**: Review indexes and query patterns
4. **Disk Space**: Monitor database size and implement cleanup
5. **Connection Issues**: Verify database configuration

### Debug Mode

Enable debug mode for detailed logging:

```php
$config = new Configuration([
    // ... other config
    'debug' => true,
    'log_level' => 'debug'
]);
```

This comprehensive database system provides enterprise-level logging and monitoring capabilities for the TCU API Client.
