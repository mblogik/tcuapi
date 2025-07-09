<?php

/**
 * Sample database configuration for TCU API Client
 * 
 * Copy this file to config.php and update with your database settings
 */

return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'tcu_api_logs',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'table_prefix' => 'tcu_api_',
    
    // Additional database options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];