<?php

/**
 * TCU API Client - Session 6 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 6 endpoints (3.26-3.30)
 * of the TCU API Client. These examples demonstrate non-degree and postgraduate operations
 * including student registration, management, and advanced academic operations.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 6 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 6 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.26 REGISTER NON-DEGREE STUDENT
    // ==========================================
    echo "1. REGISTER NON-DEGREE STUDENT (3.26)\n";
    echo "=====================================\n";
    
    try {
        // Register certificate programme student
        echo "Registering certificate programme student...\n";
        $nonDegreeStudentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'certificate',
            'study_mode' => 'part_time',
            'duration_months' => 12,
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678',
            'gender' => 'M',
            'date_of_birth' => '1990-03-15',
            'entry_qualification' => 'Form Four Certificate',
            'programme_start_date' => '2025-02-01'
        ];
        
        $nonDegreeResponse = $client->nonDegree()->registerNonDegreeStudent($nonDegreeStudentData);
        echo "Response: " . json_encode($nonDegreeResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register diploma programme student
        echo "Registering diploma programme student...\n";
        $diplomaStudentData = [
            'f4indexno' => 'S0123456790',
            'firstname' => 'Jane',
            'surname' => 'Smith',
            'programme_code' => 'DIPL001',
            'institution_code' => 'INST001',
            'programme_type' => 'diploma',
            'study_mode' => 'full_time',
            'duration_months' => 24,
            'email' => 'jane.smith@example.com',
            'phone' => '+255787654321'
        ];
        
        $diplomaResponse = $client->nonDegree()->registerNonDegreeStudent($diplomaStudentData);
        echo "Response: " . json_encode($diplomaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register short course student
        echo "Registering short course student...\n";
        $shortCourseStudentData = [
            'f4indexno' => 'S0123456791',
            'firstname' => 'Peter',
            'surname' => 'Johnson',
            'programme_code' => 'SHORT001',
            'institution_code' => 'INST002',
            'programme_type' => 'short_course',
            'study_mode' => 'weekend',
            'duration_months' => 3,
            'email' => 'peter.johnson@example.com'
        ];
        
        $shortCourseResponse = $client->nonDegree()->registerNonDegreeStudent($shortCourseStudentData);
        echo "Response: " . json_encode($shortCourseResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk register non-degree students
        echo "Bulk registering non-degree students...\n";
        $bulkNonDegreeData = [
            [
                'f4indexno' => 'S0123456792',
                'firstname' => 'Mary',
                'surname' => 'Wilson',
                'programme_code' => 'CERT002',
                'institution_code' => 'INST001',
                'programme_type' => 'certificate'
            ],
            [
                'f4indexno' => 'S0123456793',
                'firstname' => 'David',
                'surname' => 'Brown',
                'programme_code' => 'PROF001',
                'institution_code' => 'INST003',
                'programme_type' => 'professional_course'
            ]
        ];
        
        $bulkNonDegreeResponse = $client->nonDegree()->bulkRegisterNonDegreeStudents($bulkNonDegreeData);
        echo "Response: " . json_encode($bulkNonDegreeResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process registration response
        if (isset($nonDegreeResponse['student_id'])) {
            echo "Non-Degree Student Registration Details:\n";
            echo "=======================================\n";
            echo "F4 Index Number: {$nonDegreeResponse['f4indexno']}\n";
            echo "Student ID: {$nonDegreeResponse['student_id']}\n";
            echo "Registered At: {$nonDegreeResponse['registered_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.27 GET NON-DEGREE STUDENT DETAILS
    // ==========================================
    echo "2. GET NON-DEGREE STUDENT DETAILS (3.27)\n";
    echo "========================================\n";
    
    try {
        // Get non-degree student details
        echo "Getting non-degree student details for S0123456789...\n";
        $nonDegreeDetailsResponse = $client->nonDegree()->getNonDegreeStudentDetails('S0123456789');
        echo "Response: " . json_encode($nonDegreeDetailsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process student details
        if (isset($nonDegreeDetailsResponse['f4indexno'])) {
            echo "Non-Degree Student Profile Summary:\n";
            echo "===================================\n";
            echo "Name: {$nonDegreeDetailsResponse['firstname']} {$nonDegreeDetailsResponse['surname']}\n";
            echo "F4 Index: {$nonDegreeDetailsResponse['f4indexno']}\n";
            echo "Programme: {$nonDegreeDetailsResponse['programme_name']} ({$nonDegreeDetailsResponse['programme_code']})\n";
            echo "Type: " . ucfirst(str_replace('_', ' ', $nonDegreeDetailsResponse['programme_type'])) . "\n";
            echo "Study Mode: " . ucfirst(str_replace('_', ' ', $nonDegreeDetailsResponse['study_mode'])) . "\n";
            echo "Institution: {$nonDegreeDetailsResponse['institution_name']} ({$nonDegreeDetailsResponse['institution_code']})\n";
            echo "Status: " . ucfirst($nonDegreeDetailsResponse['completion_status']) . "\n";
            echo "Contact: {$nonDegreeDetailsResponse['email']} | {$nonDegreeDetailsResponse['phone']}\n";
            echo "\n";
        }
        
        // Get details for multiple students
        echo "Getting details for multiple non-degree students...\n";
        $nonDegreeStudents = ['S0123456789', 'S0123456790', 'S0123456791'];
        
        foreach ($nonDegreeStudents as $f4indexno) {
            echo "Student: $f4indexno\n";
            try {
                $response = $client->nonDegree()->getNonDegreeStudentDetails($f4indexno);
                echo "  Name: {$response['firstname']} {$response['surname']}\n";
                echo "  Programme Type: {$response['programme_type']}\n";
                echo "  Status: {$response['completion_status']}\n";
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
    // 3.28 UPDATE NON-DEGREE STUDENT INFORMATION
    // ==========================================
    echo "3. UPDATE NON-DEGREE STUDENT INFORMATION (3.28)\n";
    echo "===============================================\n";
    
    try {
        // Update non-degree student contact information
        echo "Updating non-degree student contact information...\n";
        $nonDegreeUpdateData = [
            'email' => 'john.doe.updated@example.com',
            'phone' => '+255712345679',
            'study_mode' => 'full_time',
            'completion_status' => 'in_progress'
        ];
        
        $nonDegreeUpdateResponse = $client->nonDegree()->updateNonDegreeStudentInformation('S0123456789', $nonDegreeUpdateData);
        echo "Response: " . json_encode($nonDegreeUpdateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update completion status
        echo "Updating completion status for non-degree student...\n";
        $completionUpdateResponse = $client->nonDegree()->updateCompletionStatus(
            'S0123456790',
            'completed',
            'Student successfully completed all course requirements and assessments'
        );
        echo "Response: " . json_encode($completionUpdateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process update response
        if (isset($nonDegreeUpdateResponse['updated_fields'])) {
            echo "Non-Degree Student Update Summary:\n";
            echo "==================================\n";
            echo "F4 Index Number: {$nonDegreeUpdateResponse['f4indexno']}\n";
            echo "Updated Fields: " . implode(', ', $nonDegreeUpdateResponse['updated_fields']) . "\n";
            echo "Updated At: {$nonDegreeUpdateResponse['updated_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.29 REGISTER POSTGRADUATE STUDENT
    // ==========================================
    echo "4. REGISTER POSTGRADUATE STUDENT (3.29)\n";
    echo "=======================================\n";
    
    try {
        // Register masters student
        echo "Registering masters student...\n";
        $mastersStudentData = [
            'f4indexno' => 'S0123456800',
            'firstname' => 'Alice',
            'middlename' => 'Mary',
            'surname' => 'Johnson',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'study_mode' => 'full_time',
            'funding_source' => 'government',
            'research_area' => 'Machine Learning and Artificial Intelligence',
            'supervisor_staff_id' => 'STAFF001',
            'email' => 'alice.johnson@example.com',
            'phone' => '+255712345680',
            'previous_qualification' => 'Bachelor of Science in Computer Science',
            'expected_completion_date' => '2026-12-31'
        ];
        
        $mastersResponse = $client->postgraduate()->registerPostgraduateStudent($mastersStudentData);
        echo "Response: " . json_encode($mastersResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register PhD student
        echo "Registering PhD student...\n";
        $phdStudentData = [
            'f4indexno' => 'S0123456801',
            'firstname' => 'Robert',
            'surname' => 'Brown',
            'programme_code' => 'PHD001',
            'institution_code' => 'INST001',
            'study_level' => 'doctorate',
            'study_mode' => 'full_time',
            'funding_source' => 'research_grant',
            'research_area' => 'Renewable Energy Systems',
            'supervisor_staff_id' => 'STAFF002',
            'email' => 'robert.brown@example.com',
            'phone' => '+255712345681',
            'thesis_title' => 'Advanced Solar Panel Efficiency Optimization Techniques'
        ];
        
        $phdResponse = $client->postgraduate()->registerPostgraduateStudent($phdStudentData);
        echo "Response: " . json_encode($phdResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register MBA student
        echo "Registering MBA student...\n";
        $mbaStudentData = [
            'f4indexno' => 'S0123456802',
            'firstname' => 'Maria',
            'surname' => 'Garcia',
            'programme_code' => 'MBA001',
            'institution_code' => 'INST002',
            'study_level' => 'mba',
            'study_mode' => 'part_time',
            'funding_source' => 'employer',
            'email' => 'maria.garcia@example.com',
            'phone' => '+255712345682',
            'work_experience_years' => 5
        ];
        
        $mbaResponse = $client->postgraduate()->registerPostgraduateStudent($mbaStudentData);
        echo "Response: " . json_encode($mbaResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk register postgraduate students
        echo "Bulk registering postgraduate students...\n";
        $bulkPostgradData = [
            [
                'f4indexno' => 'S0123456803',
                'firstname' => 'David',
                'surname' => 'Wilson',
                'programme_code' => 'MSC002',
                'institution_code' => 'INST001',
                'study_level' => 'masters'
            ],
            [
                'f4indexno' => 'S0123456804',
                'firstname' => 'Sarah',
                'surname' => 'Davis',
                'programme_code' => 'PHD002',
                'institution_code' => 'INST002',
                'study_level' => 'doctorate'
            ]
        ];
        
        $bulkPostgradResponse = $client->postgraduate()->bulkRegisterPostgraduateStudents($bulkPostgradData);
        echo "Response: " . json_encode($bulkPostgradResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process registration response
        if (isset($mastersResponse['student_id'])) {
            echo "Postgraduate Student Registration Details:\n";
            echo "=========================================\n";
            echo "F4 Index Number: {$mastersResponse['f4indexno']}\n";
            echo "Student ID: {$mastersResponse['student_id']}\n";
            echo "Registered At: {$mastersResponse['registered_at']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.30 GET POSTGRADUATE STUDENT DETAILS
    // ==========================================
    echo "5. GET POSTGRADUATE STUDENT DETAILS (3.30)\n";
    echo "==========================================\n";
    
    try {
        // Get postgraduate student details
        echo "Getting postgraduate student details for S0123456800...\n";
        $postgradDetailsResponse = $client->postgraduate()->getPostgraduateStudentDetails('S0123456800');
        echo "Response: " . json_encode($postgradDetailsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process student details
        if (isset($postgradDetailsResponse['f4indexno'])) {
            echo "Postgraduate Student Profile Summary:\n";
            echo "====================================\n";
            echo "Name: {$postgradDetailsResponse['firstname']} {$postgradDetailsResponse['surname']}\n";
            echo "F4 Index: {$postgradDetailsResponse['f4indexno']}\n";
            echo "Programme: {$postgradDetailsResponse['programme_name']} ({$postgradDetailsResponse['programme_code']})\n";
            echo "Study Level: " . ucfirst($postgradDetailsResponse['study_level']) . "\n";
            echo "Study Mode: " . ucfirst(str_replace('_', ' ', $postgradDetailsResponse['study_mode'])) . "\n";
            echo "Institution: {$postgradDetailsResponse['institution_name']} ({$postgradDetailsResponse['institution_code']})\n";
            echo "Research Area: {$postgradDetailsResponse['research_area']}\n";
            echo "Supervisor: {$postgradDetailsResponse['supervisor_name']} ({$postgradDetailsResponse['supervisor_staff_id']})\n";
            echo "Status: " . ucfirst(str_replace('_', ' ', $postgradDetailsResponse['completion_status'])) . "\n";
            echo "Contact: {$postgradDetailsResponse['email']} | {$postgradDetailsResponse['phone']}\n";
            echo "\n";
        }
        
        // Get details for multiple postgraduate students
        echo "Getting details for multiple postgraduate students...\n";
        $postgradStudents = ['S0123456800', 'S0123456801', 'S0123456802'];
        
        foreach ($postgradStudents as $f4indexno) {
            echo "Student: $f4indexno\n";
            try {
                $response = $client->postgraduate()->getPostgraduateStudentDetails($f4indexno);
                echo "  Name: {$response['firstname']} {$response['surname']}\n";
                echo "  Study Level: {$response['study_level']}\n";
                echo "  Research Area: {$response['research_area']}\n";
                echo "  Status: {$response['completion_status']}\n";
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
    // ADDITIONAL NON-DEGREE OPERATIONS
    // ==========================================
    echo "6. ADDITIONAL NON-DEGREE OPERATIONS\n";
    echo "===================================\n";
    
    try {
        // Get non-degree students by programme
        echo "Getting non-degree students by programme CERT001...\n";
        $nonDegreeByProgramme = $client->nonDegree()->getNonDegreeStudentsByProgramme('CERT001', [
            'programme_type' => 'certificate',
            'study_mode' => 'part_time'
        ]);
        echo "Response: " . json_encode($nonDegreeByProgramme, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get non-degree students by institution
        echo "Getting non-degree students by institution INST001...\n";
        $nonDegreeByInstitution = $client->nonDegree()->getNonDegreeStudentsByInstitution('INST001');
        echo "Response: " . json_encode($nonDegreeByInstitution, JSON_PRETTY_PRINT) . "\n\n";
        
        // Search non-degree students
        echo "Searching non-degree students...\n";
        $nonDegreeSearch = $client->nonDegree()->searchNonDegreeStudents([
            'firstname' => 'John',
            'programme_type' => 'certificate'
        ]);
        echo "Response: " . json_encode($nonDegreeSearch, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get non-degree programme statistics
        echo "Getting non-degree programme statistics...\n";
        $nonDegreeStats = $client->nonDegree()->getNonDegreeProgrammeStatistics([
            'programme_type' => 'certificate'
        ]);
        echo "Response: " . json_encode($nonDegreeStats, JSON_PRETTY_PRINT) . "\n\n";
        
        // Issue non-degree certificate
        echo "Issuing non-degree certificate...\n";
        $certificateIssue = $client->nonDegree()->issueNonDegreeCertificate('S0123456789', 'completion', [
            'grade' => 'A',
            'completion_date' => '2025-01-15'
        ]);
        echo "Response: " . json_encode($certificateIssue, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get completion history
        echo "Getting completion history...\n";
        $completionHistory = $client->nonDegree()->getCompletionHistory('S0123456789');
        echo "Response: " . json_encode($completionHistory, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate non-degree programme report
        echo "Generating non-degree programme report...\n";
        $nonDegreeReport = $client->nonDegree()->generateNonDegreeProgrammeReport([
            'institution_code' => 'INST001',
            'report_type' => 'completion',
            'include_statistics' => true
        ]);
        echo "Response: " . json_encode($nonDegreeReport, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ADDITIONAL POSTGRADUATE OPERATIONS
    // ==========================================
    echo "7. ADDITIONAL POSTGRADUATE OPERATIONS\n";
    echo "====================================\n";
    
    try {
        // Get postgraduate students by programme
        echo "Getting postgraduate students by programme MSC001...\n";
        $postgradByProgramme = $client->postgraduate()->getPostgraduateStudentsByProgramme('MSC001', [
            'study_level' => 'masters',
            'study_mode' => 'full_time'
        ]);
        echo "Response: " . json_encode($postgradByProgramme, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get postgraduate students by institution
        echo "Getting postgraduate students by institution INST001...\n";
        $postgradByInstitution = $client->postgraduate()->getPostgraduateStudentsByInstitution('INST001');
        echo "Response: " . json_encode($postgradByInstitution, JSON_PRETTY_PRINT) . "\n\n";
        
        // Search postgraduate students
        echo "Searching postgraduate students...\n";
        $postgradSearch = $client->postgraduate()->searchPostgraduateStudents([
            'study_level' => 'masters',
            'supervisor_staff_id' => 'STAFF001'
        ]);
        echo "Response: " . json_encode($postgradSearch, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get postgraduate programme statistics
        echo "Getting postgraduate programme statistics...\n";
        $postgradStats = $client->postgraduate()->getPostgraduateProgrammeStatistics([
            'study_level' => 'masters'
        ]);
        echo "Response: " . json_encode($postgradStats, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update postgraduate student information
        echo "Updating postgraduate student information...\n";
        $postgradUpdate = $client->postgraduate()->updatePostgraduateStudentInformation('S0123456800', [
            'email' => 'alice.johnson.new@example.com',
            'completion_status' => 'thesis_submitted',
            'thesis_title' => 'Advanced Machine Learning Techniques in Healthcare'
        ]);
        echo "Response: " . json_encode($postgradUpdate, JSON_PRETTY_PRINT) . "\n\n";
        
        // Register thesis submission
        echo "Registering thesis submission...\n";
        $thesisSubmission = $client->postgraduate()->registerThesisSubmission('S0123456800', [
            'thesis_title' => 'Advanced Machine Learning Techniques in Healthcare',
            'submission_date' => '2025-01-15',
            'supervisor_staff_id' => 'STAFF001',
            'thesis_type' => 'dissertation',
            'page_count' => 150
        ]);
        echo "Response: " . json_encode($thesisSubmission, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update research progress
        echo "Updating research progress...\n";
        $progressUpdate = $client->postgraduate()->updateResearchProgress('S0123456800', [
            'progress_percentage' => 85,
            'milestone_status' => 'writing',
            'notes' => 'Thesis writing in progress, expected completion in 2 months'
        ]);
        echo "Response: " . json_encode($progressUpdate, JSON_PRETTY_PRINT) . "\n\n";
        
        // Assign supervisor
        echo "Assigning co-supervisor...\n";
        $supervisorAssignment = $client->postgraduate()->assignSupervisor('S0123456801', 'STAFF003', 'co_supervisor');
        echo "Response: " . json_encode($supervisorAssignment, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get supervisor assignments
        echo "Getting supervisor assignments for STAFF001...\n";
        $supervisorAssignments = $client->postgraduate()->getSupervisorAssignments('STAFF001');
        echo "Response: " . json_encode($supervisorAssignments, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate postgraduate programme report
        echo "Generating postgraduate programme report...\n";
        $postgradReport = $client->postgraduate()->generatePostgraduateProgrammeReport([
            'institution_code' => 'INST001',
            'report_type' => 'thesis_submissions',
            'include_statistics' => true
        ]);
        echo "Response: " . json_encode($postgradReport, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process postgraduate statistics
        if (isset($postgradStats['statistics'])) {
            echo "Postgraduate Statistics Summary:\n";
            echo "===============================\n";
            $stats = $postgradStats['statistics'];
            echo "Total Students: {$stats['total_students']}\n";
            
            if (isset($stats['by_study_level'])) {
                echo "By Study Level:\n";
                foreach ($stats['by_study_level'] as $level => $count) {
                    echo "- " . ucfirst($level) . ": $count\n";
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
    
    // Example 1: Invalid programme type in non-degree registration
    try {
        echo "Testing invalid programme type in non-degree registration...\n";
        $client->nonDegree()->registerNonDegreeStudent([
            'f4indexno' => 'S0123456789',
            'firstname' => 'Test',
            'surname' => 'User',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'invalid_type'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid study level in postgraduate registration
    try {
        echo "Testing invalid study level in postgraduate registration...\n";
        $client->postgraduate()->registerPostgraduateStudent([
            'f4indexno' => 'S0123456789',
            'firstname' => 'Test',
            'surname' => 'User',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'invalid_level'
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Missing research area for doctorate programme
    try {
        echo "Testing missing research area for doctorate programme...\n";
        $client->postgraduate()->registerPostgraduateStudent([
            'f4indexno' => 'S0123456789',
            'firstname' => 'Test',
            'surname' => 'User',
            'programme_code' => 'PHD001',
            'institution_code' => 'INST001',
            'study_level' => 'doctorate'
            // Missing research_area
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Invalid completion status
    try {
        echo "Testing invalid completion status...\n";
        $client->nonDegree()->updateCompletionStatus('S0123456789', 'invalid_status', 'Some reason');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Invalid supervisor staff ID
    try {
        echo "Testing invalid supervisor staff ID...\n";
        $client->postgraduate()->assignSupervisor('S0123456789', 'invalid', 'primary');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 6: Invalid progress percentage
    try {
        echo "Testing invalid progress percentage...\n";
        $client->postgraduate()->updateResearchProgress('S0123456789', ['progress_percentage' => 150]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 6 Examples Complete ===\n";
    
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