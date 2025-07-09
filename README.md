# TCU API Client for PHP

A comprehensive PHP library for integrating with the Tanzania Commission for Universities (TCU) Integrated Admission System API.

**Company:** MBLogik  
**Author:** Ombeni Aidani <developer@mblogik.com>  
**Version:** 1.0.0

## Features

- **Enterprise-grade architecture** with proper error handling and logging
- **Comprehensive API coverage** for all TCU endpoints
- **Database logging** for monitoring and debugging
- **Robust HTTP client** with retry mechanisms
- **Data validation** and structured models
- **Multiple configuration options** for different environments

## Installation

```bash
composer require mblogik/tcuapiclient
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use MBLogik\TCUAPIClient\Client\TCUAPIClient;use MBLogik\TCUAPIClient\Config\Configuration;use MBLogik\TCUAPIClient\Models\Applicant;

// Configure the client
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'enable_database_logging' => true,
    'database' => [
        'host' => 'localhost',
        'database' => 'tcu_logs',
        'username' => 'db_user',
        'password' => 'db_password'
    ]
]);

// Initialize the client
$client = new TCUAPIClient($config);

// Check applicant status
$status = $client->applicants()->checkStatus('S0123/0001/2023');

// Add new applicant
$applicant = new Applicant([
    'first_name' => 'John',
    'surname' => 'Doe',
    'gender' => 'M',
    'form_four_index_number' => 'S0123/0001/2023',
    'nationality' => 'Tanzanian',
    'year_of_birth' => 2000
]);

$response = $client->applicants()->add($applicant);
```

## Configuration

### Basic Configuration

```php
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token'
]);
```

### Advanced Configuration

```php
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_security_token',
    'timeout' => 30,
    'retry_attempts' => 3,
    'enable_database_logging' => true,
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'db_user',
        'password' => 'db_password',
        'port' => 3306,
        'table_prefix' => 'tcu_api_'
    ]
]);
```

## Available Resources

### Applicant Resource

```php
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;
use MBLogik\TCUAPIClient\Models\Data\Applicant;

// Check status using request model
$request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023');
$response = $client->applicants()->checkStatus($request);

// Or using simple parameters
$response = $client->applicants()->checkStatus('S0123/0001/2023');

// Add applicant using request model
$applicant = new Applicant([
    'first_name' => 'John',
    'surname' => 'Doe',
    'gender' => 'M',
    'form_four_index_number' => 'S0123/0001/2023',
    'nationality' => 'Tanzanian',
    'year_of_birth' => 2000,
    'applicant_category' => 'Government'
]);

$response = $client->applicants()->add($applicant, 'INST001');

// Submit programme choices
$programmes = [
    ['programme_code' => 'PROG001', 'priority' => 1],
    ['programme_code' => 'PROG002', 'priority' => 2]
];
$client->applicants()->submitProgramme('S0123/0001/2023', $programmes);

// Submit enrolled students
$client->applicants()->submitEnrolledStudents($students);

// Submit graduates
$client->applicants()->submitGraduates($graduates);

// Foreign applicants
$client->applicants()->submitForeignApplicants($foreignApplicants);
```

### Admission Resource

```php
// Confirm admission
$client->admissions()->confirm('S0123/0001/2023', 'CONF123');

// Get admitted applicants
$client->admissions()->getAdmitted('PROG001');

// Get programmes
$client->admissions()->getProgrammes();

// Submit transfers
$client->admissions()->submitInternalTransfers($transfers);
```

### Dashboard Resource

```php
// Populate dashboard with programme statistics
$client->dashboard()->populate('DM038', 45, 60);

// Get statistics
$client->dashboard()->getStats('PROG001');
```

### Foreign Applicant Resource

```php
// Register foreign applicant
$applicantData = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'passportNumber' => 'AB123456',
    'nationality' => 'US',
    'dateOfBirth' => '1995-05-15',
    'emailAddress' => 'john.doe@example.com',
    'mobileNumber' => '+255766123456',
    'programmeCode' => 'UD023',
    'academicYear' => '2025/2026'
];
$client->foreignApplicants()->registerForeignApplicant($applicantData);

// Get foreign applicant details
$client->foreignApplicants()->getForeignApplicantDetails('AB123456');

// Process visa application
$visaData = [
    'passportNumber' => 'AB123456',
    'visaType' => 'student',
    'purposeOfVisit' => 'education',
    'durationOfStay' => '4 years'
];
$client->foreignApplicants()->processVisaApplication($visaData);

// Check visa status
$client->foreignApplicants()->getVisaStatus('VA2025001');

// Update applicant information
$updateData = [
    'mobileNumber' => '+255766654321',
    'emailAddress' => 'john.updated@example.com'
];
$client->foreignApplicants()->updateForeignApplicantInformation('AB123456', $updateData);
```

