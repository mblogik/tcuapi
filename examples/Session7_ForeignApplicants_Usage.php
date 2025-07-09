<?php

/**
 * TCU API Client - Session 7: Foreign Applicants Usage Examples (3.31-3.35)
 * 
 * This file demonstrates how to use the ForeignApplicantResource for managing
 * foreign applicant operations including registration, details retrieval,
 * visa processing, and status tracking.
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
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

// Configuration
$config = new Configuration([
    'username' => 'UDSM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30,
    'retry_attempts' => 3,
    'enable_database_logging' => true,
    'database_config' => [
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'db_user',
        'password' => 'db_password'
    ]
]);

$client = new TCUAPIClient($config);

echo "=== TCU API Client - Session 7: Foreign Applicants Examples ===\n\n";

try {
    // ====================================================================
    // 3.31 - Register Foreign Applicant
    // ====================================================================
    echo "1. Registering Foreign Applicant:\n";
    echo "-----------------------------------\n";
    
    $applicantData = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'passportNumber' => 'AB123456',
        'nationality' => 'US',
        'dateOfBirth' => '1995-05-15',
        'emailAddress' => 'john.doe@example.com',
        'mobileNumber' => '+255766123456',
        'programmeCode' => 'UD023',
        'academicYear' => '2025/2026',
        'previousEducation' => 'Bachelor of Science in Computer Science',
        'institutionCountry' => 'US',
        'englishProficiency' => 'IELTS 7.0',
        'sponsorshipType' => 'Self-sponsored'
    ];
    
    try {
        $response = $client->foreignApplicants()->registerForeignApplicant($applicantData);
        echo "✅ Registration successful!\n";
        echo "Applicant ID: " . $response['applicantId'] . "\n";
        echo "Message: " . $response['message'] . "\n\n";
    } catch (ValidationException $e) {
        echo "❌ Validation Error: " . $e->getMessage() . "\n";
        echo "Errors: " . implode(', ', $e->getErrors()) . "\n\n";
    }
    
    // ====================================================================
    // 3.32 - Get Foreign Applicant Details
    // ====================================================================
    echo "2. Retrieving Foreign Applicant Details:\n";
    echo "-----------------------------------------\n";
    
    $passportNumber = 'AB123456';
    
    try {
        $response = $client->foreignApplicants()->getForeignApplicantDetails($passportNumber);
        echo "✅ Details retrieved successfully!\n";
        echo "Applicant Name: " . $response['applicant']['firstName'] . " " . $response['applicant']['lastName'] . "\n";
        echo "Nationality: " . $response['applicant']['nationality'] . "\n";
        echo "Status: " . $response['applicant']['status'] . "\n\n";
    } catch (ValidationException $e) {
        echo "❌ Validation Error: " . $e->getMessage() . "\n\n";
    }
    
    // ====================================================================
    // 3.33 - Process Visa Application
    // ====================================================================
    echo "3. Processing Visa Application:\n";
    echo "--------------------------------\n";
    
    $visaApplicationData = [
        'passportNumber' => 'AB123456',
        'visaType' => 'student',
        'purposeOfVisit' => 'education',
        'durationOfStay' => '4 years',
        'sponsorshipDetails' => 'Self-sponsored with family support',
        'accommodationArrangements' => 'University hostel accommodation',
        'financialSupport' => '$50,000 USD',
        'healthInsurance' => 'International student health insurance',
        'emergencyContact' => 'Jane Doe, +1234567890'
    ];
    
    try {
        $response = $client->foreignApplicants()->processVisaApplication($visaApplicationData);
        echo "✅ Visa application processed successfully!\n";
        echo "Application ID: " . $response['applicationId'] . "\n";
        echo "Estimated Processing Time: " . $response['estimatedProcessingTime'] . "\n";
        echo "Message: " . $response['message'] . "\n\n";
    } catch (ValidationException $e) {
        echo "❌ Validation Error: " . $e->getMessage() . "\n\n";
    }
    
    // ====================================================================
    // 3.34 - Get Visa Status
    // ====================================================================
    echo "4. Checking Visa Application Status:\n";
    echo "-------------------------------------\n";
    
    $applicationId = 'VA2025001';
    
    try {
        $response = $client->foreignApplicants()->getVisaStatus($applicationId);
        echo "✅ Visa status retrieved successfully!\n";
        echo "Application ID: " . $response['visaApplication']['applicationId'] . "\n";
        echo "Status: " . $response['visaApplication']['status'] . "\n";
        echo "Submission Date: " . $response['visaApplication']['submissionDate'] . "\n";
        echo "Current Stage: " . $response['visaApplication']['currentStage'] . "\n";
        echo "Estimated Completion: " . $response['visaApplication']['estimatedCompletion'] . "\n\n";
    } catch (ValidationException $e) {
        echo "❌ Validation Error: " . $e->getMessage() . "\n\n";
    }
    
    // ====================================================================
    // 3.35 - Update Foreign Applicant Information
    // ====================================================================
    echo "5. Updating Foreign Applicant Information:\n";
    echo "-------------------------------------------\n";
    
    $passportNumber = 'AB123456';
    $updateData = [
        'mobileNumber' => '+255766654321',
        'emailAddress' => 'john.updated@example.com',
        'currentAddress' => '123 University Road, Dar es Salaam, Tanzania',
        'emergencyContact' => 'Jane Doe, +1234567890, jane.doe@example.com',
        'accommodationStatus' => 'Confirmed - University Hostel Block A, Room 205'
    ];
    
    try {
        $response = $client->foreignApplicants()->updateForeignApplicantInformation($passportNumber, $updateData);
        echo "✅ Information updated successfully!\n";
        echo "Message: " . $response['message'] . "\n\n";
    } catch (ValidationException $e) {
        echo "❌ Validation Error: " . $e->getMessage() . "\n\n";
    }
    
    // ====================================================================
    // Advanced Usage Examples
    // ====================================================================
    echo "6. Advanced Usage Examples:\n";
    echo "============================\n\n";
    
    // Multiple foreign applicant registration
    echo "6.1 Multiple Foreign Applicant Registration:\n";
    echo "----------------------------------------------\n";
    
    $multipleApplicants = [
        [
            'firstName' => 'Alice',
            'lastName' => 'Johnson',
            'passportNumber' => 'CD789012',
            'nationality' => 'GB',
            'dateOfBirth' => '1994-08-22',
            'emailAddress' => 'alice.johnson@example.com',
            'mobileNumber' => '+255766111222',
            'programmeCode' => 'UD025',
            'academicYear' => '2025/2026'
        ],
        [
            'firstName' => 'Pierre',
            'lastName' => 'Dubois',
            'passportNumber' => 'EF345678',
            'nationality' => 'FR',
            'dateOfBirth' => '1996-03-10',
            'emailAddress' => 'pierre.dubois@example.com',
            'mobileNumber' => '+255766333444',
            'programmeCode' => 'UD030',
            'academicYear' => '2025/2026'
        ]
    ];
    
    foreach ($multipleApplicants as $index => $applicant) {
        try {
            $response = $client->foreignApplicants()->registerForeignApplicant($applicant);
            echo "✅ Applicant " . ($index + 1) . " registered successfully!\n";
            echo "   Name: " . $applicant['firstName'] . " " . $applicant['lastName'] . "\n";
            echo "   Applicant ID: " . $response['applicantId'] . "\n\n";
        } catch (ValidationException $e) {
            echo "❌ Applicant " . ($index + 1) . " registration failed: " . $e->getMessage() . "\n\n";
        }
    }
    
    // Passport number validation examples
    echo "6.2 Passport Number Validation Examples:\n";
    echo "-----------------------------------------\n";
    
    $passportNumbers = [
        'AB123456' => 'Standard alphanumeric (8 chars)',
        'X9Y8Z7W6V5U4' => 'Long alphanumeric (12 chars)',
        '123456' => 'Numeric only (6 chars)',
        'ABCDEF' => 'Letters only (6 chars)',
        'ab123456' => 'Invalid: lowercase letters',
        'AB 123456' => 'Invalid: contains space',
        'AB-123456' => 'Invalid: contains hyphen'
    ];
    
    foreach ($passportNumbers as $passport => $description) {
        $testData = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'passportNumber' => $passport,
            'nationality' => 'US',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'test@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];
        
        try {
            $client->foreignApplicants()->registerForeignApplicant($testData);
            echo "✅ Valid: $passport ($description)\n";
        } catch (ValidationException $e) {
            echo "❌ Invalid: $passport ($description)\n";
        }
    }
    
    echo "\n";
    
    // Nationality code validation examples
    echo "6.3 Nationality Code Validation Examples:\n";
    echo "------------------------------------------\n";
    
    $nationalityCodes = [
        'US' => 'United States',
        'GB' => 'Great Britain',
        'FR' => 'France',
        'DE' => 'Germany',
        'CN' => 'China',
        'IN' => 'India',
        'KE' => 'Kenya',
        'ZA' => 'South Africa',
        'USA' => 'Invalid: 3 letters',
        'us' => 'Invalid: lowercase',
        'XX' => 'Invalid: not ISO code'
    ];
    
    foreach ($nationalityCodes as $code => $description) {
        $testData = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'passportNumber' => 'AB123456',
            'nationality' => $code,
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'test@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];
        
        try {
            $client->foreignApplicants()->registerForeignApplicant($testData);
            echo "✅ Valid: $code ($description)\n";
        } catch (ValidationException $e) {
            echo "❌ Invalid: $code ($description)\n";
        }
    }
    
} catch (TCUAPIException $e) {
    echo "❌ TCU API Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
} catch (\Exception $e) {
    echo "❌ Unexpected Error: " . $e->getMessage() . "\n";
}

echo "\n=== Session 7: Foreign Applicants Examples Complete ===\n";
echo "\nEndpoints Covered:\n";
echo "• 3.31 - Register Foreign Applicant\n";
echo "• 3.32 - Get Foreign Applicant Details\n";
echo "• 3.33 - Process Visa Application\n";
echo "• 3.34 - Get Visa Status\n";
echo "• 3.35 - Update Foreign Applicant Information\n";
echo "\nValidation Features:\n";
echo "• Passport number format validation (6-20 alphanumeric characters)\n";
echo "• ISO country code validation for nationality\n";
echo "• Email address validation\n";
echo "• Required field validation\n";
echo "• Data type validation\n";
echo "\nEnterprise Features:\n";
echo "• Database logging for audit trails\n";
echo "• Comprehensive error handling\n";
echo "• XML-based API communication\n";
echo "• Retry logic for network resilience\n";
echo "• Standardized response formats\n";