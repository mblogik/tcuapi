<?php

namespace MBLogik\TCUAPIClient\Database\Seeds;

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use Illuminate\Database\Capsule\Manager as Capsule;

class DefaultConfigSeeder
{
    private Capsule $capsule;
    private string $configTable;
    
    public function __construct(DatabaseConfig $config)
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection($config->toArray());
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
        
        $this->configTable = $config->getTablePrefix() . 'configs';
    }
    
    public function run(): void
    {
        $configs = [
            // API Settings
            [
                'key' => 'api.base_url',
                'value' => 'https://api.tcu.go.tz',
                'type' => 'string',
                'description' => 'Base URL for TCU API',
                'group' => 'api'
            ],
            [
                'key' => 'api.timeout',
                'value' => '30',
                'type' => 'integer',
                'description' => 'API request timeout in seconds',
                'group' => 'api'
            ],
            [
                'key' => 'api.retry_attempts',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Number of retry attempts for failed requests',
                'group' => 'api'
            ],
            [
                'key' => 'api.user_agent',
                'value' => 'TCU-API-Client/1.0',
                'type' => 'string',
                'description' => 'User agent string for API requests',
                'group' => 'api'
            ],
            
            // Logging Settings
            [
                'key' => 'logging.enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable API call logging',
                'group' => 'logging'
            ],
            [
                'key' => 'logging.log_requests',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Log request data',
                'group' => 'logging'
            ],
            [
                'key' => 'logging.log_responses',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Log response data',
                'group' => 'logging'
            ],
            [
                'key' => 'logging.log_errors',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Log error details',
                'group' => 'logging'
            ],
            [
                'key' => 'logging.retention_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Number of days to retain logs',
                'group' => 'logging'
            ],
            
            // Rate Limiting
            [
                'key' => 'rate_limiting.enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable rate limiting',
                'group' => 'rate_limiting'
            ],
            [
                'key' => 'rate_limiting.requests_per_minute',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Maximum requests per minute',
                'group' => 'rate_limiting'
            ],
            [
                'key' => 'rate_limiting.requests_per_hour',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'Maximum requests per hour',
                'group' => 'rate_limiting'
            ],
            [
                'key' => 'rate_limiting.block_duration',
                'value' => '300',
                'type' => 'integer',
                'description' => 'Block duration in seconds when rate limit exceeded',
                'group' => 'rate_limiting'
            ],
            
            // Performance Settings
            [
                'key' => 'performance.enable_cache',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable response caching',
                'group' => 'performance'
            ],
            [
                'key' => 'performance.cache_ttl',
                'value' => '300',
                'type' => 'integer',
                'description' => 'Cache TTL in seconds',
                'group' => 'performance'
            ],
            [
                'key' => 'performance.connection_pool_size',
                'value' => '5',
                'type' => 'integer',
                'description' => 'HTTP connection pool size',
                'group' => 'performance'
            ],
            
            // Security Settings
            [
                'key' => 'security.encrypt_logs',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Encrypt sensitive log data',
                'group' => 'security'
            ],
            [
                'key' => 'security.log_sensitive_data',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Log sensitive request/response data',
                'group' => 'security'
            ],
            [
                'key' => 'security.require_https',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require HTTPS for all API calls',
                'group' => 'security'
            ],
            
            // Notification Settings
            [
                'key' => 'notifications.error_threshold',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Number of errors before triggering notification',
                'group' => 'notifications'
            ],
            [
                'key' => 'notifications.email_alerts',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable email alerts for errors',
                'group' => 'notifications'
            ],
            [
                'key' => 'notifications.alert_email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'description' => 'Email address for alerts',
                'group' => 'notifications'
            ]
        ];
        
        foreach ($configs as $index => $config) {
            $config['sort_order'] = $index;
            $config['created_at'] = now();
            $config['updated_at'] = now();
            
            // Insert or update configuration
            $this->capsule->table($this->configTable)->updateOrInsert(
                ['key' => $config['key']],
                $config
            );
        }
        
        echo "Default configuration seeded successfully.\n";
    }
}