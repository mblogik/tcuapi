<?php

/**
 * TCU API Client - Structured Response Objects Usage Example
 * 
 * Demonstrates how the structured response objects make the API much more 
 * developer-friendly with IDE support, type safety, and clear documentation.
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
use MBLogik\TCUAPIClient\Models\Response\CheckStatusTcuResponse;
use MBLogik\TCUAPIClient\Models\Response\AdmittedApplicantTcuResponse;
use MBLogik\TCUAPIClient\Models\Response\ConfirmAdmissionTcuResponse;
use MBLogik\TCUAPIClient\Models\Response\DashboardPopulateTcuResponse;

// Configuration
$config = new Configuration([
    'username' => 'DM',
    'security_token' => 'OTcyMURGMTY5QTRENU3MUJ',
    'base_url' => 'https://api.tcu.go.tz',
    'timeout' => 30
]);

$client = new TCUAPIClient($config);

echo "=== TCU API Client - Structured Response Objects Example ===\n\n";

try {
    // ====================================================================
    // 1. Check Status - Returns CheckStatusTcuResponse Object
    // ====================================================================
    echo "1. Check Applicant Status - Structured Response:\n";
    echo "=================================================\n";
    
    /** @var CheckStatusTcuResponse $statusResponse */
    $statusResponse = $client->applicants()->checkStatus('S1001/0012/2018');
    
    // IDE provides autocompletion and type hints for all these methods:
    echo "F4 Index: " . $statusResponse->getF4IndexNo() . "\n";
    echo "Is Success: " . ($statusResponse->isSuccess() ? 'Yes' : 'No') . "\n";
    echo "Is Eligible: " . ($statusResponse->isEligible() ? 'Yes' : 'No') . "\n";
    echo "Has Admission: " . ($statusResponse->hasAdmission() ? 'Yes' : 'No') . "\n";
    echo "Can Apply: " . ($statusResponse->canApply() ? 'Yes' : 'No') . "\n";
    echo "Current State: " . $statusResponse->getCurrentState() . "\n";
    
    if ($statusResponse->hasAdmission()) {
        echo "Admission Status: " . $statusResponse->getAdmissionStatus() . "\n";
        echo "Programme Code: " . $statusResponse->getProgrammeCode() . "\n";
        echo "Institution Code: " . $statusResponse->getInstitutionCode() . "\n";
        echo "Is Confirmed: " . ($statusResponse->isConfirmed() ? 'Yes' : 'No') . "\n";
        echo "Is Provisional: " . ($statusResponse->isProvisional() ? 'Yes' : 'No') . "\n";
    }
    
    // Easy conversion to array/JSON for external systems
    echo "\nStatus Summary Array:\n";
    print_r($statusResponse->getStatusSummary());
    
    echo "\nJSON Export:\n";
    echo $statusResponse->toJson() . "\n\n";
    
    // ====================================================================
    // 2. Multiple Status Check - Returns Array of CheckStatusTcuResponse Objects
    // ====================================================================
    echo "2. Multiple Status Check - Array of Objects:\n";
    echo "=============================================\n";
    
    /** @var CheckStatusTcuResponse[] $multipleResponses */
    $multipleResponses = $client->applicants()->checkStatus([
        'S1001/0012/2018', 
        'S1001/0015/2016'
    ]);
    
    foreach ($multipleResponses as $index => $response) {
        echo "Applicant #" . ($index + 1) . ": " . $response->getF4IndexNo() . "\n";
        echo "  Status: " . $response->getCurrentState() . "\n";
        echo "  Eligible: " . ($response->isEligible() ? 'Yes' : 'No') . "\n";
        echo "  Has Admission: " . ($response->hasAdmission() ? 'Yes' : 'No') . "\n";
    }
    echo "\n";
    
    // ====================================================================
    // 3. Dashboard Populate - Returns DashboardPopulateTcuResponse Object
    // ====================================================================
    echo "3. Dashboard Populate - Structured Response:\n";
    echo "=============================================\n";
    
    /** @var DashboardPopulateTcuResponse $dashboardResponse */
    $dashboardResponse = $client->dashboard()->populate('DM038', 45, 60);
    
    echo "Programme Code: " . $dashboardResponse->getProgrammeCode() . "\n";
    echo "Is Success: " . ($dashboardResponse->isSuccess() ? 'Yes' : 'No') . "\n";
    echo "Is Populated: " . ($dashboardResponse->isPopulated() ? 'Yes' : 'No') . "\n";
    echo "Male Count: " . $dashboardResponse->getMaleCount() . "\n";
    echo "Female Count: " . $dashboardResponse->getFemaleCount() . "\n";
    echo "Total Count: " . $dashboardResponse->getTotalCount() . "\n";
    echo "Male Percentage: " . $dashboardResponse->getMalePercentage() . "%\n";
    echo "Female Percentage: " . $dashboardResponse->getFemalePercentage() . "%\n";
    echo "Gender Balance: " . $dashboardResponse->getGenderBalanceStatus() . "\n";
    echo "Has Gender Imbalance: " . ($dashboardResponse->hasGenderImbalance() ? 'Yes' : 'No') . "\n";
    echo "Operation Result: " . $dashboardResponse->getOperationResult() . "\n";
    
    echo "\nGender Distribution:\n";
    print_r($dashboardResponse->getGenderDistribution());
    
    echo "\nRecommended Next Steps:\n";
    foreach ($dashboardResponse->getRecommendedNextSteps() as $step) {
        echo "  - " . $step . "\n";
    }
    echo "\n";
    
    // ====================================================================
    // 4. Confirm Admission - Returns ConfirmAdmissionTcuResponse Object
    // ====================================================================
    echo "4. Confirm Admission - Structured Response:\n";
    echo "============================================\n";
    
    /** @var ConfirmAdmissionTcuResponse $confirmResponse */
    $confirmResponse = $client->admissions()->confirm('S1001/0012/2018', 'A5267Y');
    
    echo "F4 Index: " . $confirmResponse->getF4IndexNo() . "\n";
    echo "Is Success: " . ($confirmResponse->isSuccess() ? 'Yes' : 'No') . "\n";
    echo "Is Confirmed: " . ($confirmResponse->isConfirmed() ? 'Yes' : 'No') . "\n";
    echo "Confirmation Code: " . $confirmResponse->getConfirmationCode() . "\n";
    echo "Operation Result: " . $confirmResponse->getOperationResult() . "\n";
    echo "Operation Complete: " . ($confirmResponse->isOperationComplete() ? 'Yes' : 'No') . "\n";
    
    echo "\nRecommended Next Steps:\n";
    foreach ($confirmResponse->getRecommendedNextSteps() as $step) {
        echo "  - " . $step . "\n";
    }
    echo "\n";
    
    // ====================================================================
    // 5. Get Admitted Applicants - Returns Array of AdmittedApplicantTcuResponse Objects
    // ====================================================================
    echo "5. Get Admitted Applicants - Array of Objects:\n";
    echo "===============================================\n";
    
    /** @var AdmittedApplicantTcuResponse[] $admittedApplicants */
    $admittedApplicants = $client->admissions()->getAdmitted('DM023');
    
    echo "Found " . count($admittedApplicants) . " admitted applicants:\n\n";
    
    foreach ($admittedApplicants as $index => $applicant) {
        echo "Applicant #" . ($index + 1) . ":\n";
        echo "  F4 Index: " . $applicant->getF4IndexNo() . "\n";
        echo "  F6 Index: " . $applicant->getF6IndexNo() . "\n";
        echo "  Email: " . $applicant->getEmailAddress() . "\n";
        echo "  Mobile: " . $applicant->getFormattedMobileNumber() . "\n";
        echo "  Admission Status: " . $applicant->getAdmissionStatus() . "\n";
        echo "  Is Provisional: " . ($applicant->isProvisionalAdmission() ? 'Yes' : 'No') . "\n";
        echo "  Has Valid Contact: " . ($applicant->hasValidContactInfo() ? 'Yes' : 'No') . "\n";
        echo "  Full ID: " . $applicant->getFullIdentification() . "\n";
        echo "  Summary: " . $applicant->getSummary() . "\n";
        echo "\n";
    }
    
    // ====================================================================
    // 6. Advanced Usage - Filtering and Analysis
    // ====================================================================
    echo "6. Advanced Usage - Filtering and Analysis:\n";
    echo "============================================\n";
    
    // Filter provisional admissions
    $provisionalApplicants = array_filter($admittedApplicants, function($applicant) {
        return $applicant->isProvisionalAdmission();
    });
    
    echo "Provisional Admissions: " . count($provisionalApplicants) . "\n";
    
    // Filter applicants with valid contact info
    $validContactApplicants = array_filter($admittedApplicants, function($applicant) {
        return $applicant->hasValidContactInfo();
    });
    
    echo "Valid Contact Info: " . count($validContactApplicants) . "\n";
    
    // Extract emails for bulk communication
    $emails = array_map(function($applicant) {
        return $applicant->getEmailAddress();
    }, $validContactApplicants);
    
    echo "Email List for Communication: " . implode(', ', array_unique($emails)) . "\n";
    
    // Convert to arrays for external processing
    $applicantArrays = array_map(function($applicant) {
        return $applicant->toArray();
    }, $admittedApplicants);
    
    echo "Converted to arrays for external systems: " . count($applicantArrays) . " records\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== Benefits of Structured Response Objects ===\n";
