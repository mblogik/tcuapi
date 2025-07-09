<?php

/**
 * TCU API Client - Session 2 Endpoints Usage Examples
 * 
 * This file contains comprehensive examples for using Session 2 endpoints (3.6-3.10)
 * of the TCU API Client. These examples demonstrate administrative operations
 * including resubmission, dashboard population, and status retrieval.
 * 
 * @package    MBLogik\TCUAPIClient\Examples
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Usage examples for Session 2 TCU API endpoints
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
    
    echo "=== TCU API Client - Session 2 Endpoints Examples ===\n\n";
    
    // ==========================================
    // 3.6 RESUBMIT APPLICANT DETAILS
    // ==========================================
    echo "1. RESUBMIT APPLICANT DETAILS (3.6)\n";
    echo "====================================\n";
    
    try {
        // Resubmit with corrected email
        echo "Resubmitting applicant with corrected email...\n";
        $updatedApplicantData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'email' => 'john.doe.corrected@example.com', // Corrected email
            'phone' => '+255712345678',
            'nationality' => 'Tanzanian',
            'applicant_category' => 'Government',
            'institution_code' => 'INST001'
        ];
        
        echo "Updated Applicant Data: " . json_encode($updatedApplicantData, JSON_PRETTY_PRINT) . "\n";
        
        $resubmitResponse = $client->applicants()->resubmit($updatedApplicantData);
        echo "Response: " . json_encode($resubmitResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Resubmit with updated contact information
        echo "Resubmitting applicant with updated contact information...\n";
        $contactUpdateData = [
            'f4indexno' => 'S0123456790',
            'firstname' => 'Jane',
            'middlename' => 'Mary',
            'surname' => 'Smith',
            'gender' => 'F',
            'email' => 'jane.smith.new@example.com',
            'phone' => '+255756789012', // Updated phone
            'nationality' => 'Tanzanian',
            'applicant_category' => 'Private',
            'institution_code' => 'INST002'
        ];
        
        echo "Contact Update Data: " . json_encode($contactUpdateData, JSON_PRETTY_PRINT) . "\n";
        
        $contactUpdateResponse = $client->applicants()->resubmit($contactUpdateData);
        echo "Response: " . json_encode($contactUpdateResponse, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.7 POPULATE DASHBOARD
    // ==========================================
    echo "2. POPULATE DASHBOARD (3.7)\n";
    echo "===========================\n";
    
    try {
        // Basic dashboard population
        echo "Populating dashboard with basic statistics...\n";
        $basicDashboardResponse = $client->dashboard()->populate('PROG001', 45, 35);
        echo "Response: " . json_encode($basicDashboardResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Dashboard population with additional data
        echo "Populating dashboard with additional data...\n";
        $additionalData = [
            'academic_year' => '2025',
            'application_round' => '1',
            'institution_code' => 'INST001',
            'programme_name' => 'Computer Science',
            'faculty' => 'Engineering',
            'total_applications' => 200,
            'selected_applications' => 80,
            'confirmed_applications' => 75
        ];
        
        $detailedDashboardResponse = $client->dashboard()->populate('PROG001', 45, 35, $additionalData);
        echo "Additional Data: " . json_encode($additionalData, JSON_PRETTY_PRINT) . "\n";
        echo "Response: " . json_encode($detailedDashboardResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Multiple programme dashboard updates
        echo "Populating dashboard for multiple programmes...\n";
        $programmes = [
            ['code' => 'PROG001', 'males' => 30, 'females' => 20],
            ['code' => 'PROG002', 'males' => 40, 'females' => 25],
            ['code' => 'PROG003', 'males' => 20, 'females' => 30],
            ['code' => 'PROG004', 'males' => 35, 'females' => 15]
        ];
        
        foreach ($programmes as $programme) {
            $response = $client->dashboard()->populate(
                $programme['code'],
                $programme['males'],
                $programme['females'],
                ['batch_update' => true, 'timestamp' => date('Y-m-d H:i:s')]
            );
            echo "Programme {$programme['code']}: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
        }
        echo "\n";
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.8 GET ADMITTED APPLICANTS
    // ==========================================
    echo "3. GET ADMITTED APPLICANTS (3.8)\n";
    echo "================================\n";
    
    try {
        // Get admitted applicants for a specific programme
        echo "Getting admitted applicants for PROG001...\n";
        $admittedResponse = $client->applicants()->getAdmitted('PROG001');
        echo "Response: " . json_encode($admittedResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Get admitted applicants for multiple programmes
        echo "Getting admitted applicants for multiple programmes...\n";
        $programmes = ['PROG001', 'PROG002', 'PROG003'];
        
        foreach ($programmes as $programmeCode) {
            echo "Programme: $programmeCode\n";
            $response = $client->applicants()->getAdmitted($programmeCode);
            echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.9 GET PROGRAMMES WITH ADMITTED CANDIDATES
    // ==========================================
    echo "4. GET PROGRAMMES WITH ADMITTED CANDIDATES (3.9)\n";
    echo "================================================\n";
    
    try {
        // Get all programmes with admitted candidates
        echo "Getting all programmes with admitted candidates...\n";
        $programmesResponse = $client->admissions()->getProgrammes();
        echo "Response: " . json_encode($programmesResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process the response to show statistics
        if (isset($programmesResponse['programmes']) && !empty($programmesResponse['programmes'])) {
            echo "Programme Statistics Summary:\n";
            echo "============================\n";
            
            $totalAdmitted = 0;
            $totalMales = 0;
            $totalFemales = 0;
            
            foreach ($programmesResponse['programmes'] as $programme) {
                $admitted = $programme['admitted_count'] ?? 0;
                $males = $programme['male_count'] ?? 0;
                $females = $programme['female_count'] ?? 0;
                
                $totalAdmitted += $admitted;
                $totalMales += $males;
                $totalFemales += $females;
                
                echo "Programme: {$programme['programme_code']} ({$programme['programme_name']})\n";
                echo "  Admitted: $admitted (Males: $males, Females: $females)\n";
                echo "  Gender Ratio: " . ($admitted > 0 ? round(($males / $admitted) * 100, 1) : 0) . "% Male, " . 
                     ($admitted > 0 ? round(($females / $admitted) * 100, 1) : 0) . "% Female\n\n";
            }
            
            echo "Overall Statistics:\n";
            echo "Total Admitted: $totalAdmitted\n";
            echo "Total Males: $totalMales\n";
            echo "Total Females: $totalFemales\n";
            echo "Overall Gender Ratio: " . ($totalAdmitted > 0 ? round(($totalMales / $totalAdmitted) * 100, 1) : 0) . "% Male, " . 
                 ($totalAdmitted > 0 ? round(($totalFemales / $totalAdmitted) * 100, 1) : 0) . "% Female\n\n";
        }
        
    } catch (ValidationException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        echo "Validation Errors: " . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // ==========================================
    // 3.10 GET APPLICANTS' ADMISSION STATUS
    // ==========================================
    echo "5. GET APPLICANTS' ADMISSION STATUS (3.10)\n";
    echo "==========================================\n";
    
    try {
        // Get admission status for a specific programme
        echo "Getting admission status for PROG001...\n";
        $statusResponse = $client->applicants()->getStatus('PROG001');
        echo "Response: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";
        
        // Process status response to show confirmation statistics
        if (isset($statusResponse['applicants']) && !empty($statusResponse['applicants'])) {
            echo "Admission Status Summary for {$statusResponse['programme_code']}:\n";
            echo "========================================================\n";
            
            $confirmedCount = 0;
            $pendingCount = 0;
            $rejectedCount = 0;
            
            foreach ($statusResponse['applicants'] as $applicant) {
                $confirmationStatus = $applicant['confirmation_status'] ?? 'unknown';
                
                switch ($confirmationStatus) {
                    case 'confirmed':
                        $confirmedCount++;
                        break;
                    case 'pending':
                        $pendingCount++;
                        break;
                    case 'rejected':
                        $rejectedCount++;
                        break;
                }
            }
            
            echo "Confirmed: $confirmedCount\n";
            echo "Pending: $pendingCount\n";
            echo "Rejected: $rejectedCount\n";
            echo "Total: " . count($statusResponse['applicants']) . "\n\n";
        }
        
        // Get status for multiple programmes
        echo "Getting admission status for multiple programmes...\n";
        $programmes = ['PROG001', 'PROG002', 'PROG003'];
        
        foreach ($programmes as $programmeCode) {
            echo "Programme: $programmeCode\n";
            $response = $client->applicants()->getStatus($programmeCode);
            
            if (isset($response['applicants'])) {
                echo "  Total Applicants: " . count($response['applicants']) . "\n";
                
                // Count by status
                $statusCounts = [];
                foreach ($response['applicants'] as $applicant) {
                    $status = $applicant['admission_status'] ?? 'unknown';
                    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                }
                
                foreach ($statusCounts as $status => $count) {
                    echo "  $status: $count\n";
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
    echo "6. ERROR HANDLING EXAMPLES\n";
    echo "==========================\n";
    
    // Example 1: Missing F4 index number in resubmit
    try {
        echo "Testing missing F4 index number in resubmit...\n";
        $client->applicants()->resubmit([
            'firstname' => 'John',
            'surname' => 'Doe'
            // Missing f4indexno
        ]);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 2: Invalid programme code in dashboard populate
    try {
        echo "Testing invalid programme code in dashboard populate...\n";
        $client->dashboard()->populate('invalid_programme', 10, 20);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 3: Negative values in dashboard populate
    try {
        echo "Testing negative values in dashboard populate...\n";
        $client->dashboard()->populate('PROG001', -5, 10);
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 4: Invalid programme code in get admitted
    try {
        echo "Testing invalid programme code in get admitted...\n";
        $client->applicants()->getAdmitted('invalid_programme');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    // Example 5: Invalid programme code in get status
    try {
        echo "Testing invalid programme code in get status...\n";
        $client->applicants()->getStatus('invalid_programme');
    } catch (ValidationException $e) {
        echo "Caught validation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Session 2 Examples Complete ===\n";
    
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