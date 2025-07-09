<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Models\Data\Applicant;
use MBLogik\TCUAPIClient\Models\Data\Programme;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;
use MBLogik\TCUAPIClient\Models\Request\SubmitProgrammeRequest;

echo "=== TCU API Client - Models Usage Examples ===\n\n";

// Configuration
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'enable_database_logging' => true,
    'database' => [
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password'
    ]
]);

$client = new TCUAPIClient($config);

try {

    // ===== Example 1: Using Data Models =====
    echo "1. Creating and Validating Data Models\n";
    echo "=====================================\n";

    // Create an Applicant model
    $applicant = new Applicant([
        'first_name' => 'John',
        'middle_name' => 'Michael',
        'surname' => 'Doe',
        'gender' => 'M',
        'form_four_index_number' => 'S0123/0001/2023',
        'form_six_index_number' => 'S0123/0001/2025',
        'nationality' => 'Tanzanian',
        'year_of_birth' => 2000,
        'applicant_category' => 'Government',
        'phone_number' => '+255123456789',
        'email_address' => 'john.doe@example.com',
        'disability_status' => false
    ]);

    // Validate applicant data
    $errors = $applicant->validate();
    if (empty($errors)) {
        echo "✓ Applicant data is valid\n";
        echo "  Full Name: " . $applicant->getFullName() . "\n";
        echo "  Form Four Index: " . $applicant->getFormFourIndexNumber() . "\n";
        echo "  Is Local: " . ($applicant->isLocal() ? 'Yes' : 'No') . "\n";
        echo "  Has Form Six: " . ($applicant->hasFormSixResults() ? 'Yes' : 'No') . "\n";
        echo "  Has Disability: " . ($applicant->hasDisability() ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Applicant validation errors:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // Create a Programme model
    $programme = new Programme([
        'programme_code' => 'BSCS001',
        'programme_name' => 'Bachelor of Science in Computer Science',
        'programme_type' => 'degree',
        'level' => 'undergraduate',
        'duration' => 4,
        'faculty' => 'Faculty of Computing',
        'department' => 'Computer Science',
        'institution_code' => 'UDSM',
        'institution_name' => 'University of Dar es Salaam',
        'capacity' => 100,
        'available_slots' => 25,
        'minimum_grade' => 'B',
        'is_active' => true,
        'academic_year' => '2025/2026',
        'tuition_fee' => 1500000.00,
        'currency' => 'TZS',
        'mode_of_study' => 'Full Time'
    ]);

    $errors = $programme->validate();
    if (empty($errors)) {
        echo "✓ Programme data is valid\n";
        echo "  Programme: " . $programme->getProgrammeName() . "\n";
        echo "  Code: " . $programme->getProgrammeCode() . "\n";
        echo "  Level: " . $programme->getLevel() . "\n";
        echo "  Duration: " . $programme->getDuration() . " years\n";
        echo "  Is Undergraduate: " . ($programme->isUndergraduate() ? 'Yes' : 'No') . "\n";
        echo "  Has Available Slots: " . ($programme->hasAvailableSlots() ? 'Yes' : 'No') . "\n";
        echo "  Available Slots: " . $programme->getAvailableSlots() . "\n";
        echo "  Occupancy Rate: " . number_format($programme->getOccupancyRate(), 2) . "%\n";
        echo "  Application Open: " . ($programme->isApplicationOpen() ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Programme validation errors:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // ===== Example 2: Using Request Models =====
    echo "2. Using Request Models\n";
    echo "=======================\n";

    // Check Status Request
    echo "A. Check Status Request\n";
    $checkStatusRequest = CheckStatusRequest::forSingleApplicant(
        'S0123/0001/2023',
        'S0123/0001/2025',
        null
    );

    $errors = $checkStatusRequest->validate();
    if (empty($errors)) {
        echo "✓ Check status request is valid\n";
        echo "  Form Four Index: " . $checkStatusRequest->getFormFourIndexNumber() . "\n";
        echo "  Form Six Index: " . $checkStatusRequest->getFormSixIndexNumber() . "\n";

        // Make API call
        $response = $client->applicants()->checkStatus($checkStatusRequest);

        if ($response->isSuccess()) {
            echo "  ✓ API call successful\n";
            echo "  Status: " . $response->getStatusDescription() . "\n";
            echo "  Has Admission: " . ($response->hasAdmission() ? 'Yes' : 'No') . "\n";
            echo "  Can Apply: " . ($response->canApply() ? 'Yes' : 'No') . "\n";

            if ($response->hasAdmission()) {
                echo "  Institution: " . $response->getInstitutionName() . "\n";
                echo "  Programme: " . $response->getProgrammeName() . "\n";
                echo "  Admission Status: " . $response->getAdmissionStatus() . "\n";
                echo "  Confirmation Status: " . $response->getConfirmationStatus() . "\n";
            }
        } else {
            echo "  ✗ API call failed: " . $response->getMessage() . "\n";
        }
    } else {
        echo "✗ Check status request validation errors:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // Add Applicant Request
    echo "B. Add Applicant Request\n";
    $addApplicantRequest = AddApplicantRequest::fromApplicant($applicant, 'UDSM');

    $errors = $addApplicantRequest->validate();
    if (empty($errors)) {
        echo "✓ Add applicant request is valid\n";
        echo "  Institution Code: " . $addApplicantRequest->getInstitutionCode() . "\n";
        echo "  Applicant Name: " . $addApplicantRequest->getFirstName() . " " . $addApplicantRequest->getSurname() . "\n";
        echo "  Form Four Index: " . $addApplicantRequest->getFormFourIndexNumber() . "\n";
        echo "  Nationality: " . $addApplicantRequest->getNationality() . "\n";
        echo "  Category: " . $addApplicantRequest->getApplicantCategory() . "\n";

        // Make API call
        $response = $client->applicants()->add($addApplicantRequest);

        if ($response->isSuccessfullyAdded()) {
            echo "  ✓ Applicant added successfully\n";
            echo "  Applicant ID: " . $response->getApplicantId() . "\n";
            echo "  Registration Number: " . $response->getRegistrationNumber() . "\n";
            echo "  Application Status: " . $response->getApplicationStatus() . "\n";

            if ($response->hasWarnings()) {
                echo "  ⚠ Warnings:\n";
                foreach ($response->getWarnings() as $warning) {
                    echo "    - " . $warning . "\n";
                }
            }

            if ($response->isDuplicate()) {
                echo "  ⚠ Duplicate check result:\n";
                $duplicateInfo = $response->getDuplicateCheckResult();
                echo "    Existing ID: " . $duplicateInfo['existing_id'] . "\n";
            }
        } else {
            echo "  ✗ Failed to add applicant: " . $response->getMessage() . "\n";

            if ($response->hasValidationErrors()) {
                echo "  Validation errors:\n";
                foreach ($response->getValidationErrors() as $error) {
                    echo "    - " . $error . "\n";
                }
            }
        }
    } else {
        echo "✗ Add applicant request validation errors:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // Submit Programme Request
    echo "C. Submit Programme Request\n";
    $submitProgrammeRequest = SubmitProgrammeRequest::withMultipleProgrammes(
        'S0123/0001/2023',
        ['BSCS001', 'BSIT002', 'BSENG003']
    );

    // Add academic year and application round
    $submitProgrammeRequest->setAcademicYear('2025/2026')
                           ->setApplicationRound('First Round');

    // Update programme priority
    $submitProgrammeRequest->updateProgrammePriority('BSIT002', 1);
    $submitProgrammeRequest->updateProgrammePriority('BSCS001', 2);

    // Sort by priority
    $submitProgrammeRequest->sortProgrammesByPriority();

    $errors = $submitProgrammeRequest->validate();
    if (empty($errors)) {
        echo "✓ Submit programme request is valid\n";
        echo "  Form Four Index: " . $submitProgrammeRequest->getFormFourIndexNumber() . "\n";
        echo "  Academic Year: " . $submitProgrammeRequest->getAcademicYear() . "\n";
        echo "  Application Round: " . $submitProgrammeRequest->getApplicationRound() . "\n";
        echo "  Programme Count: " . $submitProgrammeRequest->getProgrammeCount() . "\n";

        echo "  Programmes:\n";
        foreach ($submitProgrammeRequest->getProgrammes() as $prog) {
            echo "    Priority " . $prog['priority'] . ": " . $prog['programme_code'] . "\n";
        }

        // Make API call
        $response = $client->applicants()->submitProgramme($submitProgrammeRequest);

        if ($response['status_code'] === 200) {
            echo "  ✓ Programme choices submitted successfully\n";
        } else {
            echo "  ✗ Failed to submit programme choices\n";
        }
    } else {
        echo "✗ Submit programme request validation errors:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // ===== Example 3: Fluent Interface Usage =====
    echo "3. Fluent Interface Usage\n";
    echo "=========================\n";

    $fluentApplicant = (new Applicant())
        ->setFirstName('Jane')
        ->setMiddleName('Marie')
        ->setSurname('Smith')
        ->setGender('F')
        ->setFormFourIndexNumber('S0124/0002/2023')
        ->setNationality('Tanzanian')
        ->setYearOfBirth(1999)
        ->setApplicantCategory('Private')
        ->setPhoneNumber('+255987654321')
        ->setEmailAddress('jane.smith@example.com')
        ->setDisabilityStatus(false);

    echo "✓ Created applicant using fluent interface\n";
    echo "  Full Name: " . $fluentApplicant->getFullName() . "\n";
    echo "  Form Four Index: " . $fluentApplicant->getFormFourIndexNumber() . "\n";
    echo "  Email: " . $fluentApplicant->getEmailAddress() . "\n";
    echo "\n";

    $fluentProgramme = (new Programme())
        ->setProgrammeCode('BSIT002')
        ->setProgrammeName('Bachelor of Science in Information Technology')
        ->setProgrammeType('degree')
        ->setLevel('undergraduate')
        ->setDuration(4)
        ->setFaculty('Faculty of Computing')
        ->setInstitutionCode('UDSM')
        ->setCapacity(80)
        ->setAvailableSlots(30)
        ->setIsActive(true)
        ->setTuitionFee(1800000.00)
        ->setCurrency('TZS');

    echo "✓ Created programme using fluent interface\n";
    echo "  Programme: " . $fluentProgramme->getProgrammeName() . "\n";
    echo "  Code: " . $fluentProgramme->getProgrammeCode() . "\n";
    echo "  Available Slots: " . $fluentProgramme->getAvailableSlots() . "\n";
    echo "  Occupancy Rate: " . number_format($fluentProgramme->getOccupancyRate(), 2) . "%\n";
    echo "\n";

    // ===== Example 4: Error Handling =====
    echo "4. Error Handling Examples\n";
    echo "==========================\n";

    // Invalid applicant data
    $invalidApplicant = new Applicant([
        'first_name' => '',  // Empty required field
        'surname' => 'Test',
        'gender' => 'Invalid',  // Invalid gender
        'form_four_index_number' => 'INVALID',  // Invalid format
        'year_of_birth' => 1900,  // Invalid year
        'email_address' => 'invalid-email'  // Invalid email
    ]);

    $errors = $invalidApplicant->validate();
    if (!empty($errors)) {
        echo "✗ Validation errors for invalid applicant:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    echo "\n";

    // Database logging statistics
    echo "5. Database Logging Statistics\n";
    echo "==============================\n";

    if ($client->getLogger()) {
        $stats = $client->getLogger()->getApiCallStats();
        echo "API Call Statistics:\n";
        echo "  Total calls: " . ($stats['total_calls'] ?? 0) . "\n";
        echo "  Successful calls: " . ($stats['successful_calls'] ?? 0) . "\n";
        echo "  Failed calls: " . ($stats['failed_calls'] ?? 0) . "\n";
        echo "  Average execution time: " . number_format($stats['avg_execution_time'] ?? 0, 3) . " seconds\n";
        echo "  Max execution time: " . number_format($stats['max_execution_time'] ?? 0, 3) . " seconds\n";
    } else {
        echo "Database logging is not enabled\n";
    }

} catch (ValidationException $e) {
    echo "Validation Error: " . $e->getMessage() . "\n";
    $errors = $e->getErrors();
    foreach ($errors as $error) {
        echo "- " . $error . "\n";
    }
} catch (TCUAPIException $e) {
    echo "TCU API Error: " . $e->getMessage() . "\n";
    $context = $e->getContext();
    if (!empty($context)) {
        echo "Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "Unexpected Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nModels usage examples completed!\n";