echo "✅ **IDE Autocompletion**: Full IntelliSense support with method suggestions\n";
echo "✅ **Type Safety**: Catch errors at development time, not runtime\n";
echo "✅ **Clear Documentation**: Each method is self-documenting with PHPDoc\n";
echo "✅ **Helper Methods**: Built-in convenience methods for common operations\n";
echo "✅ **Consistent API**: Same patterns across all endpoints\n";
echo "✅ **Easy Testing**: Mock objects and unit tests are much simpler\n";
echo "✅ **Better Debugging**: Clear object structure in var_dump and debuggers\n";
echo "✅ **Data Validation**: Built-in validation and type checking\n";
echo "✅ **Extensibility**: Easy to add new methods and functionality\n";
echo "✅ **External Integration**: Clean conversion to arrays/JSON for other systems\n";

echo "\n=== Comparison: Before vs After ===\n";
echo "**Before (Raw Arrays):**\n";
echo "```php\n";
echo "\$response = \$client->applicants()->checkStatus('S1001/0012/2018');\n";
echo "if (\$response['StatusCode'] == 200 && isset(\$response['f4indexno'])) {\n";
echo "    echo \$response['StatusDescription']; // Prone to typos and errors\n";
echo "}\n";
echo "```\n\n";

echo "**After (Structured Objects):**\n";
echo "```php\n";
echo "\$response = \$client->applicants()->checkStatus('S1001/0012/2018');\n";
echo "if (\$response->isSuccess() && \$response->isEligible()) {\n";
echo "    echo \$response->getCurrentState(); // IDE autocompletion + type safety\n";
echo "}\n";
echo "```\n\n";

echo "The structured approach eliminates array key typos, provides better IDE support,\n";
echo "and makes the code much more maintainable and developer-friendly!\n";