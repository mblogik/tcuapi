<?php

/**
 * TCU API Client - Admitted Applicants Usage Example
 * 
 * Demonstrates the structured response objects for admitted applicants,
 * showing how developers can easily access and work with the data using
 * the AdmittedApplicantTcuResponse model.
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
use MBLogik\TCUAPIClient\Models\Response\AdmittedApplicantTcuResponse;

// Configuration
$config = new Configuration([
    'username' => 'DM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30
]);

$client = new TCUAPIClient($config);

echo "=== TCU API Client - Admitted Applicants Structured Response Example ===\n\n";

try {
    // ====================================================================
    // Get Admitted Applicants - Returns Array of AdmittedApplicantTcuResponse Objects
    // ====================================================================
    echo "1. Getting Admitted Applicants for Programme DM023:\n";
    echo "=====================================================\n";
    
    /** @var AdmittedApplicantTcuResponse[] $admittedApplicants */
    $admittedApplicants = $client->admissions()->getAdmitted('DM023');
    
    echo "Found " . count($admittedApplicants) . " admitted applicants:\n\n";
    
    // ====================================================================
    // Working with Structured Response Objects
    // ====================================================================
    foreach ($admittedApplicants as $index => $applicant) {
        echo "Applicant #" . ($index + 1) . ":\n";
        echo "================\n";
        
        // Easy property access with IDE support and type safety
        echo "F4 Index Number: " . $applicant->getF4IndexNo() . "\n";
        echo "F6 Index Number: " . $applicant->getF6IndexNo() . "\n";
        echo "Mobile Number: " . $applicant->getMobileNumber() . "\n";
        echo "Email Address: " . $applicant->getEmailAddress() . "\n";
        echo "Admission Status: " . $applicant->getAdmissionStatus() . "\n";
        echo "Programme Code: " . $applicant->getProgrammeCode() . "\n";
        
        // Helper methods for easy data interpretation
        echo "Is Provisional: " . ($applicant->isProvisionalAdmission() ? 'Yes' : 'No') . "\n";
        echo "Is Confirmed: " . ($applicant->isConfirmedAdmission() ? 'Yes' : 'No') . "\n";
        echo "Has Valid Contact: " . ($applicant->hasValidContactInfo() ? 'Yes' : 'No') . "\n";
        echo "Has Both Forms: " . ($applicant->hasBothFormResults() ? 'Yes' : 'No') . "\n";
        
        // Formatted data
        echo "Formatted Mobile: " . $applicant->getFormattedMobileNumber() . "\n";
        echo "Full Identification: " . $applicant->getFullIdentification() . "\n";
        echo "Summary: " . $applicant->getSummary() . "\n";
        
        echo "\n";
    }
    
    // ====================================================================
    // Advanced Usage Examples
    // ====================================================================
    echo "2. Advanced Usage Examples:\n";
    echo "============================\n\n";
    
    // Filter provisional admissions
    echo "2.1 Filtering Provisional Admissions:\n";
    echo "---------------------------------------\n";
    $provisionalApplicants = array_filter($admittedApplicants, function($applicant) {
        return $applicant->isProvisionalAdmission();
    });
    
    echo "Provisional admissions: " . count($provisionalApplicants) . "\n";
    foreach ($provisionalApplicants as $applicant) {
        echo "  - " . $applicant->getF4IndexNo() . " (" . $applicant->getEmailAddress() . ")\n";
    }
    echo "\n";
    
    // Filter applicants with valid contact info
    echo "2.2 Filtering Applicants with Valid Contact Info:\n";
    echo "---------------------------------------------------\n";
    $validContactApplicants = array_filter($admittedApplicants, function($applicant) {
        return $applicant->hasValidContactInfo();
    });
    
    echo "Applicants with valid contact info: " . count($validContactApplicants) . "\n";
    foreach ($validContactApplicants as $applicant) {
        echo "  - " . $applicant->getF4IndexNo() . " (" . $applicant->getFormattedMobileNumber() . ")\n";
    }
    echo "\n";
    
    // Extract email addresses for bulk communication
    echo "2.3 Extracting Email Addresses for Communication:\n";
    echo "---------------------------------------------------\n";
    $emailAddresses = array_map(function($applicant) {
        return $applicant->getEmailAddress();
    }, $admittedApplicants);
    
    $uniqueEmails = array_unique(array_filter($emailAddresses));
    echo "Unique email addresses for communication: " . count($uniqueEmails) . "\n";
    foreach ($uniqueEmails as $email) {
        echo "  - " . $email . "\n";
    }
    echo "\n";
    
    // Convert to array for external processing
    echo "2.4 Converting to Arrays for External Processing:\n";
    echo "---------------------------------------------------\n";
    $applicantsArray = array_map(function($applicant) {
        return $applicant->toArray();
    }, $admittedApplicants);
    
    echo "Converted " . count($applicantsArray) . " applicants to array format\n";
    echo "Sample array structure:\n";
    if (!empty($applicantsArray)) {
        print_r($applicantsArray[0]);
    }
    echo "\n";
    
    // Generate JSON for API responses
    echo "2.5 Generating JSON for API Responses:\n";
    echo "---------------------------------------\n";
    if (!empty($admittedApplicants)) {
        $sampleJson = $admittedApplicants[0]->toJson();
        echo "Sample JSON output:\n";
        echo $sampleJson . "\n\n";
    }
    
    // Quick summary statistics
    echo "2.6 Quick Summary Statistics:\n";
    echo "------------------------------\n";
    $totalApplicants = count($admittedApplicants);
    $provisionalCount = count($provisionalApplicants);
    $validContactCount = count($validContactApplicants);
    
    echo "Total Admitted: $totalApplicants\n";
    echo "Provisional: $provisionalCount (" . round(($provisionalCount / $totalApplicants) * 100, 1) . "%)\n";
    echo "Valid Contact: $validContactCount (" . round(($validContactCount / $totalApplicants) * 100, 1) . "%)\n";
    
    $bothFormsCount = count(array_filter($admittedApplicants, function($applicant) {
        return $applicant->hasBothFormResults();
    }));
    echo "Both F4 & F6: $bothFormsCount (" . round(($bothFormsCount / $totalApplicants) * 100, 1) . "%)\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Benefits of Structured Response Objects ===\n";
echo "✅ Type safety and IDE autocompletion\n";
echo "✅ Easy property access with descriptive method names\n";
echo "✅ Built-in helper methods for common operations\n";
echo "✅ Consistent data formatting across the application\n";
echo "✅ Easy conversion to arrays/JSON for external systems\n";
echo "✅ Clear documentation and examples for developers\n";
echo "✅ Reduced errors from typos in array key names\n";
echo "✅ Better testing with strongly typed objects\n";