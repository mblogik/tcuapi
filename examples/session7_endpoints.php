<?php

/**
 * TCU API Client - Session 7 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 7 endpoints (3.31-3.35)
 * of the TCU API Client. These examples demonstrate foreign applicant operations
 * including registration, visa processing, document verification, and application management.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 7 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 7 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.31 REGISTER FOREIGN APPLICANT
    // ==========================================
    echo "1. REGISTER FOREIGN APPLICANT (3.31)\n";
    echo "====================================\n";
    
    try {
        // Register undergraduate foreign applicant
        echo "Registering undergraduate foreign applicant from USA...\n";
        $foreignApplicantData = [
            'passport_number' => 'A1234567',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001',
            'study_level' => 'undergraduate',
            'funding_source' => 'self_sponsored',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'visa_status' => 'pending',
            'passport_expiry_date' => '2030-12-31',
            'date_of_birth' => '1995-05-15',
            'gender' => 'M',
            'previous_qualification' => 'High School Diploma',
            'intended_start_date' => '2025-09-01'
        ];
        
        $foreignApplicantResponse = $client->foreignApplicants()->registerForeignApplicant($foreignApplicantData);
        echo "Response: " . json_encode($foreignApplicantResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register postgraduate foreign applicant
        echo "Registering postgraduate foreign applicant from Canada...\n";
        $postgradForeignData = [
            'passport_number' => 'C7654321',
            'firstname' => 'Jane',
            'surname' => 'Smith',
            'nationality' => 'CA',
            'country_of_origin' => 'CA',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'funding_source' => 'scholarship',
            'email' => 'jane.smith@example.com',
            'phone' => '+1987654321',
            'visa_status' => 'approved',
            'passport_expiry_date' => '2028-06-30',
            'date_of_birth' => '1992-08-20',
            'previous_qualification' => 'Bachelor of Science in Computer Science'
        ];
        
        $postgradForeignResponse = $client->foreignApplicants()->registerForeignApplicant($postgradForeignData);
        echo "Response: " . json_encode($postgradForeignResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register doctorate foreign applicant
        echo "Registering doctorate foreign applicant from UK...\n";
        $doctorateData = [
            'passport_number' => 'UK123456',
            'firstname' => 'David',
            'surname' => 'Johnson',
            'nationality' => 'GB',
            'country_of_origin' => 'GB',
            'programme_code' => 'PHD001',
            'institution_code' => 'INST002',
            'study_level' => 'doctorate',
            'funding_source' => 'research_grant',
            'email' => 'david.johnson@example.com',
            'phone' => '+441234567890',
            'visa_status' => 'pending',
            'passport_expiry_date' => '2032-01-15'
        ];
        
        $doctorateResponse = $client->foreignApplicants()->registerForeignApplicant($doctorateData);
        echo "Response: " . json_encode($doctorateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk register foreign applicants
        echo "Bulk registering foreign applicants...\n";
        $bulkForeignData = [
            [
                'passport_number' => 'AU987654',
                'firstname' => 'Sarah',
                'surname' => 'Wilson',
                'nationality' => 'AU',
                'country_of_origin' => 'AU',
                'programme_code' => 'BSC002',
                'institution_code' => 'INST001'
            ],
            [
                'passport_number' => 'DE456789',
                'firstname' => 'Hans',
                'surname' => 'Mueller',
                'nationality' => 'DE',
                'country_of_origin' => 'DE',
                'programme_code' => 'MSC002',
                'institution_code' => 'INST002'
            ]
        ];
        
        $bulkForeignResponse = $client->foreignApplicants()->bulkRegisterForeignApplicants($bulkForeignData);
        echo "Response: " . json_encode($bulkForeignResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process registration response
        if (isset($foreignApplicantResponse['applicant_id'])) {
            echo "Foreign Applicant Registration Details:\n";
            echo "======================================\n";
            echo "Passport Number: {$foreignApplicantResponse['passport_number']}\n";
            echo "Applicant ID: {$foreignApplicantResponse['applicant_id']}\n";
            echo "Registered At: {$foreignApplicantResponse['registered_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.32 GET FOREIGN APPLICANT DETAILS
    // ==========================================
    echo "2. GET FOREIGN APPLICANT DETAILS (3.32)\n";
    echo "=======================================\n";
    
    try {
        // Get foreign applicant details
        echo "Getting foreign applicant details for A1234567...\n";
        $foreignDetailsResponse = $client->foreignApplicants()->getForeignApplicantDetails('A1234567');
        echo "Response: " . json_encode($foreignDetailsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process applicant details
        if (isset($foreignDetailsResponse['passport_number'])) {
            echo "Foreign Applicant Profile Summary:\n";
            echo "=================================\n";
            echo "Name: {$foreignDetailsResponse['firstname']} {$foreignDetailsResponse['surname']}\n";
            echo "Passport: {$foreignDetailsResponse['passport_number']}\n";
            echo "Nationality: {$foreignDetailsResponse['nationality']}\n";
            echo "Country of Origin: {$foreignDetailsResponse['country_of_origin']}\n";
            echo "Programme: {$foreignDetailsResponse['programme_name']} ({$foreignDetailsResponse['programme_code']})\n";
            echo "Institution: {$foreignDetailsResponse['institution_name']} ({$foreignDetailsResponse['institution_code']})\n";
            echo "Study Level: " . ucfirst($foreignDetailsResponse['study_level']) . "\n";
            echo "Funding Source: " . ucfirst(str_replace('_', ' ', $foreignDetailsResponse['funding_source'])) . "\n";
            echo "Visa Status: " . ucfirst($foreignDetailsResponse['visa_status']) . "\n";
            echo "Application Status: " . ucfirst(str_replace('_', ' ', $foreignDetailsResponse['application_status'])) . "\n";
            echo "Contact: {$foreignDetailsResponse['email']} | {$foreignDetailsResponse['phone']}\n";
            echo "\n";
        }
        
        // Get details for multiple foreign applicants
        echo "Getting details for multiple foreign applicants...\n";
        $foreignApplicants = ['A1234567', 'C7654321', 'UK123456'];
        
        foreach ($foreignApplicants as $passportNumber) {
            echo "Applicant: $passportNumber\n";
            try {
                $response = $client->foreignApplicants()->getForeignApplicantDetails($passportNumber);
                echo "  Name: {$response['firstname']} {$response['surname']}\n";
                echo "  Nationality: {$response['nationality']}\n";
                echo "  Visa Status: {$response['visa_status']}\n";
                echo "  Application Status: {$response['application_status']}\n";
            } catch (ValidationException $e) {
                echo "  Error: " . $e->getMessage() . "\n";
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.33 UPDATE FOREIGN APPLICANT INFORMATION
    // ==========================================
    echo "3. UPDATE FOREIGN APPLICANT INFORMATION (3.33)\n";
    echo "==============================================\n";
    
    try {
        // Update foreign applicant contact information
        echo "Updating foreign applicant contact information...\n";
        $foreignUpdateData = [
            'email' => 'john.doe.updated@example.com',
            'phone' => '+1234567891',
            'visa_status' => 'approved',
            'application_status' => 'approved',
            'passport_expiry_date' => '2031-12-31'
        ];
        
        $foreignUpdateResponse = $client->foreignApplicants()->updateForeignApplicantInformation('A1234567', $foreignUpdateData);
        echo "Response: " . json_encode($foreignUpdateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update application status
        echo "Updating application status for foreign applicant...\n";
        $statusUpdateResponse = $client->foreignApplicants()->updateApplicationStatus(
            'C7654321',
            'conditional_offer',
            'Applicant meets academic requirements but needs to complete language proficiency test'
        );
        echo "Response: " . json_encode($statusUpdateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process update response
        if (isset($foreignUpdateResponse['updated_fields'])) {
            echo "Foreign Applicant Update Summary:\n";
            echo "================================\n";
            echo "Passport Number: {$foreignUpdateResponse['passport_number']}\n";
            echo "Updated Fields: " . implode(', ', $foreignUpdateResponse['updated_fields']) . "\n";
            echo "Updated At: {$foreignUpdateResponse['updated_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.34 PROCESS VISA APPLICATION
    // ==========================================
    echo "4. PROCESS VISA APPLICATION (3.34)\n";
    echo "=================================\n";
    
    try {
        // Process student visa application
        echo "Processing student visa application...\n";
        $visaData = [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-15',
            'intended_departure_date' => '2029-07-31',
            'duration_of_stay_days' => 1440,
            'purpose_of_visit' => 'Pursuing Bachelor of Science in Computer Science',
            'accommodation_details' => 'University dormitory',
            'financial_support_proof' => 'Bank statement and scholarship letter',
            'health_insurance' => 'International student health insurance'
        ];
        
        $visaResponse = $client->foreignApplicants()->processVisaApplication('A1234567', $visaData);
        echo "Response: " . json_encode($visaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process business visa application
        echo "Processing business visa application...\n";
        $businessVisaData = [
            'visa_type' => 'business',
            'intended_arrival_date' => '2025-03-01',
            'intended_departure_date' => '2025-03-15',
            'duration_of_stay_days' => 14,
            'purpose_of_visit' => 'Academic conference and research collaboration',
            'inviting_organization' => 'University of Dar es Salaam'
        ];
        
        $businessVisaResponse = $client->foreignApplicants()->processVisaApplication('UK123456', $businessVisaData);
        echo "Response: " . json_encode($businessVisaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process multiple entry visa
        echo "Processing multiple entry visa application...\n";
        $multipleVisaData = [
            'visa_type' => 'multiple_entry',
            'intended_arrival_date' => '2025-09-01',
            'intended_departure_date' => '2027-08-31',
            'duration_of_stay_days' => 730,
            'purpose_of_visit' => 'PhD research with multiple field work trips'
        ];
        
        $multipleVisaResponse = $client->foreignApplicants()->processVisaApplication('C7654321', $multipleVisaData);
        echo "Response: " . json_encode($multipleVisaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process visa response
        if (isset($visaResponse['visa_application_id'])) {
            echo "Visa Application Details:\n";
            echo "========================\n";
            echo "Passport Number: {$visaResponse['passport_number']}\n";
            echo "Application ID: {$visaResponse['visa_application_id']}\n";
            echo "Visa Status: {$visaResponse['visa_status']}\n";
            echo "Processed At: {$visaResponse['processed_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.35 GET VISA STATUS
    // ==========================================
    echo "5. GET VISA STATUS (3.35)\n";
    echo "========================\n";
    
    try {
        // Get visa status
        echo "Getting visa status for A1234567...\n";
        $visaStatusResponse = $client->foreignApplicants()->getVisaStatus('A1234567');
        echo "Response: " . json_encode($visaStatusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process visa status
        if (isset($visaStatusResponse['visa_status'])) {
            echo "Visa Status Summary:\n";
            echo "===================\n";
            echo "Passport Number: {$visaStatusResponse['passport_number']}\n";
            echo "Visa Status: " . ucfirst($visaStatusResponse['visa_status']) . "\n";
            echo "Visa Type: " . ucfirst($visaStatusResponse['visa_type']) . "\n";
            echo "Application Date: {$visaStatusResponse['visa_application_date']}\n";
            
            if (isset($visaStatusResponse['visa_approval_date'])) {
                echo "Approval Date: {$visaStatusResponse['visa_approval_date']}\n";
            }
            
            if (isset($visaStatusResponse['visa_expiry_date'])) {
                echo "Expiry Date: {$visaStatusResponse['visa_expiry_date']}\n";
            }
            echo "\n";
        }
        
        // Get visa status for multiple applicants
        echo "Getting visa status for multiple applicants...\n";
        $visaApplicants = ['A1234567', 'C7654321', 'UK123456'];
        
        foreach ($visaApplicants as $passportNumber) {
            echo "Applicant: $passportNumber\n";
            try {
                $response = $client->foreignApplicants()->getVisaStatus($passportNumber);
                echo "  Visa Status: {$response['visa_status']}\n";
                echo "  Visa Type: {$response['visa_type']}\n";
                
                if (isset($response['visa_expiry_date'])) {
                    echo "  Expiry Date: {$response['visa_expiry_date']}\n";
                }
            } catch (ValidationException $e) {
                echo "  Error: " . $e->getMessage() . "\n";
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ADDITIONAL FOREIGN APPLICANT OPERATIONS
    // ==========================================
    echo "6. ADDITIONAL FOREIGN APPLICANT OPERATIONS\n";
    echo "==========================================\n";
    
    try {
        // Get foreign applicants by programme
        echo "Getting foreign applicants by programme BSC001...\n";
        $foreignByProgramme = $client->foreignApplicants()->getForeignApplicantsByProgramme('BSC001', [
            'nationality' => 'US',
            'visa_status' => 'approved'
        ]);
        echo "Response: " . json_encode($foreignByProgramme, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get foreign applicants by institution
        echo "Getting foreign applicants by institution INST001...\n";
        $foreignByInstitution = $client->foreignApplicants()->getForeignApplicantsByInstitution('INST001');
        echo "Response: " . json_encode($foreignByInstitution, JSON_PRETTY_PRINT) . "\n\n";
        
        // Search foreign applicants
        echo "Searching foreign applicants...\n";
        $foreignSearch = $client->foreignApplicants()->searchForeignApplicants([
            'nationality' => 'US',
            'programme_code' => 'BSC001',
            'visa_status' => 'approved'
        ]);
        echo "Response: " . json_encode($foreignSearch, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get foreign applicant statistics
        echo "Getting foreign applicant statistics...\n";
        $foreignStats = $client->foreignApplicants()->getForeignApplicantStatistics([
            'programme_code' => 'BSC001'
        ]);
        echo "Response: " . json_encode($foreignStats, JSON_PRETTY_PRINT) . "\n\n";
        
        // Submit document verification
        echo "Submitting document verification...\n";
        $documentVerification = $client->foreignApplicants()->submitDocumentVerification('A1234567', [
            [
                'document_type' => 'passport',
                'document_number' => 'A1234567',
                'issuing_authority' => 'US Department of State'
            ],
            [
                'document_type' => 'academic_transcript',
                'document_number' => 'TRANS001',
                'issuing_authority' => 'ABC High School'
            ],
            [
                'document_type' => 'language_certificate',
                'document_number' => 'TOEFL123',
                'issuing_authority' => 'Educational Testing Service'
            ]
        ]);
        echo "Response: " . json_encode($documentVerification, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get application history
        echo "Getting application history...\n";
        $applicationHistory = $client->foreignApplicants()->getApplicationHistory('A1234567');
        echo "Response: " . json_encode($applicationHistory, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get visa requirements
        echo "Getting visa requirements for US nationals studying undergraduate...\n";
        $visaRequirements = $client->foreignApplicants()->getVisaRequirements('US', 'undergraduate');
        echo "Response: " . json_encode($visaRequirements, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate foreign applicant report
        echo "Generating foreign applicant report...\n";
        $foreignReport = $client->foreignApplicants()->generateForeignApplicantReport([
            'institution_code' => 'INST001',
            'report_type' => 'visa_status',
            'include_statistics' => true
        ]);
        echo "Response: " . json_encode($foreignReport, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // VISA PROCESSING WORKFLOW EXAMPLES
    // ==========================================
    echo "7. VISA PROCESSING WORKFLOW EXAMPLES\n";
    echo "====================================\n";
    
    try {
        // Complete visa application workflow
        echo "Complete visa application workflow for new applicant...\n";
        
        // Step 1: Register foreign applicant
        $newApplicantData = [
            'passport_number' => 'FR789012',
            'firstname' => 'Marie',
            'surname' => 'Dubois',
            'nationality' => 'FR',
            'country_of_origin' => 'FR',
            'programme_code' => 'MSC003',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'funding_source' => 'government',
            'email' => 'marie.dubois@example.com',
            'phone' => '+33123456789'
        ];
        
        $newApplicantResponse = $client->foreignApplicants()->registerForeignApplicant($newApplicantData);
        echo "Step 1 - Registration: " . json_encode($newApplicantResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Step 2: Submit documents
        $documentSubmission = $client->foreignApplicants()->submitDocumentVerification('FR789012', [
            [
                'document_type' => 'passport',
                'document_number' => 'FR789012'
            ],
            [
                'document_type' => 'degree_certificate',
                'document_number' => 'DEGREE001'
            ],
            [
                'document_type' => 'financial_statement',
                'document_number' => 'BANK001'
            ]
        ]);
        echo "Step 2 - Document Submission: " . json_encode($documentSubmission, JSON_PRETTY_PRINT) . "\n\n";
        
        // Step 3: Process visa application
        $visaApplication = $client->foreignApplicants()->processVisaApplication('FR789012', [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-09-01',
            'intended_departure_date' => '2027-08-31',
            'duration_of_stay_days' => 730
        ]);
        echo "Step 3 - Visa Application: " . json_encode($visaApplication, JSON_PRETTY_PRINT) . "\n\n";
        
        // Step 4: Update application status
        $statusUpdate = $client->foreignApplicants()->updateApplicationStatus(
            'FR789012',
            'approved',
            'All documents verified and visa approved'
        );
        echo "Step 4 - Status Update: " . json_encode($statusUpdate, JSON_PRETTY_PRINT) . "\n\n";
        
        // Step 5: Get final visa status
        $finalVisaStatus = $client->foreignApplicants()->getVisaStatus('FR789012');
        echo "Step 5 - Final Visa Status: " . json_encode($finalVisaStatus, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ERROR HANDLING EXAMPLES
    // ==========================================
    echo "8. ERROR HANDLING EXAMPLES\n";
    echo "==========================\n";
    
    // Example 1: Invalid passport number format
    try {
        echo "Testing invalid passport number format...\n";
        $client->foreignApplicants()->registerForeignApplicant([
            'passport_number' => 'invalid',
            'firstname' => 'Test',
            'surname' => 'User',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid nationality format
    try {
        echo "Testing invalid nationality format...\n";
        $client->foreignApplicants()->registerForeignApplicant([
            'passport_number' => 'A1234567',
            'firstname' => 'Test',
            'surname' => 'User',
            'nationality' => 'invalid',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Invalid visa date order
    try {
        echo "Testing invalid visa date order...\n";
        $client->foreignApplicants()->processVisaApplication('A1234567', [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-01',
            'intended_departure_date' => '2025-07-31' // Before arrival
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Invalid visa duration
    try {
        echo "Testing invalid visa duration...\n";
        $client->foreignApplicants()->processVisaApplication('A1234567', [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-01',
            'intended_departure_date' => '2025-08-02',
            'duration_of_stay_days' => 2000 // Too long
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Empty document verification
    try {
        echo "Testing empty document verification...\n";
        $client->foreignApplicants()->submitDocumentVerification('A1234567', []);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 6: Invalid application status
    try {
        echo "Testing invalid application status...\n";
        $client->foreignApplicants()->updateApplicationStatus('A1234567', 'invalid_status', 'Some reason');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 7 Examples Complete ===\n";
    
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