### Applicant Resource - Additional Methods

```php
// Resubmit applicant programme choices (endpoint 3.6)
$client->applicants()->resubmit($applicantData);

// Submit programme choices (endpoint 3.3)
$client->applicants()->submitProgrammeChoices($applicantData);
```

## Database Logging

The library provides comprehensive database logging for all API calls:

```php
// Get statistics
$stats = $client->getLogger()->getApiCallStats();

// Get recent errors
$errors = $client->getLogger()->getRecentErrors(10);
```

## Error Handling

```php
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\AuthenticationException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;

try {
    $response = $client->applicants()->checkStatus('S0123/0001/2023');
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
} catch (TCUAPIException $e) {
    // Handle general API errors
    $context = $e->getContext();
}
```

## Models

The package includes comprehensive models for structured data handling:

### Data Models

#### Applicant Model

```php
use MBLogik\TCUAPIClient\Models\Data\Applicant;

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
    'email_address' => 'john.doe@example.com'
]);

// Validate data
$errors = $applicant->validate();
if (empty($errors)) {
    $response = $client->applicants()->add($applicant, 'INST001');
}

// Use helper methods
echo $applicant->getFullName();
echo $applicant->isLocal() ? 'Local' : 'Foreign';
echo $applicant->hasFormSixResults() ? 'Has Form Six' : 'No Form Six';
```

#### Programme Model

```php
use MBLogik\TCUAPIClient\Models\Data\Programme;

$programme = new Programme([
    'programme_code' => 'BSCS001',
    'programme_name' => 'Bachelor of Science in Computer Science',
    'programme_type' => 'degree',
    'level' => 'undergraduate',
    'duration' => 4,
    'faculty' => 'Faculty of Computing',
    'institution_code' => 'UDSM',
    'capacity' => 100,
    'available_slots' => 25,
    'is_active' => true,
    'tuition_fee' => 1500000.00,
    'currency' => 'TZS'
]);

// Use helper methods
echo $programme->isUndergraduate() ? 'Undergraduate' : 'Postgraduate';
echo $programme->hasAvailableSlots() ? 'Available' : 'Full';
echo "Occupancy Rate: " . $programme->getOccupancyRate() . "%";
```

### Request Models

#### Check Status Request

```php
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;

// Single applicant
$request = CheckStatusRequest::forSingleApplicant(
    'S0123/0001/2023',
    'S0123/0001/2025', // Form six (optional)
    'AVN123456'        // AVN (optional)
);

// Multiple applicants
$request = CheckStatusRequest::forMultipleApplicants([
    'S0123/0001/2023',
    'S0124/0002/2023'
]);

// Applicant with multiple results
$request = CheckStatusRequest::forApplicantWithMultipleResults(
    'S0123/0001/2023',
    ['S0124/0001/2023'], // Other form four
    ['S0123/0001/2025']  // Other form six
);

$response = $client->applicants()->checkStatus($request);
```

#### Add Applicant Request

```php
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;

// Local applicant
$request = AddApplicantRequest::forLocalApplicant(
    'INST001',           // Institution code
    'John',              // First name
    'Doe',               // Surname
    'M',                 // Gender
    'S0123/0001/2023',   // Form four index
    2000,                // Year of birth
    'Government'         // Category
);

// Foreign applicant
$request = AddApplicantRequest::forForeignApplicant(
    'INST001',
    'Jane',
    'Smith',
    'F',
    'Kenyan',
    1999,
    'Private',
    'AVN123456'
);

$response = $client->applicants()->add($request);
```

#### Submit Programme Request

```php
use MBLogik\TCUAPIClient\Models\Request\SubmitProgrammeRequest;

// Single programme
$request = SubmitProgrammeRequest::withSingleProgramme(
    'S0123/0001/2023',
    'PROG001'
);

// Multiple programmes
$request = SubmitProgrammeRequest::withMultipleProgrammes(
    'S0123/0001/2023',
    ['PROG001', 'PROG002', 'PROG003']
);

// Advanced usage with fluent interface
$request = new SubmitProgrammeRequest([
    'form_four_index_number' => 'S0123/0001/2023',
    'academic_year' => '2025/2026'
]);

$request->addProgramme('PROG001', 1)
        ->addProgramme('PROG002', 2)
        ->updateProgrammePriority('PROG001', 2)
        ->updateProgrammePriority('PROG002', 1)
        ->sortProgrammesByPriority();

$response = $client->applicants()->submitProgramme($request);
```

