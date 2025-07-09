<?php

/**
 * TCU API Client - Session 4 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 4 endpoints (3.16-3.20)
 * of the TCU API Client. These examples demonstrate verification and enrollment operations
 * including document verification, certificate validation, and student enrollment.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 4 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 4 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.16 VERIFY STUDENT DOCUMENTS
    // ==========================================
    echo "1. VERIFY STUDENT DOCUMENTS (3.16)\n";
    echo "==================================\n";
    
    try {
        // Verify multiple documents for a student
        echo "Verifying student documents for S0123456789...\n";
        $documents = [
            [
                'document_type' => 'certificate',
                'document_number' => 'CERT123456',
                'document_data' => 'base64_encoded_certificate_data_here'
            ],
            [
                'document_type' => 'transcript',
                'document_number' => 'TRANS789012',
                'document_data' => 'base64_encoded_transcript_data_here'
            ],
            [
                'document_type' => 'birth_certificate',
                'document_number' => 'BIRTH345678',
                'document_data' => 'base64_encoded_birth_certificate_data_here'
            ]
        ];
        
        $verificationResponse = $client->verification()->verifyStudentDocuments('S0123456789', $documents);
        echo "Response: " . json_encode($verificationResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process verification results
        if (isset($verificationResponse['verification_results'])) {
            echo "Verification Results Summary:\n";
            echo "============================\n";
            
            foreach ($verificationResponse['verification_results'] as $result) {
                echo "- Document Type: {$result['document_type']}\n";
                echo "  Document Number: {$result['document_number']}\n";
                echo "  Status: {$result['verification_status']}\n";
                echo "  Verified At: {$result['verified_at']}\n";
                if (isset($result['verification_notes'])) {
                    echo "  Notes: {$result['verification_notes']}\n";
                }
                echo "\n";
            }
        }
        
        // Verify a single document using convenience method
        echo "Verifying single document using convenience method...\n";
        $singleDocResponse = $client->verification()->verifySingleDocument(
            'S0123456790',
            'certificate',
            'CERT654321',
            'base64_encoded_certificate_data_here',
            ['issuing_authority' => 'NECTA', 'year' => '2023']
        );
        echo "Response: " . json_encode($singleDocResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.17 GET VERIFICATION STATUS
    // ==========================================
    echo "2. GET VERIFICATION STATUS (3.17)\n";
    echo "=================================\n";
    
    try {
        // Get verification status for a student
        echo "Getting verification status for S0123456789...\n";
        $statusResponse = $client->verification()->getVerificationStatus('S0123456789');
        echo "Response: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process verification status
        if (isset($statusResponse['verification_status'])) {
            echo "Verification Status Details:\n";
            echo "==========================\n";
            echo "F4 Index Number: {$statusResponse['f4indexno']}\n";
            echo "Overall Status: {$statusResponse['verification_status']}\n";
            echo "Verified At: {$statusResponse['verified_at']}\n";
            
            if (isset($statusResponse['documents'])) {
                echo "Document Status:\n";
                foreach ($statusResponse['documents'] as $doc) {
                    echo "- {$doc['document_type']} ({$doc['document_number']}): {$doc['status']}\n";
                }
            }
            echo "\n";
        }
        
        // Get verification status for multiple students
        echo "Getting verification status for multiple students...\n";
        $students = ['S0123456789', 'S0123456790', 'S0123456791'];
        
        foreach ($students as $f4indexno) {
            echo "Student: $f4indexno\n";
            try {
                $response = $client->verification()->getVerificationStatus($f4indexno);
                echo "  Status: " . $response['verification_status'] . "\n";
                if (isset($response['verified_at'])) {
                    echo "  Verified At: {$response['verified_at']}\n";
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
    // 3.18 VALIDATE CERTIFICATE
    // ==========================================
    echo "3. VALIDATE CERTIFICATE (3.18)\n";
    echo "==============================\n";
    
    try {
        // Validate Form Four certificate
        echo "Validating Form Four certificate...\n";
        $certificateResponse = $client->verification()->validateCertificate(
            'CERT123456',
            'form_four',
            ['year' => '2023', 'school' => 'Kilimanjaro Secondary School']
        );
        echo "Response: " . json_encode($certificateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Validate Form Six certificate
        echo "Validating Form Six certificate...\n";
        $form6Response = $client->verification()->validateCertificate(
            'ACSEE789012',
            'form_six',
            ['year' => '2025', 'school' => 'Mwenge Catholic Secondary School']
        );
        echo "Response: " . json_encode($form6Response, JSON_PRETTY_PRINT) . "\n\n";
        
        // Validate degree certificate
        echo "Validating degree certificate...\n";
        $degreeResponse = $client->verification()->validateCertificate(
            'DEG345678',
            'degree',
            ['university' => 'University of Dar es Salaam', 'year' => '2024']
        );
        echo "Response: " . json_encode($degreeResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process certificate validation results
        if (isset($certificateResponse['validation_status'])) {
            echo "Certificate Validation Summary:\n";
            echo "==============================\n";
            echo "Certificate Number: {$certificateResponse['certificate_number']}\n";
            echo "Certificate Type: {$certificateResponse['certificate_type']}\n";
            echo "Validation Status: {$certificateResponse['validation_status']}\n";
            echo "Validated At: {$certificateResponse['validated_at']}\n";
            
            if (isset($certificateResponse['certificate_details'])) {
                echo "Certificate Details:\n";
                foreach ($certificateResponse['certificate_details'] as $key => $value) {
                    echo "- " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
                }
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.19 SUBMIT DOCUMENT RE-VERIFICATION
    // ==========================================
    echo "4. SUBMIT DOCUMENT RE-VERIFICATION (3.19)\n";
    echo "=========================================\n";
    
    try {
        // Submit documents for re-verification
        echo "Submitting documents for re-verification...\n";
        $reVerificationDocuments = [
            [
                'document_id' => 'DOC001',
                'document_type' => 'certificate'
            ],
            [
                'document_id' => 'DOC002',
                'document_type' => 'transcript'
            ]
        ];
        
        $reVerificationResponse = $client->verification()->submitDocumentReVerification(
            'S0123456789',
            $reVerificationDocuments,
            'Documents were damaged during initial verification process and need to be re-verified with updated copies.'
        );
        echo "Response: " . json_encode($reVerificationResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Re-verify single document using convenience method
        echo "Re-verifying single document using convenience method...\n";
        $singleReVerificationResponse = $client->verification()->reVerifySingleDocument(
            'S0123456790',
            'DOC003',
            'birth_certificate',
            'Original document was illegible, submitting clearer copy for re-verification.'
        );
        echo "Response: " . json_encode($singleReVerificationResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process re-verification response
        if (isset($reVerificationResponse['reverification_id'])) {
            echo "Re-verification Request Details:\n";
            echo "===============================\n";
            echo "F4 Index Number: {$reVerificationResponse['f4indexno']}\n";
            echo "Re-verification ID: {$reVerificationResponse['reverification_id']}\n";
            echo "Submitted At: {$reVerificationResponse['submitted_at']}\n";
            echo "Reason: {$reVerificationResponse['reason']}\n";
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.20 GET DOCUMENT VERIFICATION HISTORY
    // ==========================================
    echo "5. GET DOCUMENT VERIFICATION HISTORY (3.20)\n";
    echo "===========================================\n";
    
    try {
        // Get complete verification history for a student
        echo "Getting complete verification history for S0123456789...\n";
        $historyResponse = $client->verification()->getDocumentVerificationHistory('S0123456789');
        echo "Response: " . json_encode($historyResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get verification history filtered by document type
        echo "Getting verification history for certificates only...\n";
        $certificateHistoryResponse = $client->verification()->getDocumentVerificationHistory(
            'S0123456789',
            'certificate'
        );
        echo "Response: " . json_encode($certificateHistoryResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process verification history
        if (isset($historyResponse['verification_history'])) {
            echo "Verification History Summary for {$historyResponse['f4indexno']}:\n";
            echo "=========================================================\n";
            
            foreach ($historyResponse['verification_history'] as $entry) {
                echo "Verification ID: {$entry['verification_id']}\n";
                echo "Document Type: {$entry['document_type']}\n";
                echo "Status: {$entry['verification_status']}\n";
                echo "Verified At: {$entry['verified_at']}\n";
                if (isset($entry['notes'])) {
                    echo "Notes: {$entry['notes']}\n";
                }
                echo "---\n";
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // STUDENT ENROLLMENT EXAMPLES
    // ==========================================
    echo "6. STUDENT ENROLLMENT EXAMPLES\n";
    echo "==============================\n";
    
    try {
        // Enroll a student
        echo "Enrolling student S0123456789...\n";
        $enrollmentResponse = $client->enrollment()->enrollStudent(
            'S0123456789',
            'PROG001',
            'INST001',
            [
                'academic_year' => '2025/2026',
                'semester' => '1',
                'study_mode' => 'full_time'
            ]
        );
        echo "Response: " . json_encode($enrollmentResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get enrollment status
        echo "Getting enrollment status for S0123456789...\n";
        $enrollmentStatusResponse = $client->enrollment()->getEnrollmentStatus('S0123456789');
        echo "Response: " . json_encode($enrollmentStatusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get enrollment statistics for a programme
        echo "Getting enrollment statistics for PROG001...\n";
        $enrollmentStatsResponse = $client->enrollment()->getEnrollmentStatistics('PROG001', '2025/2026');
        echo "Response: " . json_encode($enrollmentStatsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Defer enrollment
        echo "Deferring enrollment for S0123456790...\n";
        $deferResponse = $client->enrollment()->deferEnrollment(
            'S0123456790',
            'Personal circumstances require deferment for one semester',
            '2025-09-01'
        );
        echo "Response: " . json_encode($deferResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Bulk enroll students
        echo "Bulk enrolling multiple students...\n";
        $bulkEnrollmentData = [
            [
                'f4indexno' => 'S0123456791',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001'
            ],
            [
                'f4indexno' => 'S0123456792',
                'programme_code' => 'PROG002',
                'institution_code' => 'INST002'
            ]
        ];
        
        $bulkEnrollmentResponse = $client->enrollment()->bulkEnrollStudents($bulkEnrollmentData);
        echo "Response: " . json_encode($bulkEnrollmentResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ADDITIONAL VERIFICATION OPERATIONS
    // ==========================================
    echo "7. ADDITIONAL VERIFICATION OPERATIONS\n";
    echo "====================================\n";
    
    try {
        // Get verification statistics for a programme
        echo "Getting verification statistics for PROG001...\n";
        $verificationStatsResponse = $client->verification()->getVerificationStatistics('PROG001');
        echo "Response: " . json_encode($verificationStatsResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Update verification status
        echo "Updating verification status for S0123456789...\n";
        $updateStatusResponse = $client->verification()->updateVerificationStatus(
            'S0123456789',
            'verified',
            'All documents have been successfully verified'
        );
        echo "Response: " . json_encode($updateStatusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process verification statistics
        if (isset($verificationStatsResponse['statistics'])) {
            echo "Verification Statistics Summary for {$verificationStatsResponse['programme_code']}:\n";
            echo "==================================================================\n";
            
            $stats = $verificationStatsResponse['statistics'];
            echo "Total Verifications: {$stats['total_verifications']}\n";
            echo "Verified Documents: {$stats['verified_documents']}\n";
            echo "Pending Verifications: {$stats['pending_verifications']}\n";
            echo "Rejected Verifications: {$stats['rejected_verifications']}\n";
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
    
    // Example 1: Invalid F4 index number in document verification
    try {
        echo "Testing invalid F4 index number in document verification...\n";
        $client->verification()->verifyStudentDocuments('invalid_f4', [
            ['document_type' => 'certificate', 'document_number' => 'CERT123', 'document_data' => 'data']
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid certificate type in certificate validation
    try {
        echo "Testing invalid certificate type in certificate validation...\n";
        $client->verification()->validateCertificate('CERT123456', 'invalid_type');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Empty documents array in document verification
    try {
        echo "Testing empty documents array in document verification...\n";
        $client->verification()->verifyStudentDocuments('S0123456789', []);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Invalid programme code in enrollment
    try {
        echo "Testing invalid programme code in enrollment...\n";
        $client->enrollment()->enrollStudent('S0123456789', 'invalid_programme', 'INST001');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Invalid enrollment status
    try {
        echo "Testing invalid enrollment status...\n";
        $client->enrollment()->updateEnrollmentStatus('S0123456789', 'invalid_status', 'Some reason');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 4 Examples Complete ===\n";
    
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