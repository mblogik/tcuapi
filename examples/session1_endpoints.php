<?php

/**
 * TCU API Client - Session 1 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 1 endpoints (3.1-3.5)
 * of the TCU API Client. These examples demonstrate proper usage, validation,
 * error handling, and response processing.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 1 TCU API endpoints
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;

// Configuration setup
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30,
    'retry_attempts' => 3,
    'enable_logging' => true,
    'enable_database_logging' => true,
    'database_config' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api',
        'username' => 'db_user',
        'password' => 'db_password',
        'port' => 3306,
        'table_prefix' => 'tcu_api_'
    ]
]);

try {
    // Initialize the client
    $client = new TCUAPIClient($config);
    
    echo "=== TCU API Client - Session 1 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.1 CHECK APPLICANT STATUS
    // ==========================================
    echo "1. CHECK APPLICANT STATUS (3.1)\n";
    echo "================================\n";
    
    try {
        // Basic check with F4 index number only
        echo "Checking applicant status with F4 index number...\n";
        $statusResponse = $client->applicants()->checkStatus('S0123456789');
        echo "Response: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Check with both F4 and F6 index numbers
        echo "Checking applicant status with F4 and F6 index numbers...\n";
        $statusResponse = $client->applicants()->checkStatus('S0123456789', 'S0123456790');
        echo "Response: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Check with F4 index number and AVN (for diploma holders)
        echo "Checking applicant status with F4 index number and AVN...\n";
        $statusResponse = $client->applicants()->checkStatus('S0123456789', null, 'AVN123456');
        echo "Response: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.2 ADD APPLICANT
    // ==========================================
    echo "2. ADD APPLICANT (3.2)\n";
    echo "====================\n";
    
    try {
        // Prepare applicant data
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'f4indexno' => 'S0123456789',
            'f6indexno' => 'S0123456790',
            'nationality' => 'Tanzanian',
            'year' => 2000,
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        echo "Adding new applicant...\n";
        echo "Applicant Data: " . json_encode($applicantData, JSON_PRETTY_PRINT) . "\n";
        
        $addResponse = $client->applicants()->add($applicantData);
        echo "Response: " . json_encode($addResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Example with diploma holder (using AVN)
        $diplomaApplicantData = [
            'firstname' => 'Jane',
            'middlename' => 'Mary',
            'surname' => 'Smith',
            'gender' => 'F',
            'f4indexno' => 'S0123456791',
            'avn' => 'AVN123457',
            'nationality' => 'Tanzanian',
            'year' => 1999,
            'applicant_category' => 'Private',
            'institution_code' => 'INST002',
            'email' => 'jane.smith@example.com',
            'phone' => '+255756789012'
        ];
        
        echo "Adding diploma holder applicant...\n";
        echo "Diploma Applicant Data: " . json_encode($diplomaApplicantData, JSON_PRETTY_PRINT) . "\n";
        
        $diplomaResponse = $client->applicants()->add($diplomaApplicantData);
        echo "Response: " . json_encode($diplomaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.3 SUBMIT PROGRAMME CHOICES
    // ==========================================
    echo "3. SUBMIT PROGRAMME CHOICES (3.3)\n";
    echo "=================================\n";
    
    try {
        // Prepare programme choices
        $programmeChoices = [
            [
                'programme_code' => 'PROG001',
                'priority' => 1
            ],
            [
                'programme_code' => 'PROG002',
                'priority' => 2
            ],
            [
                'programme_code' => 'PROG003',
                'priority' => 3
            ]
        ];
        
        // Contact details
        $contactDetails = [
            'mobile' => '+255712345678',
            'email' => 'john.doe@example.com'
        ];
        
        // Additional data
        $additionalData = [
            'admission_status' => 'selected',
            'programme_of_admission' => 'PROG001',
            'other_f4_index_numbers' => ['S0123456792', 'S0123456793'],
            'other_f6_index_numbers' => ['S0123456794']
        ];
        
        echo "Submitting programme choices...\n";
        echo "Programme Choices: " . json_encode($programmeChoices, JSON_PRETTY_PRINT) . "\n";
        echo "Contact Details: " . json_encode($contactDetails, JSON_PRETTY_PRINT) . "\n";
        echo "Additional Data: " . json_encode($additionalData, JSON_PRETTY_PRINT) . "\n";
        
        $submitResponse = $client->applicants()->submitProgrammeChoices(
            'S0123456789',
            $programmeChoices,
            $contactDetails,
            $additionalData
        );
        
        echo "Response: " . json_encode($submitResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.4 CONFIRM APPLICANT SELECTION
    // ==========================================
    echo "4. CONFIRM APPLICANT SELECTION (3.4)\n";
    echo "====================================\n";
    
    try {
        // Confirm admission with TCU provided code
        echo "Confirming applicant selection...\n";
        $confirmResponse = $client->admissions()->confirm('S0123456789', 'CONF123ABC');
        echo "Response: " . json_encode($confirmResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Example with different confirmation code format
        echo "Confirming with different code format...\n";
        $confirmResponse2 = $client->admissions()->confirm('S0123456790', 'TCU2025001');
        echo "Response: " . json_encode($confirmResponse2, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.5 UNCONFIRM ADMISSION
    // ==========================================
    echo "5. UNCONFIRM ADMISSION (3.5)\n";
    echo "============================\n";
    
    try {
        // Unconfirm admission with reason
        echo "Unconfirming admission...\n";
        $unconfirmResponse = $client->admissions()->unconfirm(
            'S0123456789',
            'Changed mind, want to attend different institution'
        );
        echo "Response: " . json_encode($unconfirmResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Example with detailed reason
        echo "Unconfirming with detailed reason...\n";
        $detailedReason = 'I have received a scholarship offer from another institution that ' .
                         'better aligns with my career goals and provides better financial support. ' .
                         'I appreciate the opportunity but must decline this admission.';
        
        $unconfirmResponse2 = $client->admissions()->unconfirm('S0123456791', $detailedReason);
        echo "Response: " . json_encode($unconfirmResponse2, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ERROR HANDLING EXAMPLES
    // ==========================================
    echo "6. ERROR HANDLING EXAMPLES\n";
    echo "==========================\n";
    
    // Example 1: Invalid F4 index number format
    try {
        echo "Testing invalid F4 index number...\n";
        $client->applicants()->checkStatus('invalid_f4_format');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Missing required fields
    try {
        echo "Testing missing required fields...\n";
        $client->applicants()->add([
            'firstname' => 'John',
            'middlename' => 'Michael'
            // Missing other required fields
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Invalid email format
    try {
        echo "Testing invalid email format...\n";
        $client->applicants()->submitProgrammeChoices(
            'S0123456789',
            [['programme_code' => 'PROG001', 'priority' => 1]],
            ['email' => 'invalid-email-format', 'mobile' => '+255712345678']
        );
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Empty confirmation code
    try {
        echo "Testing empty confirmation code...\n";
        $client->admissions()->confirm('S0123456789', '');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Empty reason for unconfirm
    try {
        echo "Testing empty reason for unconfirm...\n";
        $client->admissions()->unconfirm('S0123456789', '');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 1 Examples Complete ===\n";
    
} catch (TCUAPIException $e) {
    echo "TCU API Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    
    if (method_exists($e, 'getContext')) {
        echo "Context: " . json_encode($e->getContext(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}