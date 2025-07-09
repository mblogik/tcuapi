<?php

/**
 * Test script for unconfirm endpoint
 */

// Simple XML generation test for unconfirm endpoint
function generateUnconfirmXMLRequest($username, $sessionToken, $f4indexno, $confirmationCode) {
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Create root Request element
    $request = $xml->createElement('Request');
    $xml->appendChild($request);
    
    // Create UsernameToken element
    $usernameToken = $xml->createElement('UsernameToken');
    $request->appendChild($usernameToken);
    
    // Add Username
    $usernameElement = $xml->createElement('Username', $username);
    $usernameToken->appendChild($usernameElement);
    
    // Add SessionToken
    $sessionTokenElement = $xml->createElement('SessionToken', $sessionToken);
    $usernameToken->appendChild($sessionTokenElement);
    
    // Create RequestParameters element
    $requestParams = $xml->createElement('RequestParameters');
    $request->appendChild($requestParams);
    
    // Add parameters
    $f4Param = $xml->createElement('f4indexno', htmlspecialchars($f4indexno));
    $requestParams->appendChild($f4Param);
    
    $confirmParam = $xml->createElement('ConfirmationCode', htmlspecialchars($confirmationCode));
    $requestParams->appendChild($confirmParam);
    
    return $xml->saveXML();
}

echo "=== Unconfirm Endpoint XML Generation Test ===\n\n";

// Test data matching your sample
$f4indexno = 'S1001/0012/2018';
$confirmationCode = '36R96P';

$xml = generateUnconfirmXMLRequest('UDSM', 'OTcyMURGMTY5QTRENU3MUJ', $f4indexno, $confirmationCode);
echo $xml . "\n";

echo "==========================================\n\n";

// Test another confirmation code format from your sample
echo "Testing with different confirmation code format:\n\n";
$confirmationCode2 = 'A5267Y'; // From the previous example

$xml2 = generateUnconfirmXMLRequest('UDSM', 'OTcyMURGMTY5QTRENU3MUJ', $f4indexno, $confirmationCode2);
echo $xml2 . "\n";

echo "==========================================\n\n";

// Test the validation for the new format
echo "Testing validation for different confirmation code formats:\n\n";
$confirmationCodes = [
    '36R96P' => false,  // From your sample - doesn't match our pattern
    'A5267Y' => true,   // From previous sample - matches our pattern
    'B1234Z' => true,   // Valid pattern
    'C9876X' => true,   // Valid pattern
];

foreach ($confirmationCodes as $code => $expectedOldPattern) {
    $resultOldPattern = preg_match('/^[A-Z][0-9]{4}[A-Z]$/', $code);
    $resultNewPattern = preg_match('/^[A-Z0-9]{6}$/', $code);
    
    echo "Code: '$code'\n";
    echo "  Old pattern (A5267Y): " . ($resultOldPattern ? 'MATCH' : 'NO MATCH') . "\n";
    echo "  New pattern (36R96P): " . ($resultNewPattern ? 'MATCH' : 'NO MATCH') . "\n";
    echo "\n";
}