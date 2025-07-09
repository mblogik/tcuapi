<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Config;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Config\DatabaseConfig;

class DatabaseConfigTest extends TestCase
{
    public function testCanCreateDatabaseConfigWithDefaults()
    {
        $config = new DatabaseConfig();
        
        $this->assertEquals('mysql', $config->getDriver());
        $this->assertEquals('127.0.0.1', $config->getHost());
        $this->assertEquals('tcu_api_logs', $config->getDatabase());
        $this->assertEquals('root', $config->getUsername());
        $this->assertEquals('', $config->getPassword());
        $this->assertEquals(3306, $config->getPort());
        $this->assertEquals('tcu_api_', $config->getTablePrefix());
    }
    
    public function testCanCreateDatabaseConfigWithCustomValues()
    {
        $config = new DatabaseConfig([
            'driver' => 'postgresql',
            'host' => 'localhost',
            'database' => 'custom_db',
            'username' => 'custom_user',
            'password' => 'custom_pass',
            'port' => 5432,
            'table_prefix' => 'custom_'
        ]);
        
        $this->assertEquals('postgresql', $config->getDriver());
        $this->assertEquals('localhost', $config->getHost());
        $this->assertEquals('custom_db', $config->getDatabase());
        $this->assertEquals('custom_user', $config->getUsername());
        $this->assertEquals('custom_pass', $config->getPassword());
        $this->assertEquals(5432, $config->getPort());
        $this->assertEquals('custom_', $config->getTablePrefix());
    }
    
    public function testToArrayReturnsCorrectFormat()
    {
        $config = new DatabaseConfig([
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'test_db',
            'username' => 'test_user',
            'password' => 'test_pass',
            'port' => 3306,
            'table_prefix' => 'test_'
        ]);
        
        $expected = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'test_db',
            'username' => 'test_user',
            'password' => 'test_pass',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'test_',
            'strict' => true,
            'engine' => null,
        ];
        
        $this->assertEquals($expected, $config->toArray());
    }
    
    public function testSqliteConfiguration()
    {
        $config = new DatabaseConfig([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        
        $this->assertEquals('sqlite', $config->getDriver());
        $this->assertEquals(':memory:', $config->getDatabase());
    }
}