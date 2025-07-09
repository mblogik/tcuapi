<?php

/**
 * Test script for unconfirm method
 */

require_once __DIR__ . '/vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;

// Test configuration
$config = new Configuration([
    'username' => 'UDSM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30
]);

try {
    $client = new TCUAPIClient($config);
    
    echo "=== Testing TCU API Client - unconfirm Endpoint ===\n\n";
    
    // Test 1: Valid unconfirm data with format 1 (A5267Y)
    echo "1. Testing valid unconfirm data (format 1):\n";
    try {
        $response = $client->admissions()->unconfirm('S1001/0012/2018', 'A5267Y');
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Expected network error (we're not actually connecting): " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Test 2: Valid unconfirm data with format 2 (36R96P)
    echo "2. Testing valid unconfirm data (format 2):\n";
    try {
        $response = $client->admissions()->unconfirm('S1001/0012/2018', '36R96P');
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Expected network error (we're not actually connecting): " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Test 3: Invalid F4 index number
    echo "3. Testing invalid F4 index number:\n";
    try {
        $response = $client->admissions()->unconfirm('invalid_f4', '36R96P');
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Expected validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Test 4: Invalid confirmation code
    echo "4. Testing invalid confirmation code:\n";
    try {
        $response = $client->admissions()->unconfirm('S1001/0012/2018', 'invalid_code');
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "Expected validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Test 5: Valid confirmation codes (both formats)
    echo "5. Testing different valid confirmation codes:\n";
    $validCodes = [
        'A5267Y' => 'Format 1 (Letter+4Digits+Letter)',
        '36R96P' => 'Format 2 (6 Alphanumeric)',
        'B1234Z' => 'Format 1 (Letter+4Digits+Letter)',
        'X9Y8Z7' => 'Format 2 (6 Alphanumeric)',
        'C0000D' => 'Format 1 (Letter+4Digits+Letter)'
    ];
    
    foreach ($validCodes as $code => $description) {
        echo "Testing code: $code ($description)\n";
        try {
            $response = $client->admissions()->unconfirm('S1001/0012/2018', $code);
            echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
        } catch (Exception $e) {
            echo "Expected network error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // Test 6: Invalid confirmation code formats
    echo "6. Testing invalid confirmation code formats:\n";
    $invalidCodes = [
        'A526Y' => 'Too short',
        'A52677Y' => 'Too long',
        'a5267Y' => 'Lowercase first letter',
        'A5267y' => 'Lowercase last letter',
        '15267Y' => 'Number first in format 1',
        'A526AY' => 'Letter in middle digits',
        '36r96P' => 'Lowercase in format 2',
        '36R96p' => 'Lowercase in format 2',
        '3R96P' => 'Too short for format 2',
        '36R96PP' => 'Too long for format 2'
    ];
    
    foreach ($invalidCodes as $code => $description) {
        echo "Testing invalid code: $code ($description)\n";
        try {
            $response = $client->admissions()->unconfirm('S1001/0012/2018', $code);
            echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
        } catch (Exception $e) {
            echo "Expected validation error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}