### Response Models

#### Check Status Response

```php
$response = $client->applicants()->checkStatus($request);

if ($response->isSuccess()) {
    echo "Status: " . $response->getStatusDescription();
    echo "Has Admission: " . ($response->hasAdmission() ? 'Yes' : 'No');
    echo "Can Apply: " . ($response->canApply() ? 'Yes' : 'No');
    
    if ($response->hasAdmission()) {
        echo "Institution: " . $response->getInstitutionName();
        echo "Programme: " . $response->getProgrammeName();
        echo "Is Confirmed: " . ($response->isConfirmed() ? 'Yes' : 'No');
    }
    
    // Get comprehensive status summary
    $summary = $response->getStatusSummary();
    print_r($summary);
}
```

#### Add Applicant Response

```php
$response = $client->applicants()->add($request);

if ($response->isSuccessfullyAdded()) {
    echo "Applicant ID: " . $response->getApplicantId();
    echo "Registration Number: " . $response->getRegistrationNumber();
    echo "Status: " . $response->getApplicationStatus();
    
    if ($response->hasWarnings()) {
        echo "Warnings: " . implode(', ', $response->getWarnings());
    }
    
    if ($response->isDuplicate()) {
        echo "Duplicate detected";
        $duplicateInfo = $response->getDuplicateCheckResult();
    }
    
    // Get comprehensive summary
    $summary = $response->getAddSummary();
    print_r($summary);
}
```

## 📚 Documentation

Comprehensive documentation is available in the `/docs` directory:

### 🚀 **Quick References**
- **[Documentation Index](docs/README.md)** - Complete documentation overview
- **[Endpoint Implementation Status](docs/ENDPOINT_IMPLEMENTATION_STATUS.md)** - Current API implementation status
- **[Endpoints Guide](docs/ENDPOINTS.md)** - Complete endpoint reference and examples

### 🔒 **Security & Setup**
- **[Security Guidelines](docs/SECURITY.md)** - Security best practices and credential management
- **[Git Setup Guide](docs/GIT_SETUP_COMPLETE.md)** - Complete git configuration summary
- **[Database Setup](docs/DATABASE.md)** - Database configuration and migration guide

### 🛠️ **Development**
- **[Git Exclusions Guide](docs/GIT_EXCLUSIONS.md)** - Detailed explanation of git exclusions

## Examples

Check the `examples/` directory for comprehensive usage examples:

### 🚀 **Basic Usage**
- **[basic_usage.php](examples/basic_usage.php)**: Basic API operations and client setup
- **[config_examples.php](examples/config_examples.php)**: Different configuration setups and options
- **[models_usage.php](examples/models_usage.php)**: Working with data models and validation

### 📋 **Session-Based API Examples**
- **[session1_endpoints.php](examples/session1_endpoints.php)**: Core Applicant Operations (3.1-3.5)
- **[session2_endpoints.php](examples/session2_endpoints.php)**: Administrative Operations (3.6-3.10)  
- **[session3_endpoints.php](examples/session3_endpoints.php)**: Confirmation and Transfers (3.11-3.15)
- **[session4_endpoints.php](examples/session4_endpoints.php)**: Verification and Enrollment (3.16-3.20)
- **[session5_endpoints.php](examples/session5_endpoints.php)**: Graduates and Staff (3.21-3.25)
- **[session6_endpoints.php](examples/session6_endpoints.php)**: Non-degree and Postgraduate (3.26-3.30)
- **[session7_endpoints.php](examples/session7_endpoints.php)**: Foreign Applicants (3.31-3.35)
- **[Session7_ForeignApplicants_Usage.php](examples/Session7_ForeignApplicants_Usage.php)**: Detailed foreign applicant examples with validation

### 🏗️ **Advanced Usage**
- **[foreign_applicants.php](examples/foreign_applicants.php)**: Foreign applicant processing and management
- **[migrations_example.php](examples/migrations_example.php)**: Database setup and migrations

## Requirements

- PHP 8.0 or higher
- MySQL/MariaDB (for database logging)
- cURL extension
- JSON extension

## License

This package is licensed under the MIT License.

## Support

For issues and questions, please contact the development team or check the API documentation.
