<?php

/**
 * TCU API Client - Session 3 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 3 endpoints (3.11-3.15)
 * of the TCU API Client. These examples demonstrate confirmation and transfer operations
 * including confirmed applicants, confirmation codes, rejections, and transfers.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 3 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 3 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.11 GET CONFIRMED APPLICANTS
    // ==========================================
    echo "1. GET CONFIRMED APPLICANTS (3.11)\n";
    echo "==================================\n";
    
    try {
        // Get confirmed applicants for a specific programme
        echo "Getting confirmed applicants for PROG001...\n";
        $confirmedResponse = $client->applicants()->getConfirmed('PROG001');
        echo "Response: " . json_encode($confirmedResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process confirmed applicants to show statistics
        if (isset($confirmedResponse['confirmed_applicants']) && !empty($confirmedResponse['confirmed_applicants'])) {
            echo "Confirmed Applicants Summary for {$confirmedResponse['programme_code']}:\n";
            echo "=========================================================\n";
            
            $totalConfirmed = count($confirmedResponse['confirmed_applicants']);
            $multipleAdmissionsCount = 0;
            $singleAdmissionCount = 0;
            
            foreach ($confirmedResponse['confirmed_applicants'] as $applicant) {
                if ($applicant['multiple_admissions'] ?? false) {
                    $multipleAdmissionsCount++;
                } else {
                    $singleAdmissionCount++;
                }
                
                echo "- {$applicant['firstname']} {$applicant['surname']} ({$applicant['f4indexno']})\n";
                echo "  Confirmed: {$applicant['confirmed_at']}\n";
                echo "  Multiple Admissions: " . ($applicant['multiple_admissions'] ? 'Yes' : 'No') . "\n";
                if (!empty($applicant['other_programmes'])) {
                    echo "  Other Programmes: " . implode(', ', $applicant['other_programmes']) . "\n";
                }
                echo "\n";
            }
            
            echo "Statistics:\n";
            echo "Total Confirmed: $totalConfirmed\n";
            echo "With Multiple Admissions: $multipleAdmissionsCount\n";
            echo "With Single Admission: $singleAdmissionCount\n\n";
        }
        
        // Get confirmed applicants for multiple programmes
        echo "Getting confirmed applicants for multiple programmes...\n";
        $programmes = ['PROG001', 'PROG002', 'PROG003'];
        
        foreach ($programmes as $programmeCode) {
            echo "Programme: $programmeCode\n";
            $response = $client->applicants()->getConfirmed($programmeCode);
            
            if (isset($response['confirmed_applicants'])) {
                echo "  Confirmed Applicants: " . count($response['confirmed_applicants']) . "\n";
            }
            echo "\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.12 REQUEST CONFIRMATION CODE
    // ==========================================
    echo "2. REQUEST CONFIRMATION CODE (3.12)\n";
    echo "===================================\n";
    
    try {
        // Request confirmation code for applicant with multiple admissions
        echo "Requesting confirmation code for applicant with multiple admissions...\n";
        $confirmationCodeResponse = $client->dashboard()->requestConfirmationCode('S0123456789');
        echo "Response: " . json_encode($confirmationCodeResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process confirmation code response
        if (isset($confirmationCodeResponse['confirmation_code'])) {
            echo "Confirmation Code Details:\n";
            echo "========================\n";
            echo "F4 Index Number: {$confirmationCodeResponse['f4indexno']}\n";
            echo "Confirmation Code: {$confirmationCodeResponse['confirmation_code']}\n";
            echo "Expires At: {$confirmationCodeResponse['expires_at']}\n";
            echo "Can Request Again At: {$confirmationCodeResponse['can_request_again_at']}\n";
            
            if (isset($confirmationCodeResponse['institutions'])) {
                echo "Available Institutions:\n";
                foreach ($confirmationCodeResponse['institutions'] as $institution) {
                    echo "- {$institution['institution_name']} ({$institution['institution_code']})\n";
                    echo "  Programme: {$institution['programme_name']} ({$institution['programme_code']})\n";
                }
            }
            echo "\n";
        }
        
        // Request confirmation code for multiple applicants
        echo "Requesting confirmation codes for multiple applicants...\n";
        $applicants = ['S0123456789', 'S0123456790', 'S0123456791'];
        
        foreach ($applicants as $f4indexno) {
            echo "Applicant: $f4indexno\n";
            try {
                $response = $client->dashboard()->requestConfirmationCode($f4indexno);
                echo "  Status: " . $response['status_description'] . "\n";
                if (isset($response['confirmation_code'])) {
                    echo "  Code: {$response['confirmation_code']}\n";
                    echo "  Expires: {$response['expires_at']}\n";
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
    // 3.13 CANCEL/REJECT ADMISSION
    // ==========================================
    echo "3. CANCEL/REJECT ADMISSION (3.13)\n";
    echo "=================================\n";
    
    try {
        // Reject admission with various reasons
        echo "Rejecting admission with financial constraints reason...\n";
        $rejectResponse = $client->dashboard()->reject(
            'S0123456789',
            'Financial constraints prevent me from attending this programme at this time.'
        );
        echo "Response: " . json_encode($rejectResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Reject admission with career change reason
        echo "Rejecting admission with career change reason...\n";
        $rejectResponse2 = $client->dashboard()->reject(
            'S0123456790',
            'After careful consideration, I have decided to pursue a different career path that requires specialized training not available in this programme.'
        );
        echo "Response: " . json_encode($rejectResponse2, JSON_PRETTY_PRINT) . "\n\n";
        
        // Reject admission with family circumstances reason
        echo "Rejecting admission with family circumstances reason...\n";
        $rejectResponse3 = $client->dashboard()->reject(
            'S0123456791',
            'Due to unexpected family circumstances, I will not be able to attend university at this time and need to defer my studies.'
        );
        echo "Response: " . json_encode($rejectResponse3, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process rejection response
        if (isset($rejectResponse['can_restore_until'])) {
            echo "Rejection Details:\n";
            echo "=================\n";
            echo "F4 Index Number: {$rejectResponse['f4indexno']}\n";
            echo "Rejected At: {$rejectResponse['rejected_at']}\n";
            echo "Can Restore Until: {$rejectResponse['can_restore_until']}\n";
            echo "Programme: {$rejectResponse['programme_name']} ({$rejectResponse['programme_code']})\n";
            echo "Institution: {$rejectResponse['institution_code']}\n";
            echo "Rejection ID: {$rejectResponse['rejection_id']}\n\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.14 SUBMIT INTERNAL TRANSFERS
    // ==========================================
    echo "4. SUBMIT INTERNAL TRANSFERS (3.14)\n";
    echo "===================================\n";
    
    try {
        // Submit multiple internal transfers
        echo "Submitting multiple internal transfers...\n";
        $internalTransfers = [
            [
                'f4indexno' => 'S0123456789',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Better alignment with career goals and interests'
            ],
            [
                'f4indexno' => 'S0123456790',
                'from_programme_code' => 'PROG003',
                'to_programme_code' => 'PROG004',
                'reason' => 'Academic performance improvement and subject preference'
            ]
        ];
        
        echo "Internal Transfer Data: " . json_encode($internalTransfers, JSON_PRETTY_PRINT) . "\n";
        
        $internalTransferResponse = $client->transfers()->submitInternalTransfers($internalTransfers);
        echo "Response: " . json_encode($internalTransferResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Submit single internal transfer using convenience method
        echo "Submitting single internal transfer using convenience method...\n";
        $singleInternalResponse = $client->transfers()->submitSingleInternalTransfer(
            'S0123456791',
            'PROG005',
            'PROG006',
            'Discovered new passion for this field of study',
            ['academic_year' => '2025', 'semester' => '1']
        );
        echo "Response: " . json_encode($singleInternalResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get internal transfer status
        echo "Getting internal transfer status...\n";
        $internalStatusResponse = $client->transfers()->getInternalTransferStatus('PROG001');
        echo "Response: " . json_encode($internalStatusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.15 SUBMIT INTER-INSTITUTIONAL TRANSFERS
    // ==========================================
    echo "5. SUBMIT INTER-INSTITUTIONAL TRANSFERS (3.15)\n";
    echo "==============================================\n";
    
    try {
        // Submit inter-institutional transfers
        echo "Submitting inter-institutional transfers...\n";
        $interTransfers = [
            [
                'f4indexno' => 'S0123456789',
                'from_institution_code' => 'INST001',
                'to_institution_code' => 'INST002',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Better facilities and research opportunities at target institution'
            ]
        ];
        
        echo "Inter-institutional Transfer Data: " . json_encode($interTransfers, JSON_PRETTY_PRINT) . "\n";
        
        $interTransferResponse = $client->transfers()->submitInterInstitutionalTransfers($interTransfers);
        echo "Response: " . json_encode($interTransferResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Submit single inter-institutional transfer using convenience method
        echo "Submitting single inter-institutional transfer using convenience method...\n";
        $singleInterResponse = $client->transfers()->submitSingleInterInstitutionalTransfer(
            'S0123456790',
            'INST003',
            'INST004',
            'PROG007',
            'PROG008',
            'Geographic relocation and better programme reputation',
            ['academic_year' => '2025', 'semester' => '1', 'relocation_reason' => 'Family moved']
        );
        echo "Response: " . json_encode($singleInterResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get inter-institutional transfer status
        echo "Getting inter-institutional transfer status...\n";
        $interStatusResponse = $client->transfers()->getInterInstitutionalTransferStatus('PROG001');
        echo "Response: " . json_encode($interStatusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // ADDITIONAL TRANSFER OPERATIONS
    // ==========================================
    echo "6. ADDITIONAL TRANSFER OPERATIONS\n";
    echo "=================================\n";
    
    try {
        // Get transfer history for specific applicant
        echo "Getting transfer history for applicant...\n";
        $transferHistoryResponse = $client->transfers()->getTransferHistory('S0123456789');
        echo "Response: " . json_encode($transferHistoryResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Cancel a transfer request
        echo "Cancelling a transfer request...\n";
        $cancelResponse = $client->transfers()->cancelTransfer(
            'INT_2025_001',
            'Changed my mind about the transfer after further consideration'
        );
        echo "Response: " . json_encode($cancelResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process transfer history
        if (isset($transferHistoryResponse['transfers']) && !empty($transferHistoryResponse['transfers'])) {
            echo "Transfer History Summary for {$transferHistoryResponse['f4indexno']}:\n";
            echo "=======================================================\n";
            
            foreach ($transferHistoryResponse['transfers'] as $transfer) {
                echo "Transfer ID: {$transfer['transfer_id']}\n";
                echo "Type: {$transfer['transfer_type']}\n";
                echo "Status: {$transfer['status']}\n";
                echo "Submitted: {$transfer['submitted_at']}\n";
                if (isset($transfer['approved_at'])) {
                    echo "Approved: {$transfer['approved_at']}\n";
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
    // ERROR HANDLING EXAMPLES
    // ==========================================
    echo "7. ERROR HANDLING EXAMPLES\n";
    echo "==========================\n";
    
    // Example 1: Invalid programme code in getConfirmed
    try {
        echo "Testing invalid programme code in getConfirmed...\n";
        $client->applicants()->getConfirmed('invalid_programme');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid F4 index number in requestConfirmationCode
    try {
        echo "Testing invalid F4 index number in requestConfirmationCode...\n";
        $client->dashboard()->requestConfirmationCode('invalid_f4');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Empty reason in reject
    try {
        echo "Testing empty reason in reject...\n";
        $client->dashboard()->reject('S0123456789', '');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Same source and target programme in internal transfer
    try {
        echo "Testing same source and target programme in internal transfer...\n";
        $client->transfers()->submitSingleInternalTransfer(
            'S0123456789',
            'PROG001',
            'PROG001',
            'Transfer reason'
        );
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Same source and target institution in inter-institutional transfer
    try {
        echo "Testing same source and target institution in inter-institutional transfer...\n";
        $client->transfers()->submitSingleInterInstitutionalTransfer(
            'S0123456789',
            'INST001',
            'INST001',
            'PROG001',
            'PROG002',
            'Transfer reason'
        );
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 6: Empty transfer data
    try {
        echo "Testing empty transfer data...\n";
        $client->transfers()->submitInternalTransfers([]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 3 Examples Complete ===\n";
    
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