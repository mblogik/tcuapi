# TCU API Client - Endpoints Documentation

## Overview

This document provides comprehensive documentation for all implemented TCU API endpoints, including request/response models, usage examples, and best practices.

## Table of Contents

1. [Applicant Management](#applicant-management)
2. [Admission Management](#admission-management)  
3. [Dashboard and Reporting](#dashboard-and-reporting)
4. [Foreign Applicants](#foreign-applicants)
5. [Postgraduate Programmes](#postgraduate-programmes)
6. [Transfer Management](#transfer-management)
7. [Common Response Patterns](#common-response-patterns)

---

## Applicant Management

### 1. Check Applicant Status

**Purpose**: Check if an applicant has prior admission, discontinuation, graduation, or current admission status.

**Endpoint**: `POST /applicants/checkStatus`

**Request Model**: `CheckStatusRequest`

**Response Model**: `CheckStatusResponse`

#### Basic Usage

```php
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;

// Single applicant check
$request = CheckStatusRequest::forSingleApplicant(
    'S0123/0001/2023',
    'S0123/0001/2025', // Form six (optional)
    'AVN123456'        // AVN for diploma holders (optional)
);

$response = $client->applicants()->checkStatus($request);

// Check status
if ($response->isSuccess()) {
    if ($response->hasAdmission()) {
        echo "Applicant has prior admission\n";
        echo "Institution: " . $response->getInstitutionName() . "\n";
        echo "Programme: " . $response->getProgrammeName() . "\n";
        echo "Status: " . $response->getAdmissionStatus() . "\n";
    } else {
        echo "Applicant can apply\n";
    }
}
```

#### Multiple Applicants Check

```php
$request = CheckStatusRequest::forMultipleApplicants([
    'S0123/0001/2023',
    'S0124/0002/2023',
    'S0125/0003/2023'
]);

$response = $client->applicants()->checkStatus($request);
```

#### Applicant with Multiple Results

```php
$request = CheckStatusRequest::forApplicantWithMultipleResults(
    'S0123/0001/2023',                    // Primary form four
    ['S0124/0001/2023', 'S0125/0001/2023'], // Other form four results
    ['S0123/0001/2025']                   // Other form six results
);

$response = $client->applicants()->checkStatus($request);
```

**Response Properties**:
- `form_four_index_number` - Applicant's form four index
- `admission_status` - Current admission status
- `admission_year` - Year of admission
- `institution_code` - Institution code
- `institution_name` - Institution name
- `programme_code` - Programme code
- `programme_name` - Programme name
- `confirmation_status` - Confirmation status
- `graduation_status` - Graduation status
- `multiple_admissions` - Array of multiple admissions

**Response Methods**:
- `hasAdmission()` - Check if applicant has admission
- `isAdmitted()` - Check if currently admitted
- `isConfirmed()` - Check if admission is confirmed
- `hasGraduated()` - Check if applicant has graduated
- `isDiscontinued()` - Check if applicant discontinued
- `canApply()` - Check if applicant can apply for new admission

---

### 2. Add Applicant

**Purpose**: Register a new applicant in the TCU system.

**Endpoint**: `POST /applicants/add`

**Request Model**: `AddApplicantRequest`

**Response Model**: `AddApplicantResponse`

#### Basic Usage

```php
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;

// Create request for local applicant
$request = AddApplicantRequest::forLocalApplicant(
    'INST001',           // Institution code
    'John',              // First name
    'Doe',               // Surname
    'M',                 // Gender
    'S0123/0001/2023',   // Form four index
    2000,                // Year of birth
    'Government',        // Applicant category
    'S0123/0001/2025'    // Form six index (optional)
);

// Add optional information
$request->setMiddleName('Michael')
        ->setPhoneNumber('+255123456789')
        ->setEmailAddress('john.doe@example.com')
        ->setPhysicalAddress('Dar es Salaam, Tanzania');

$response = $client->applicants()->add($request);

if ($response->isSuccessfullyAdded()) {
    echo "Applicant added successfully\n";
    echo "Applicant ID: " . $response->getApplicantId() . "\n";
    echo "Registration Number: " . $response->getRegistrationNumber() . "\n";
    
    if ($response->hasWarnings()) {
        echo "Warnings: " . implode(', ', $response->getWarnings()) . "\n";
    }
} else {
    echo "Failed to add applicant\n";
    print_r($response->getValidationErrors());
}
```

#### Foreign Applicant

```php
$request = AddApplicantRequest::forForeignApplicant(
    'INST001',           // Institution code
    'Jane',              // First name
    'Smith',             // Surname
    'F',                 // Gender
    'Kenyan',            // Nationality
    1999,                // Year of birth
    'Private',           // Applicant category
    'AVN123456'          // AVN (optional)
);

$response = $client->applicants()->add($request);
```

#### Using Applicant Model

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
    'email_address' => 'john.doe@example.com',
    'disability_status' => false
]);

// Validate applicant data
$errors = $applicant->validate();
if (empty($errors)) {
    $request = AddApplicantRequest::fromApplicant($applicant, 'INST001');
    $response = $client->applicants()->add($request);
}
```

**Response Properties**:
- `applicant_id` - Unique applicant identifier
- `registration_number` - Registration number
- `application_status` - Application status
- `validation_errors` - Array of validation errors
- `warnings` - Array of warnings
- `duplicate_check_result` - Duplicate check information
- `next_steps` - Array of next steps

**Response Methods**:
- `isSuccessfullyAdded()` - Check if applicant was successfully added
- `hasValidationErrors()` - Check if there are validation errors
- `hasWarnings()` - Check if there are warnings
- `isDuplicate()` - Check if applicant is duplicate
- `requiresManualReview()` - Check if manual review is required
- `canProceedToNext()` - Check if can proceed to next step

---

### 3. Submit Programme Choices

**Purpose**: Submit applicant's programme choices with priorities.

**Endpoint**: `POST /applicants/submitProgramme`

**Request Model**: `SubmitProgrammeRequest`

**Response Model**: `SubmitProgrammeResponse`

#### Basic Usage

```php
use MBLogik\TCUAPIClient\Models\Request\SubmitProgrammeRequest;

// Single programme
$request = SubmitProgrammeRequest::withSingleProgramme(
    'S0123/0001/2023',
    'PROG001'
);

$response = $client->applicants()->submitProgramme($request);
```

#### Multiple Programmes

```php
$request = SubmitProgrammeRequest::withMultipleProgrammes(
    'S0123/0001/2023',
    ['PROG001', 'PROG002', 'PROG003']
);

$response = $client->applicants()->submitProgramme($request);
```

#### Advanced Programme Selection

```php
$request = new SubmitProgrammeRequest([
    'form_four_index_number' => 'S0123/0001/2023',
    'academic_year' => '2025/2026',
    'application_round' => 'First Round'
]);

// Add programmes with custom priorities
$request->addProgramme('PROG001', 1)
        ->addProgramme('PROG002', 2)
        ->addProgramme('PROG003', 3);

// Add programme with additional details
$request->addProgrammeWithDetails('PROG004', 4, [
    'preference_reason' => 'Career alignment',
    'special_requirements' => 'Laboratory access needed'
]);

// Update priority
$request->updateProgrammePriority('PROG002', 1);
$request->updateProgrammePriority('PROG001', 2);

// Sort by priority
$request->sortProgrammesByPriority();

$response = $client->applicants()->submitProgramme($request);

if ($response->isSuccess()) {
    echo "Programme choices submitted successfully\n";
    echo "Total programmes: " . $request->getProgrammeCount() . "\n";
}
```

---

### 4. Get Applicant Verification Status

**Purpose**: Check the verification status of applicants for a specific programme.

**Endpoint**: `POST /applicants/getApplicantVerificationStatus`

#### Usage

```php
$response = $client->applicants()->getVerificationStatus('PROG001');

if ($response->isSuccess()) {
    $applicants = $response->getData();
    
    foreach ($applicants as $applicant) {
        echo "Index: " . $applicant['form_four_index_number'] . "\n";
        echo "Status: " . $applicant['verification_status'] . "\n";
        echo "Verification Date: " . $applicant['verification_date'] . "\n";
        echo "---\n";
    }
}
```

---

### 5. Submit Enrolled Students

**Purpose**: Submit students who have enrolled/registered for undergraduate programmes.

**Endpoint**: `POST /applicants/submitEnrolledStudents`

#### Usage

```php
$enrolledStudents = [
    [
        'form_four_index_number' => 'S0123/0001/2023',
        'programme_code' => 'PROG001',
        'registration_number' => 'REG001/2025',
        'enrollment_date' => '2025-01-15',
        'academic_year' => '2025/2026',
        'year_of_study' => 1,
        'mode_of_study' => 'Full Time',
        'student_category' => 'Government'
    ],
    [
        'form_four_index_number' => 'S0124/0002/2023',
        'programme_code' => 'PROG002',
        'registration_number' => 'REG002/2025',
        'enrollment_date' => '2025-01-15',
        'academic_year' => '2025/2026',
        'year_of_study' => 1,
        'mode_of_study' => 'Part Time',
        'student_category' => 'Private'
    ]
];

$response = $client->applicants()->submitEnrolledStudents($enrolledStudents);

if ($response->isSuccess()) {
    echo "Enrolled students submitted successfully\n";
    $data = $response->getData();
    echo "Total submitted: " . count($data) . "\n";
}
```

---

### 6. Submit Graduates

**Purpose**: Submit information about graduated students.

**Endpoint**: `POST /applicants/submitGraduates`

#### Usage

```php
$graduates = [
    [
        'first_name' => 'John',
        'middle_name' => 'Michael',
        'surname' => 'Doe',
        'gender' => 'M',
        'form_four_index_number' => 'S0123/0001/2023',
        'registration_number' => 'REG001/2025',
        'programme_code' => 'PROG001',
        'graduation_date' => '2025-01-15',
        'academic_year' => '2024/2025',
        'classification' => 'First Class',
        'gpa' => 4.5,
        'thesis_title' => 'Research on AI Applications',
        'supervisor' => 'Dr. Jane Smith'
    ]
];

$response = $client->applicants()->submitGraduates($graduates);

if ($response->isSuccess()) {
    echo "Graduates submitted successfully\n";
}
```

---

## Admission Management

### 1. Confirm Admission

**Purpose**: Confirm an applicant's admission to a programme.

**Endpoint**: `POST /admission/confirm`

#### Usage

```php
$response = $client->admissions()->confirm(
    'S0123/0001/2023',  // Form four index number
    'CONF123456'        // Confirmation code
);

if ($response->isSuccess()) {
    echo "Admission confirmed successfully\n";
} else {
    echo "Failed to confirm admission: " . $response->getMessage() . "\n";
}
```

---

### 2. Unconfirm Admission

**Purpose**: Cancel/unconfirm an applicant's admission.

**Endpoint**: `POST /admission/unconfirm`

#### Usage

```php
$response = $client->admissions()->unconfirm(
    'S0123/0001/2023',               // Form four index number
    'Changed mind about programme'    // Reason (optional)
);

if ($response->isSuccess()) {
    echo "Admission unconfirmed successfully\n";
}
```

---

### 3. Get Admitted Applicants

**Purpose**: Retrieve list of admitted applicants for a programme.

**Endpoint**: `POST /admission/getAdmitted`

#### Usage

```php
$response = $client->admissions()->getAdmitted('PROG001');

if ($response->isSuccess()) {
    $admittedApplicants = $response->getData();
    
    foreach ($admittedApplicants as $applicant) {
        echo "Name: " . $applicant['full_name'] . "\n";
        echo "Index: " . $applicant['form_four_index_number'] . "\n";
        echo "Programme: " . $applicant['programme_name'] . "\n";
        echo "Admission Date: " . $applicant['admission_date'] . "\n";
        echo "Status: " . $applicant['status'] . "\n";
        echo "---\n";
    }
}
```

---

### 4. Get Programmes with Admitted Candidates

**Purpose**: Get list of programmes that have admitted candidates.

**Endpoint**: `POST /admission/getProgrammes`

#### Usage

```php
$response = $client->admissions()->getProgrammes();

if ($response->isSuccess()) {
    $programmes = $response->getData();
    
    foreach ($programmes as $programme) {
        echo "Programme: " . $programme['programme_name'] . "\n";
        echo "Code: " . $programme['programme_code'] . "\n";
        echo "Admitted Count: " . $programme['admitted_count'] . "\n";
        echo "Capacity: " . $programme['capacity'] . "\n";
        echo "---\n";
    }
}
```

---

### 5. Request Confirmation Code

**Purpose**: Request a confirmation code for multiple admission students.

**Endpoint**: `POST /admission/requestConfirmationCode`

#### Usage

```php
$response = $client->admissions()->requestConfirmationCode('S0123/0001/2023');

if ($response->isSuccess()) {
    $data = $response->getData();
    echo "Confirmation code sent to: " . $data['contact_method'] . "\n";
    echo "Code expires at: " . $data['expiry_time'] . "\n";
}
```

---

### 6. Cancel/Reject Admission

**Purpose**: Cancel or reject an applicant's admission.

**Endpoint**: `POST /admission/reject`

#### Usage

```php
$response = $client->admissions()->reject(
    'S0123/0001/2023',
    'Did not meet minimum requirements'
);

if ($response->isSuccess()) {
    echo "Admission rejected successfully\n";
}
```

---

## Dashboard and Reporting

### 1. Populate Dashboard

**Purpose**: Submit programme statistics for dashboard display.

**Endpoint**: `POST /dashboard/populate`

#### Usage

```php
$response = $client->dashboard()->populate(
    'PROG001',  // Programme code
    45,         // Male applicants
    30,         // Female applicants
    [           // Additional data
        'total_applications' => 75,
        'admission_year' => '2025/2026',
        'application_deadline' => '2025-03-15',
        'available_slots' => 50
    ]
);

if ($response->isSuccess()) {
    echo "Dashboard populated successfully\n";
}
```

---

### 2. Get Dashboard Statistics

**Purpose**: Retrieve dashboard statistics.

#### Usage

```php
// Get all statistics
$response = $client->dashboard()->getStats();

// Get statistics for specific programme
$response = $client->dashboard()->getStats('PROG001');

if ($response->isSuccess()) {
    $stats = $response->getData();
    
    echo "Total Applications: " . $stats['total_applications'] . "\n";
    echo "Male Applicants: " . $stats['male_applicants'] . "\n";
    echo "Female Applicants: " . $stats['female_applicants'] . "\n";
    echo "Admitted: " . $stats['admitted'] . "\n";
    echo "Confirmed: " . $stats['confirmed'] . "\n";
}
```

---

### 3. Get Admission Summary

**Purpose**: Get admission summary for reporting.

#### Usage

```php
$response = $client->dashboard()->getAdmissionSummary(
    '2025/2026',  // Academic year
    'PROG001'     // Programme code (optional)
);

if ($response->isSuccess()) {
    $summary = $response->getData();
    
    echo "Academic Year: " . $summary['academic_year'] . "\n";
    echo "Total Programmes: " . $summary['total_programmes'] . "\n";
    echo "Total Applications: " . $summary['total_applications'] . "\n";
    echo "Total Admissions: " . $summary['total_admissions'] . "\n";
    echo "Confirmation Rate: " . $summary['confirmation_rate'] . "%\n";
}
```

---

## Foreign Applicants

### 1. Submit Foreign Applicants

**Purpose**: Submit foreign applicants for bachelor's degree programmes.

**Endpoint**: `POST /applicants/submitForeignApplicants`

#### Usage

```php
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
                'grades' => 'A, B, B',
                'subjects' => ['Mathematics', 'Physics', 'Chemistry']
            ]
        ],
        'programme_choices' => [
            [
                'programme_code' => 'PROG001',
                'priority' => 1,
                'institution_code' => 'INST001'
            ]
        ],
        'supporting_documents' => [
            'academic_transcripts',
            'passport_copy',
            'english_proficiency_certificate'
        ]
    ]
];

$response = $client->applicants()->submitForeignApplicants($foreignApplicants);

if ($response->isSuccess()) {
    echo "Foreign applicants submitted successfully\n";
    $data = $response->getData();
    
    foreach ($data as $applicant) {
        echo "Name: " . $applicant['full_name'] . "\n";
        echo "Application ID: " . $applicant['application_id'] . "\n";
        echo "Status: " . $applicant['status'] . "\n";
        echo "---\n";
    }
}
```

---

### 2. Submit Foreign Admitted (Direct Entry)

**Purpose**: Submit admitted foreign students under direct entry.

**Endpoint**: `POST /applicants/submitForeignAdmittedDirect`

#### Usage

```php
$admittedDirectEntry = [
    [
        'first_name' => 'Jane',
        'middle_name' => 'Marie',
        'surname' => 'Smith',
        'gender' => 'F',
        'nationality' => 'Kenyan',
        'passport_number' => 'P1234567',
        'programme_code' => 'PROG001',
        'institution_code' => 'INST001',
        'admission_year' => '2025/2026',
        'entry_type' => 'Direct',
        'admission_date' => '2025-01-15',
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

$response = $client->applicants()->submitForeignAdmittedDirect($admittedDirectEntry);
```

---

### 3. Submit Foreign Admitted (Equivalent Entry)

**Purpose**: Submit admitted foreign students under equivalent entry.

**Endpoint**: `POST /applicants/submitForeignAdmittedEquivalent`

#### Usage

```php
$admittedEquivalentEntry = [
    [
        'first_name' => 'David',
        'middle_name' => 'Johnson',
        'surname' => 'Williams',
        'gender' => 'M',
        'nationality' => 'Ugandan',
        'passport_number' => 'P7654321',
        'programme_code' => 'PROG002',
        'institution_code' => 'INST001',
        'admission_year' => '2025/2026',
        'entry_type' => 'Equivalent',
        'admission_date' => '2025-01-15',
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
                'issue_date' => '2024-12-15',
                'expiry_date' => '2027-12-15'
            ]
        ]
    ]
];

$response = $client->applicants()->submitForeignAdmittedEquivalent($admittedEquivalentEntry);
```

---

## Error Handling

### Common Error Patterns

```php
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Exceptions\AuthenticationException;
use MBLogik\TCUAPIClient\Exceptions\NetworkException;

try {
    $response = $client->applicants()->checkStatus($request);
    
    if ($response->isSuccess()) {
        // Handle successful response
        $data = $response->getData();
    } else {
        // Handle API error response
        echo "API Error: " . $response->getMessage() . "\n";
        echo "Status Code: " . $response->getStatusCode() . "\n";
        
        if ($response->hasErrors()) {
            print_r($response->getErrors());
        }
    }
    
} catch (ValidationException $e) {
    echo "Validation Error: " . $e->getMessage() . "\n";
    $errors = $e->getErrors();
    foreach ($errors as $error) {
        echo "- " . $error . "\n";
    }
    
} catch (AuthenticationException $e) {
    echo "Authentication Error: " . $e->getMessage() . "\n";
    echo "Please check your credentials\n";
    
} catch (NetworkException $e) {
    echo "Network Error: " . $e->getMessage() . "\n";
    echo "Please check your internet connection\n";
    
} catch (TCUAPIException $e) {
    echo "TCU API Error: " . $e->getMessage() . "\n";
    $context = $e->getContext();
    if (!empty($context)) {
        echo "Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "Unexpected Error: " . $e->getMessage() . "\n";
}
```

---

## Best Practices

### 1. Request Validation

Always validate requests before sending:

```php
$request = new CheckStatusRequest($data);

$errors = $request->validate();
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "Validation Error: " . $error . "\n";
    }
    return;
}

