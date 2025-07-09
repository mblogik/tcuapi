<?php

/**
 * TCU API Client - Session 5 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 5 endpoints (3.21-3.25)
 * of the TCU API Client. These examples demonstrate graduate and staff operations
 * including registration, management, and verification.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 5 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 5 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.21 REGISTER GRADUATE
    // ==========================================
    echo "1. REGISTER GRADUATE (3.21)\n";
    echo "===========================\n";
    
    try {
        // Register a single graduate
        echo "Registering graduate S0123456789...\n";
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'upper_second',
            'email' => 'john.doe@email.com',
            'phone' => '+255712345678',
            'gender' => 'M',
            'date_of_birth' => '1995-05-15',
            'graduation_date' => '2024-11-15',
            'thesis_title' => 'Machine Learning Applications in Agriculture'
        ];
        
        $graduateResponse = $client->graduates()->registerGraduate($graduateData);
        echo "Response: " . json_encode($graduateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register another graduate with different classification
        echo "Registering graduate with first class honors...\n";
        $graduateData2 = [
            'f4indexno' => 'S0123456790',
            'firstname' => 'Jane',
            'surname' => 'Smith',
            'programme_code' => 'PROG002',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'first_class',
            'email' => 'jane.smith@email.com',
            'phone' => '+255787654321'
        ];
        
        $graduateResponse2 = $client->graduates()->registerGraduate($graduateData2);
        echo "Response: " . json_encode($graduateResponse2, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk register graduates
        echo "Bulk registering multiple graduates...\n";
        $graduatesData = [
            [
                'f4indexno' => 'S0123456791',
                'firstname' => 'Peter',
                'surname' => 'Johnson',
                'programme_code' => 'PROG003',
                'institution_code' => 'INST002',
                'graduation_year' => 2024,
                'degree_classification' => 'lower_second'
            ],
            [
                'f4indexno' => 'S0123456792',
                'firstname' => 'Mary',
                'surname' => 'Wilson',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001',
                'graduation_year' => 2024,
                'degree_classification' => 'distinction'
            ]
        ];
        
        $bulkGraduateResponse = $client->graduates()->bulkRegisterGraduates($graduatesData);
        echo "Response: " . json_encode($bulkGraduateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process graduate registration response
        if (isset($graduateResponse['graduate_id'])) {
            echo "Graduate Registration Details:\n";
            echo "=============================\n";
            echo "F4 Index Number: {$graduateResponse['f4indexno']}\n";
            echo "Graduate ID: {$graduateResponse['graduate_id']}\n";
            echo "Registered At: {$graduateResponse['registered_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.22 GET GRADUATE DETAILS
    // ==========================================
    echo "2. GET GRADUATE DETAILS (3.22)\n";
    echo "==============================\n";
    
    try {
        // Get graduate details
        echo "Getting graduate details for S0123456789...\n";
        $graduateDetailsResponse = $client->graduates()->getGraduateDetails('S0123456789');
        echo "Response: " . json_encode($graduateDetailsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process graduate details
        if (isset($graduateDetailsResponse['f4indexno'])) {
            echo "Graduate Profile Summary:\n";
            echo "========================\n";
            echo "Name: {$graduateDetailsResponse['firstname']} {$graduateDetailsResponse['surname']}\n";
            echo "F4 Index: {$graduateDetailsResponse['f4indexno']}\n";
            echo "Programme: {$graduateDetailsResponse['programme_name']} ({$graduateDetailsResponse['programme_code']})\n";
            echo "Institution: {$graduateDetailsResponse['institution_name']} ({$graduateDetailsResponse['institution_code']})\n";
            echo "Graduation Year: {$graduateDetailsResponse['graduation_year']}\n";
            echo "Classification: " . ucfirst(str_replace('_', ' ', $graduateDetailsResponse['degree_classification'])) . "\n";
            echo "Contact: {$graduateDetailsResponse['email']} | {$graduateDetailsResponse['phone']}\n";
            echo "\n";
        }
        
        // Get graduate details for multiple graduates
        echo "Getting details for multiple graduates...\n";
        $graduates = ['S0123456789', 'S0123456790', 'S0123456791'];
        
        foreach ($graduates as $f4indexno) {
            echo "Graduate: $f4indexno\n";
            try {
                $response = $client->graduates()->getGraduateDetails($f4indexno);
                echo "  Name: {$response['firstname']} {$response['surname']}\n";
                echo "  Classification: {$response['degree_classification']}\n";
                echo "  Graduation Year: {$response['graduation_year']}\n";
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
    // 3.23 UPDATE GRADUATE INFORMATION
    // ==========================================
    echo "3. UPDATE GRADUATE INFORMATION (3.23)\n";
    echo "=====================================\n";
    
    try {
        // Update graduate contact information
        echo "Updating graduate contact information...\n";
        $updateData = [
            'email' => 'john.doe.updated@email.com',
            'phone' => '+255712345679',
            'current_address' => 'New Address, Dar es Salaam',
            'employment_status' => 'employed',
            'employer' => 'Tech Company Ltd'
        ];
        
        $updateResponse = $client->graduates()->updateGraduateInformation('S0123456789', $updateData);
        echo "Response: " . json_encode($updateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update degree classification (correction)
        echo "Updating degree classification (correction)...\n";
        $classificationUpdate = [
            'degree_classification' => 'first_class',
            'classification_change_reason' => 'Grade recalculation after appeal',
            'updated_by' => 'Academic Registrar'
        ];
        
        $classificationResponse = $client->graduates()->updateGraduateInformation('S0123456790', $classificationUpdate);
        echo "Response: " . json_encode($classificationResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process update response
        if (isset($updateResponse['updated_fields'])) {
            echo "Update Summary:\n";
            echo "==============\n";
            echo "F4 Index Number: {$updateResponse['f4indexno']}\n";
            echo "Updated Fields: " . implode(', ', $updateResponse['updated_fields']) . "\n";
            echo "Updated At: {$updateResponse['updated_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.24 REGISTER STAFF MEMBER
    // ==========================================
    echo "4. REGISTER STAFF MEMBER (3.24)\n";
    echo "===============================\n";
    
    try {
        // Register academic staff member
        echo "Registering academic staff member...\n";
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'Dr. Alice',
            'surname' => 'Johnson',
            'position' => 'senior_lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science',
            'employment_status' => 'permanent',
            'qualification_level' => 'doctorate',
            'email' => 'alice.johnson@university.ac.tz',
            'phone' => '+255712345680',
            'specialization' => 'Artificial Intelligence',
            'date_of_employment' => '2020-01-15',
            'salary_scale' => 'PUTS4'
        ];
        
        $staffResponse = $client->staff()->registerStaffMember($staffData);
        echo "Response: " . json_encode($staffResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register administrative staff member
        echo "Registering administrative staff member...\n";
        $adminStaffData = [
            'staff_id' => 'ADMIN001',
            'firstname' => 'Robert',
            'surname' => 'Brown',
            'position' => 'registrar',
            'institution_code' => 'INST001',
            'department' => 'Academic Affairs',
            'employment_status' => 'permanent',
            'qualification_level' => 'masters',
            'email' => 'robert.brown@university.ac.tz',
            'phone' => '+255712345681'
        ];
        
        $adminStaffResponse = $client->staff()->registerStaffMember($adminStaffData);
        echo "Response: " . json_encode($adminStaffResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk register staff members
        echo "Bulk registering multiple staff members...\n";
        $staffMembers = [
            [
                'staff_id' => 'STAFF002',
                'firstname' => 'Maria',
                'surname' => 'Garcia',
                'position' => 'lecturer',
                'institution_code' => 'INST001',
                'department' => 'Mathematics'
            ],
            [
                'staff_id' => 'STAFF003',
                'firstname' => 'David',
                'surname' => 'Wilson',
                'position' => 'professor',
                'institution_code' => 'INST001',
                'department' => 'Physics'
            ]
        ];
        
        $bulkStaffResponse = $client->staff()->bulkRegisterStaff($staffMembers);
        echo "Response: " . json_encode($bulkStaffResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process staff registration response
        if (isset($staffResponse['staff_id'])) {
            echo "Staff Registration Details:\n";
            echo "==========================\n";
            echo "Staff ID: {$staffResponse['staff_id']}\n";
            echo "Registration ID: {$staffResponse['registration_id']}\n";
            echo "Registered At: {$staffResponse['registered_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.25 GET STAFF DETAILS
    // ==========================================
    echo "5. GET STAFF DETAILS (3.25)\n";
    echo "===========================\n";
    
    try {
        // Get staff details
        echo "Getting staff details for STAFF001...\n";
        $staffDetailsResponse = $client->staff()->getStaffDetails('STAFF001');
        echo "Response: " . json_encode($staffDetailsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process staff details
        if (isset($staffDetailsResponse['staff_id'])) {
            echo "Staff Profile Summary:\n";
            echo "=====================\n";
            echo "Name: {$staffDetailsResponse['firstname']} {$staffDetailsResponse['surname']}\n";
            echo "Staff ID: {$staffDetailsResponse['staff_id']}\n";
            echo "Position: " . ucfirst(str_replace('_', ' ', $staffDetailsResponse['position'])) . "\n";
            echo "Department: {$staffDetailsResponse['department']}\n";
            echo "Institution: {$staffDetailsResponse['institution_code']}\n";
            echo "Employment Status: " . ucfirst($staffDetailsResponse['employment_status']) . "\n";
            echo "Qualification: " . ucfirst($staffDetailsResponse['qualification_level']) . "\n";
            echo "Contact: {$staffDetailsResponse['email']} | {$staffDetailsResponse['phone']}\n";
            echo "\n";
        }
        
        // Get staff details for multiple staff members
        echo "Getting details for multiple staff members...\n";
        $staffMembers = ['STAFF001', 'ADMIN001', 'STAFF002'];
        
        foreach ($staffMembers as $staffId) {
            echo "Staff ID: $staffId\n";
            try {
                $response = $client->staff()->getStaffDetails($staffId);
                echo "  Name: {$response['firstname']} {$response['surname']}\n";
                echo "  Position: {$response['position']}\n";
                echo "  Department: {$response['department']}\n";
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
    // ADDITIONAL GRADUATE OPERATIONS
    // ==========================================
    echo "6. ADDITIONAL GRADUATE OPERATIONS\n";
    echo "=================================\n";
    
    try {
        // Get graduates by programme
        echo "Getting graduates by programme PROG001...\n";
        $graduatesByProgramme = $client->graduates()->getGraduatesByProgramme('PROG001', [
            'graduation_year' => 2024,
            'degree_classification' => 'first_class'
        ]);
        echo "Response: " . json_encode($graduatesByProgramme, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get graduates by institution
        echo "Getting graduates by institution INST001...\n";
        $graduatesByInstitution = $client->graduates()->getGraduatesByInstitution('INST001');
        echo "Response: " . json_encode($graduatesByInstitution, JSON_PRETTY_PRINT) . "\n\n";
        
        // Search graduates
        echo "Searching graduates by name 'John'...\n";
        $graduateSearch = $client->graduates()->searchGraduates([
            'firstname' => 'John',
            'graduation_year' => 2024
        ]);
        echo "Response: " . json_encode($graduateSearch, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get graduate statistics
        echo "Getting graduate statistics...\n";
        $graduateStats = $client->graduates()->getGraduateStatistics([
            'programme_code' => 'PROG001',
            'graduation_year' => 2024
        ]);
        echo "Response: " . json_encode($graduateStats, JSON_PRETTY_PRINT) . "\n\n";
        
        // Verify graduate credentials
        echo "Verifying graduate credentials...\n";
        $credentialVerification = $client->graduates()->verifyGraduateCredentials('S0123456789', [
            'certificate_number' => 'CERT123456',
            'verification_type' => 'employment'
        ]);
        echo "Response: " . json_encode($credentialVerification, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate graduate certificate
        echo "Generating graduate certificate...\n";
        $certificateGeneration = $client->graduates()->generateGraduateCertificate('S0123456789', 'completion');
        echo "Response: " . json_encode($certificateGeneration, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ADDITIONAL STAFF OPERATIONS
    // ==========================================
    echo "7. ADDITIONAL STAFF OPERATIONS\n";
    echo "==============================\n";
    
    try {
        // Get staff by institution
        echo "Getting staff by institution INST001...\n";
        $staffByInstitution = $client->staff()->getStaffByInstitution('INST001', [
            'position' => 'lecturer',
            'employment_status' => 'permanent'
        ]);
        echo "Response: " . json_encode($staffByInstitution, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get staff by department
        echo "Getting staff by department...\n";
        $staffByDepartment = $client->staff()->getStaffByDepartment('INST001', 'Computer Science');
        echo "Response: " . json_encode($staffByDepartment, JSON_PRETTY_PRINT) . "\n\n";
        
        // Search staff
        echo "Searching staff by criteria...\n";
        $staffSearch = $client->staff()->searchStaff([
            'position' => 'lecturer',
            'institution_code' => 'INST001'
        ]);
        echo "Response: " . json_encode($staffSearch, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get staff statistics
        echo "Getting staff statistics...\n";
        $staffStats = $client->staff()->getStaffStatistics([
            'institution_code' => 'INST001'
        ]);
        echo "Response: " . json_encode($staffStats, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update staff information
        echo "Updating staff information...\n";
        $staffUpdate = $client->staff()->updateStaffInformation('STAFF001', [
            'email' => 'alice.johnson.new@university.ac.tz',
            'position' => 'associate_professor',
            'salary_scale' => 'PUTS5'
        ]);
        echo "Response: " . json_encode($staffUpdate, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update staff employment status
        echo "Updating staff employment status...\n";
        $employmentUpdate = $client->staff()->updateStaffEmploymentStatus(
            'STAFF002',
            'contract',
            'Contract renewal for 2 years',
            ['contract_end_date' => '2027-01-31']
        );
        echo "Response: " . json_encode($employmentUpdate, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get staff employment history
        echo "Getting staff employment history...\n";
        $employmentHistory = $client->staff()->getStaffEmploymentHistory('STAFF001');
        echo "Response: " . json_encode($employmentHistory, JSON_PRETTY_PRINT) . "\n\n";
        
        // Verify staff credentials
        echo "Verifying staff credentials...\n";
        $staffCredentialVerification = $client->staff()->verifyStaffCredentials('STAFF001', [
            'certificate_number' => 'PHD123456',
            'verification_type' => 'academic'
        ]);
        echo "Response: " . json_encode($staffCredentialVerification, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate staff report
        echo "Generating staff report...\n";
        $staffReport = $client->staff()->generateStaffReport([
            'institution_code' => 'INST001',
            'report_type' => 'summary',
            'include_statistics' => true
        ]);
        echo "Response: " . json_encode($staffReport, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process staff statistics
        if (isset($staffStats['statistics'])) {
            echo "Staff Statistics Summary:\n";
            echo "========================\n";
            $stats = $staffStats['statistics'];
            echo "Total Staff: {$stats['total_staff']}\n";
            
            if (isset($stats['by_position'])) {
                echo "By Position:\n";
                foreach ($stats['by_position'] as $position => $count) {
                    echo "- " . ucfirst(str_replace('_', ' ', $position)) . ": $count\n";
                }
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ERROR HANDLING EXAMPLES
    // ==========================================
    echo "8. ERROR HANDLING EXAMPLES\n";
    echo "==========================\n";
    
    // Example 1: Invalid F4 index number in graduate registration
    try {
        echo "Testing invalid F4 index number in graduate registration...\n";
        $client->graduates()->registerGraduate([
            'f4indexno' => 'invalid_f4',
            'firstname' => 'Test',
            'surname' => 'User',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid staff ID in staff registration
    try {
        echo "Testing invalid staff ID in staff registration...\n";
        $client->staff()->registerStaffMember([
            'staff_id' => 'invalid',
            'firstname' => 'Test',
            'surname' => 'Staff',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Test Department'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Empty update data
    try {
        echo "Testing empty update data in graduate update...\n";
        $client->graduates()->updateGraduateInformation('S0123456789', []);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Invalid degree classification
    try {
        echo "Testing invalid degree classification...\n";
        $client->graduates()->registerGraduate([
            'f4indexno' => 'S0123456789',
            'firstname' => 'Test',
            'surname' => 'User',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'invalid_classification'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Invalid staff position
    try {
        echo "Testing invalid staff position...\n";
        $client->staff()->registerStaffMember([
            'staff_id' => 'STAFF999',
            'firstname' => 'Test',
            'surname' => 'Staff',
            'position' => 'invalid_position',
            'institution_code' => 'INST001',
            'department' => 'Test Department'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 5 Examples Complete ===\n";
    
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