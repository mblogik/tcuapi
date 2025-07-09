<?php

/**
 * Test runner for TCU API Client
 * 
 * This script runs the PHPUnit tests without requiring a full composer autoload
 * It manually includes the necessary files and runs basic tests
 */

echo "=== TCU API Client Test Runner ===\n\n";

// Basic autoloader
spl_autoload_register(function ($class) {
    $prefix = 'MBLogik\\TCUAPIClient\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Test results
$tests = [];
$passed = 0;
$failed = 0;

function runTest($testName, $testFunction) {
    global $tests, $passed, $failed;
    
    try {
        echo "Running: {$testName}... ";
        $result = $testFunction();
        if ($result) {
            echo "✓ PASSED\n";
            $passed++;
        } else {
            echo "✗ FAILED\n";
            $failed++;
        }
        $tests[] = ['name' => $testName, 'result' => $result ? 'PASSED' : 'FAILED'];
    } catch (Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
        $failed++;
        $tests[] = ['name' => $testName, 'result' => 'ERROR: ' . $e->getMessage()];
    }
}

// Test 1: Configuration Creation
runTest('Configuration Creation', function() {
    $config = new \MBLogik\TCUAPIClient\Config\Configuration([
        'username' => 'test_user',
        'security_token' => 'test_token',
        'base_url' => 'https://api.tcu.go.tz',
        'timeout' => 30
    ]);
    
    return $config->getUsername() === 'test_user' && 
           $config->getSecurityToken() === 'test_token' &&
           $config->getBaseUrl() === 'https://api.tcu.go.tz' &&
           $config->getTimeout() === 30;
});

// Test 2: Configuration Validation
runTest('Configuration Validation', function() {
    $config = new \MBLogik\TCUAPIClient\Config\Configuration([
        'username' => 'test_user',
        'security_token' => 'test_token'
    ]);
    
    $errors = $config->validate();
    return empty($errors);
});

// Test 3: Configuration Validation Failure
runTest('Configuration Validation Failure', function() {
    $config = new \MBLogik\TCUAPIClient\Config\Configuration([
        'base_url' => 'invalid-url'
    ]);
    
    $errors = $config->validate();
    return !empty($errors) && 
           in_array('Username is required', $errors) &&
           in_array('Security token is required', $errors) &&
           in_array('Invalid base URL format', $errors);
});

// Test 4: Database Configuration
runTest('Database Configuration', function() {
    $config = new \MBLogik\TCUAPIClient\Config\DatabaseConfig([
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'test_db',
        'username' => 'test_user',
        'password' => 'test_pass'
    ]);
    
    return $config->getDriver() === 'mysql' &&
           $config->getHost() === 'localhost' &&
           $config->getDatabase() === 'test_db' &&
           $config->getUsername() === 'test_user' &&
           $config->getPassword() === 'test_pass';
});

// Test 5: Applicant Model Creation
runTest('Applicant Model Creation', function() {
    $applicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant([
        'first_name' => 'John',
        'middle_name' => 'Michael',
        'surname' => 'Doe',
        'gender' => 'M',
        'form_four_index_number' => 'S0123/0001/2023',
        'nationality' => 'Tanzanian',
        'year_of_birth' => 2000,
        'applicant_category' => 'Government'
    ]);
    
    return $applicant->getFirstName() === 'John' &&
           $applicant->getMiddleName() === 'Michael' &&
           $applicant->getSurname() === 'Doe' &&
           $applicant->getFullName() === 'John Michael Doe' &&
           $applicant->getGender() === 'M' &&
           $applicant->getFormFourIndexNumber() === 'S0123/0001/2023' &&
           $applicant->getNationality() === 'Tanzanian' &&
           $applicant->getYearOfBirth() === 2000 &&
           $applicant->getApplicantCategory() === 'Government';
});

// Test 6: Applicant Model Validation
runTest('Applicant Model Validation', function() {
    $applicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant([
        'first_name' => 'John',
        'surname' => 'Doe',
        'gender' => 'M',
        'form_four_index_number' => 'S0123/0001/2023',
        'nationality' => 'Tanzanian',
        'year_of_birth' => 2000,
        'applicant_category' => 'Government'
    ]);
    
    $errors = $applicant->validate();
    return empty($errors);
});

// Test 7: Applicant Model Validation Failure
runTest('Applicant Model Validation Failure', function() {
    $applicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant([
        'first_name' => '', // Empty required field
        'surname' => 'Doe',
        'gender' => 'Invalid', // Invalid gender
        'form_four_index_number' => 'INVALID', // Invalid format
        'nationality' => 'Tanzanian',
        'year_of_birth' => 1900, // Invalid year
        'applicant_category' => 'Government'
    ]);
    
    $errors = $applicant->validate();
    return !empty($errors) &&
           in_array('Field \'first_name\' is required', $errors) &&
           in_array('Gender must be M, F, Male, or Female', $errors) &&
           in_array('Form four index number must be in format: S0123/0001/2023', $errors) &&
           in_array('Year of birth must be between 1950 and ' . date('Y'), $errors);
});

// Test 8: Applicant Utility Methods
runTest('Applicant Utility Methods', function() {
    $localApplicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant(['nationality' => 'Tanzanian']);
    $foreignApplicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant(['nationality' => 'Kenyan']);
    $applicantWithFormSix = new \MBLogik\TCUAPIClient\Models\Data\Applicant(['form_six_index_number' => 'S0123/0001/2025']);
    $applicantWithAvn = new \MBLogik\TCUAPIClient\Models\Data\Applicant(['avn' => 'AVN123456']);
    $applicantWithDisability = new \MBLogik\TCUAPIClient\Models\Data\Applicant(['disability_status' => true]);
    
    return $localApplicant->isLocal() &&
           !$localApplicant->isForeign() &&
           !$foreignApplicant->isLocal() &&
           $foreignApplicant->isForeign() &&
           $applicantWithFormSix->hasFormSixResults() &&
           $applicantWithAvn->hasDiplomaResults() &&
           $applicantWithDisability->hasDisability();
});

// Test 9: Programme Model Creation
runTest('Programme Model Creation', function() {
    $programme = new \MBLogik\TCUAPIClient\Models\Data\Programme([
        'programme_code' => 'BSCS001',
        'programme_name' => 'Bachelor of Science in Computer Science',
        'programme_type' => 'degree',
        'level' => 'undergraduate',
        'duration' => 4,
        'institution_code' => 'UDSM',
        'capacity' => 100,
        'available_slots' => 25,
        'is_active' => true
    ]);
    
    return $programme->getProgrammeCode() === 'BSCS001' &&
           $programme->getProgrammeName() === 'Bachelor of Science in Computer Science' &&
           $programme->getProgrammeType() === 'degree' &&
           $programme->getLevel() === 'undergraduate' &&
           $programme->getDuration() === 4 &&
           $programme->getInstitutionCode() === 'UDSM' &&
           $programme->getCapacity() === 100 &&
           $programme->getAvailableSlots() === 25 &&
           $programme->isActive();
});

// Test 10: Programme Utility Methods
runTest('Programme Utility Methods', function() {
    $undergraduateProgramme = new \MBLogik\TCUAPIClient\Models\Data\Programme(['level' => 'undergraduate']);
    $postgraduateProgramme = new \MBLogik\TCUAPIClient\Models\Data\Programme(['level' => 'postgraduate']);
    $programmeWithSlots = new \MBLogik\TCUAPIClient\Models\Data\Programme(['available_slots' => 10]);
    $programmeWithoutSlots = new \MBLogik\TCUAPIClient\Models\Data\Programme(['available_slots' => 0]);
    $programmeWithCapacity = new \MBLogik\TCUAPIClient\Models\Data\Programme(['capacity' => 100, 'available_slots' => 25]);
    
    return $undergraduateProgramme->isUndergraduate() &&
           !$undergraduateProgramme->isPostgraduate() &&
           !$postgraduateProgramme->isUndergraduate() &&
           $postgraduateProgramme->isPostgraduate() &&
           $programmeWithSlots->hasAvailableSlots() &&
           !$programmeWithoutSlots->hasAvailableSlots() &&
           $programmeWithCapacity->getOccupancyRate() === 75.0;
});

// Test 11: Check Status Request
runTest('Check Status Request', function() {
    $request = \MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest::forSingleApplicant('S0123/0001/2023');
    
    return $request->getFormFourIndexNumber() === 'S0123/0001/2023' &&
           $request->getEndpoint() === '/applicants/checkStatus' &&
           $request->getMethod() === 'POST';
});

// Test 12: Check Status Request Multiple Applicants
runTest('Check Status Request Multiple Applicants', function() {
    $indexNumbers = ['S0123/0001/2023', 'S0124/0002/2023'];
    $request = \MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest::forMultipleApplicants($indexNumbers);
    
    return $request->getFormFourIndexNumbers() === $indexNumbers &&
           $request->getEndpoint() === '/applicants/checkStatus' &&
           $request->getMethod() === 'POST';
});

// Test 13: Add Applicant Request
runTest('Add Applicant Request', function() {
    $request = \MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest::forLocalApplicant(
        'INST001',
        'John',
        'Doe',
        'M',
        'S0123/0001/2023',
        2000,
        'Government'
    );
    
    return $request->getInstitutionCode() === 'INST001' &&
           $request->getFirstName() === 'John' &&
           $request->getSurname() === 'Doe' &&
           $request->getGender() === 'M' &&
           $request->getFormFourIndexNumber() === 'S0123/0001/2023' &&
           $request->getYearOfBirth() === 2000 &&
           $request->getApplicantCategory() === 'Government' &&
           $request->getEndpoint() === '/applicants/add' &&
           $request->getMethod() === 'POST';
});

// Test 14: Exception Classes
runTest('Exception Classes', function() {
    $tcuException = new \MBLogik\TCUAPIClient\Exceptions\TCUAPIException('Test message', 400, null, ['context' => 'test']);
    $validationException = new \MBLogik\TCUAPIClient\Exceptions\ValidationException('Validation failed', ['error1', 'error2']);
    $authException = new \MBLogik\TCUAPIClient\Exceptions\AuthenticationException('Auth failed');
    $networkException = new \MBLogik\TCUAPIClient\Exceptions\NetworkException('Network error');
    
    return $tcuException->getMessage() === 'Test message' &&
           $tcuException->getCode() === 400 &&
           $tcuException->getContext() === ['context' => 'test'] &&
           $validationException->getErrors() === ['error1', 'error2'] &&
           $authException->getCode() === 401 &&
           $networkException instanceof \MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
});

// Test 15: JSON Serialization
runTest('JSON Serialization', function() {
    $applicant = new \MBLogik\TCUAPIClient\Models\Data\Applicant([
        'first_name' => 'John',
        'surname' => 'Doe',
        'gender' => 'M'
    ]);
    
    $json = $applicant->toJson();
    $decoded = json_decode($json, true);
    
    return $decoded['first_name'] === 'John' &&
           $decoded['surname'] === 'Doe' &&
           $decoded['gender'] === 'M';
});

echo "\n=== Test Results ===\n";
echo "Total tests: " . count($tests) . "\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";
echo "Success rate: " . number_format(($passed / count($tests)) * 100, 2) . "%\n";

if ($failed > 0) {
    echo "\nFailed tests:\n";
    foreach ($tests as $test) {
        if ($test['result'] !== 'PASSED') {
            echo "  - {$test['name']}: {$test['result']}\n";
        }
    }
}

echo "\n=== Test Summary ===\n";
if ($failed === 0) {
    echo "🎉 All tests passed! The TCU API Client is working correctly.\n";
} else {
    echo "⚠ {$failed} test(s) failed. Please review the results above.\n";
}

echo "\n=== End of Test Runner ===\n";