$response = $client->applicants()->checkStatus($request);
```

### 2. Response Handling

Always check response status:

```php
$response = $client->applicants()->add($request);

if ($response->isSuccess()) {
    // Process successful response
    $data = $response->getData();
} else {
    // Handle error response
    echo "Error: " . $response->getMessage() . "\n";
    
    if ($response->hasErrors()) {
        foreach ($response->getErrors() as $error) {
            echo "- " . $error . "\n";
        }
    }
}
```

### 3. Logging and Monitoring

Enable database logging for monitoring:

```php
$config = new Configuration([
    'username' => 'your_username',
    'security_token' => 'your_token',
    'enable_database_logging' => true,
    'database' => [
        'host' => 'localhost',
        'database' => 'tcu_api_logs',
        'username' => 'root',
        'password' => 'password'
    ]
]);

$client = new TCUAPIClient($config);

// After making requests, you can check statistics
$logger = $client->getLogger();
if ($logger) {
    $stats = $logger->getApiCallStats();
    echo "Total API calls: " . $stats['total_calls'] . "\n";
    echo "Success rate: " . ($stats['successful_calls'] / $stats['total_calls'] * 100) . "%\n";
}
```

### 4. Batch Operations

For multiple operations, use batch processing:

```php
$applicants = [
    ['name' => 'John Doe', 'index' => 'S0123/0001/2023'],
    ['name' => 'Jane Smith', 'index' => 'S0124/0002/2023'],
    // ... more applicants
];

$results = [];
foreach ($applicants as $applicantData) {
    try {
        $request = CheckStatusRequest::forSingleApplicant($applicantData['index']);
        $response = $client->applicants()->checkStatus($request);
        $results[] = [
            'applicant' => $applicantData,
            'status' => $response->getStatusSummary()
        ];
    } catch (Exception $e) {
        $results[] = [
            'applicant' => $applicantData,
            'error' => $e->getMessage()
        ];
    }
}
```

This comprehensive documentation covers all the major endpoints and provides practical examples for implementing the TCU API client in your applications.