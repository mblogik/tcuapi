<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Models\Data\Applicant;

// Basic configuration
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'timeout' => 30,
    'enable_logging' => true,
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password',
        'port' => 3306,
        'table_prefix' => 'tcu_api_'
    ]
]);

try {
    // Initialize the client
    $client = new TCUAPIClient($config);

    // Example 1: Check applicant status (3.1)
    echo "=== Checking Applicant Status ===\n";
    $statusResponse = $client->applicants()->checkStatus('S0123/0001/2023', 'S0123/0001/2025');
    echo "Status: " . json_encode($statusResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 2: Add a new applicant (3.2)
    echo "=== Adding New Applicant ===\n";
    $applicantData = [
        'firstname' => 'John',
        'middlename' => 'Michael',
        'surname' => 'Doe',
        'gender' => 'M',
        'f4indexno' => 'S0123/0001/2023',
        'f6indexno' => 'S0123/0001/2025',
        'nationality' => 'Tanzanian',
        'year' => 2000,
        'applicant_category' => 'Government',
        'institution_code' => 'INST001',
        'email' => 'john.doe@example.com',
        'phone' => '+255712345678'
    ];

    $addResponse = $client->applicants()->add($applicantData);
    echo "Add Response: " . json_encode($addResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 3: Submit programme choices (3.3)
    echo "=== Submitting Programme Choices ===\n";
    $programmeChoices = [
        [
            'programme_code' => 'PROG001',
            'priority' => 1
        ],
        [
            'programme_code' => 'PROG002',
            'priority' => 2
        ]
    ];
    
    $contactDetails = [
        'mobile' => '+255712345678',
        'email' => 'john.doe@example.com'
    ];
    
    $additionalData = [
        'admission_status' => 'selected',
        'programme_of_admission' => 'PROG001'
    ];

    $programmeResponse = $client->applicants()->submitProgrammeChoices('S0123/0001/2023', $programmeChoices, $contactDetails, $additionalData);
    echo "Programme Response: " . json_encode($programmeResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 4: Get admitted applicants
    echo "=== Getting Admitted Applicants ===\n";
    $admittedResponse = $client->admissions()->getAdmitted('PROG001');
    echo "Admitted: " . json_encode($admittedResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 5: Confirm admission (3.4)
    echo "=== Confirming Admission ===\n";
    $confirmResponse = $client->admissions()->confirm('S0123/0001/2023', 'CONF123ABC');
    echo "Confirm Response: " . json_encode($confirmResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    // Example 6: Unconfirm admission (3.5)
    echo "=== Unconfirming Admission ===\n";
    $unconfirmResponse = $client->admissions()->unconfirm('S0123/0001/2023', 'Changed mind, want to attend different institution');
    echo "Unconfirm Response: " . json_encode($unconfirmResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 7: Populate dashboard
    echo "=== Populating Dashboard ===\n";
    $dashboardResponse = $client->dashboard()->populate('PROG001', 45, 30, [
        'total_applications' => 75,
        'admission_year' => '2025'
    ]);
    echo "Dashboard Response: " . json_encode($dashboardResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 8: Get API call statistics (if database logging is enabled)
    if ($client->getLogger()) {
        echo "=== API Call Statistics ===\n";
        $stats = $client->getLogger()->getApiCallStats();
        echo "Stats: " . json_encode($stats, JSON_PRETTY_PRINT) . "\n\n";

        echo "=== Recent Errors ===\n";
        $errors = $client->getLogger()->getRecentErrors(5);
        echo "Recent Errors: " . json_encode($errors, JSON_PRETTY_PRINT) . "\n\n";
    }

} catch (TCUAPIException $e) {
    echo "TCU API Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Context: " . json_encode($e->getContext(), JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
