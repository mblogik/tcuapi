<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Config;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Config\DatabaseConfig;

class ConfigurationTest extends TestCase
{
    public function testCanCreateConfigurationWithDefaults()
    {
        $config = new Configuration();
        
        $this->assertEquals('https://api.tcu.go.tz', $config->getBaseUrl());
        $this->assertEquals(30, $config->getTimeout());
        $this->assertEquals(3, $config->getRetryAttempts());
        $this->assertFalse($config->isLoggingEnabled());
        $this->assertFalse($config->isCacheEnabled());
        $this->assertFalse($config->isDatabaseLoggingEnabled());
    }
    
    public function testCanCreateConfigurationWithCustomValues()
    {
        $config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'base_url' => 'https://test.api.tcu.go.tz',
            'timeout' => 60,
            'retry_attempts' => 5,
            'enable_logging' => true,
            'enable_cache' => true,
            'enable_database_logging' => true,
            'database' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'test_db'
            ]
        ]);
        
        $this->assertEquals('test_user', $config->getUsername());
        $this->assertEquals('test_token', $config->getSecurityToken());
        $this->assertEquals('https://test.api.tcu.go.tz', $config->getBaseUrl());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertEquals(5, $config->getRetryAttempts());
        $this->assertTrue($config->isLoggingEnabled());
        $this->assertTrue($config->isCacheEnabled());
        $this->assertTrue($config->isDatabaseLoggingEnabled());
        $this->assertInstanceOf(DatabaseConfig::class, $config->getDatabaseConfig());
    }
    
    public function testCanSetCredentials()
    {
        $config = new Configuration();
        $config->setCredentials('new_user', 'new_token');
        
        $this->assertEquals('new_user', $config->getUsername());
        $this->assertEquals('new_token', $config->getSecurityToken());
    }
    
    public function testValidationPassesWithValidConfiguration()
    {
        $config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'base_url' => 'https://api.tcu.go.tz',
            'timeout' => 30
        ]);
        
        $errors = $config->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationFailsWithMissingCredentials()
    {
        $config = new Configuration([
            'base_url' => 'https://api.tcu.go.tz'
        ]);
        
        $errors = $config->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Username is required', $errors);
        $this->assertContains('Security token is required', $errors);
    }
    
    public function testValidationFailsWithInvalidUrl()
    {
        $config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'base_url' => 'invalid-url'
        ]);
        
        $errors = $config->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Invalid base URL format', $errors);
    }
    
    public function testValidationFailsWithInvalidTimeout()
    {
        $config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'timeout' => 0
        ]);
        
        $errors = $config->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Timeout must be greater than 0', $errors);
    }
}