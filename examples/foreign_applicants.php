<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Models\Applicant;

// Initialize client
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password'
    ]
]);

$client = new TCUAPIClient($config);

try {
    // Example 1: Submit foreign applicants for bachelor's degree
    echo "=== Submitting Foreign Applicants ===\n";

    $foreignApplicants = [
        [
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'surname' => 'Smith',
            'gender' => 'F',
            'nationality' => 'Kenyan',
            'year_of_birth' => 1999,
            'passport_number' => 'P1234567',
            'academic_qualifications' => [
                [
                    'qualification' => 'A-Level',
                    'institution' => 'Nairobi High School',
                    'year' => 2018,
                    'grades' => 'A, B, B'
                ]
            ],
            'programme_choices' => [
                [
                    'programme_code' => 'PROG001',
                    'priority' => 1
                ]
            ]
        ],
        [
            'first_name' => 'David',
            'middle_name' => 'Johnson',
            'surname' => 'Williams',
            'gender' => 'M',
            'nationality' => 'Ugandan',
            'year_of_birth' => 1998,
            'passport_number' => 'P7654321',
            'academic_qualifications' => [
                [
                    'qualification' => 'A-Level',
                    'institution' => 'Kampala Secondary School',
                    'year' => 2017,
                    'grades' => 'A, A, B'
                ]
            ],
            'programme_choices' => [
                [
                    'programme_code' => 'PROG002',
                    'priority' => 1
                ]
            ]
        ]
    ];

    $foreignResponse = $client->applicants()->submitForeignApplicants($foreignApplicants);
    echo "Foreign Applicants Response: " . json_encode($foreignResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 2: Submit admitted foreign students (Direct Entry)
    echo "=== Submitting Admitted Foreign Students (Direct Entry) ===\n";

    $admittedDirectEntry = [
        [
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'surname' => 'Smith',
            'gender' => 'F',
            'nationality' => 'Kenyan',
            'passport_number' => 'P1234567',
            'programme_code' => 'PROG001',
            'admission_year' => '2025',
            'entry_type' => 'Direct',
            'academic_qualifications' => [
                [
                    'qualification' => 'A-Level',
                    'institution' => 'Nairobi High School',
                    'year' => 2018,
                    'grades' => 'A, B, B',
                    'equivalency_status' => 'Approved'
                ]
            ]
        ]
    ];

    $directEntryResponse = $client->applicants()->submitForeignAdmittedDirect($admittedDirectEntry);
    echo "Direct Entry Response: " . json_encode($directEntryResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 3: Submit admitted foreign students (Equivalent Entry)
    echo "=== Submitting Admitted Foreign Students (Equivalent Entry) ===\n";

    $admittedEquivalentEntry = [
        [
            'first_name' => 'David',
            'middle_name' => 'Johnson',
            'surname' => 'Williams',
            'gender' => 'M',
            'nationality' => 'Ugandan',
            'passport_number' => 'P7654321',
            'programme_code' => 'PROG002',
            'admission_year' => '2025',
            'entry_type' => 'Equivalent',
            'academic_qualifications' => [
                [
                    'qualification' => 'A-Level',
                    'institution' => 'Kampala Secondary School',
                    'year' => 2017,
                    'grades' => 'A, A, B',
                    'equivalency_status' => 'Approved',
                    'equivalency_body' => 'NACTE'
                ]
            ],
            'equivalency_documents' => [
                [
                    'document_type' => 'Equivalency Certificate',
                    'document_number' => 'EQ2025001',
                    'issuing_body' => 'NACTE',
                    'issue_date' => '2024-12-15'
                ]
            ]
        ]
    ];

    $equivalentEntryResponse = $client->applicants()->submitForeignAdmittedEquivalent($admittedEquivalentEntry);
    echo "Equivalent Entry Response: " . json_encode($equivalentEntryResponse, JSON_PRETTY_PRINT) . "\n\n";

    // Example 4: Track application progress
    echo "=== Tracking Application Progress ===\n";

    // Check status of submitted applications
    $programmes = ['PROG001', 'PROG002'];

    foreach ($programmes as $programmeCode) {
        echo "Programme: {$programmeCode}\n";

        // Get admitted applicants
        $admitted = $client->admissions()->getAdmitted($programmeCode);
        echo "Admitted count: " . count($admitted) . "\n";

        // Get confirmed applicants
        $confirmed = $client->admissions()->getConfirmed($programmeCode);
        echo "Confirmed count: " . count($confirmed) . "\n";

        echo "---\n";
    }

    // Example 5: Generate statistics report
    echo "=== Foreign Applicants Statistics ===\n";

    if ($client->getLogger()) {
        $stats = $client->getLogger()->getApiCallStats();
        echo "Total API calls: " . ($stats['total_calls'] ?? 0) . "\n";
        echo "Successful calls: " . ($stats['successful_calls'] ?? 0) . "\n";
        echo "Failed calls: " . ($stats['failed_calls'] ?? 0) . "\n";
        echo "Average execution time: " . number_format($stats['avg_execution_time'] ?? 0, 3) . " seconds\n";
    }

} catch (TCUAPIException $e) {
    echo "TCU API Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";

    if (!empty($e->getContext())) {
        echo "Context: " . json_encode($e->getContext(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
