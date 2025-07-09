# TCU API Client - Endpoints Implementation Progress

## ✅ Completed Endpoints

### 3.1 - Check Applicant Status
- **URL**: `https://api.tcu.go.tz/applicants/checkStatus`
- **Method**: `checkStatus($f4indexno)`
- **Support**: ✅ Single F4 index number, ✅ Multiple F4 index numbers
- **Validation**: ✅ F4 index format (S1001/0012/2018)
- **XML Generation**: ✅ Proper UsernameToken structure

**Usage Example:**
```php
// Single F4 index
$response = $client->applicants()->checkStatus('S1001/0012/2018');

// Multiple F4 indexes
$response = $client->applicants()->checkStatus(['S1001/0012/2018', 'S1001/0015/2016']);
```

### 3.2 - Add Applicant
- **URL**: `https://api.tcu.go.tz/applicants/add`
- **Method**: `add($applicantData)`
- **Support**: ✅ Single applicant, ✅ Multiple applicants
- **Validation**: ✅ F4/F6 format, ✅ Gender (M/F), ✅ Category (A/B/C), ✅ Other index numbers
- **XML Generation**: ✅ Multiple RequestParameters blocks

**Usage Example:**
```php
// Single applicant
$applicant = [
    'f4indexno' => 'S1001/0012/2018',
    'f6indexno' => 'S1001/0562/2018',
    'Gender' => 'M',
    'Category' => 'A',
    'Otherf4indexno' => 'P1001/0012/2016, P1020/0289/2015',
    'Otherf6indexno' => 'P1001/0562/2016, P0234/0002/2014'
];
$response = $client->applicants()->add($applicant);

// Multiple applicants
$applicants = [
    [ /* applicant 1 data */ ],
    [ /* applicant 2 data */ ]
];
$response = $client->applicants()->add($applicants);
```

### 3.3 - Submit Programme Choices
- **URL**: `https://api.tcu.go.tz/applicants/submitProgramme`
- **Method**: `submitProgrammeChoices($applicantData)`
- **Support**: ✅ Single applicant, ✅ Multiple applicants
- **Validation**: ✅ All required fields, ✅ Programme codes (UD023), ✅ Mobile numbers, ✅ Email, ✅ Date formats, ✅ National ID format
- **XML Generation**: ✅ Single RequestParameters block with all fields

**Usage Example:**
```php
$applicant = [
    'f4indexno' => 'S1001/0012/2018',
    'f6indexno' => 'S1001/0562/2018',
    'Gender' => 'M',
    'SelectedProgrammes' => 'UD023, UD038, UD022',
    'MobileNumber' => '0766345678',
    'OtherMobileNumber' => '0766345678',
    'EmailAddress' => 'steve2014@hotmail.com',
    'Category' => 'A',
    'AdmissionStatus' => 'provisional admission',
    'ProgrammeAdmitted' => 'UD038',
    'Reason' => 'eligible',
    'Nationality' => 'Tanzanian',
    'Impairment' => 'None',
    'DateOfBirth' => '1980-12-09',
    'NationalIdNumber' => '19620228-00001-00001-19',
    'Otherf4indexno' => 'S0001/0001/2009, S0001/0001/2010',
    'Otherf6indexno' => 'S0001/0501/2009, S0001/0501/2010'
];
$response = $client->applicants()->submitProgrammeChoices($applicant);
```

### 3.4 - Confirm Applicant Selection
- **URL**: `https://api.tcu.go.tz/admission/confirm`
- **Method**: `confirm($f4indexno, $confirmationCode)`
- **Resource**: `AdmissionResource` (not ApplicantResource)
- **Support**: ✅ Single confirmation request
- **Validation**: ✅ F4 index format, ✅ Confirmation code format (A5267Y pattern)
- **XML Generation**: ✅ Single RequestParameters block

**Usage Example:**
```php
// Confirm admission with TCU provided token
$response = $client->admissions()->confirm('S1001/0012/2018', 'A5267Y');
```

## 🔧 Technical Implementation

### XML Request Format
All requests use the standard TCU API XML format:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Request>
    <UsernameToken>
        <Username>DM</Username>
        <SessionToken>OTcyMURGMTY5QTRENU3MUJ</SessionToken>
    </UsernameToken>
    <RequestParameters>
        <!-- endpoint-specific parameters -->
    </RequestParameters>
</Request>
```

### Authentication
- Username and SessionToken are automatically included in all requests
- Configured via `Configuration` class
- Uses XML-based authentication (not Bearer tokens)

### Validation Patterns
- **F4 Index**: `^[A-Z][0-9]{4}\/[0-9]{4}\/[0-9]{4}$` (e.g., S1001/0012/2018)
- **F6 Index**: `^[A-Z][0-9]{4}\/[0-9]{4}\/[0-9]{4}$` (same format as F4)
- **Gender**: M or F (case-sensitive)
- **Category**: A, B, or C (case-sensitive)
- **Programme Code**: `^[A-Z]{2}[0-9]{3}$` (e.g., UD023)
- **Mobile Number**: `^[0-9]{10}$` (e.g., 0766345678)
- **Date of Birth**: `^[0-9]{4}-[0-9]{2}-[0-9]{2}$` (e.g., 1980-12-09)
- **National ID**: `^[0-9]{8}-[0-9]{5}-[0-9]{5}-[0-9]{2}$` (e.g., 19620228-00001-00001-19)
- **Confirmation Code**: `^[A-Z][0-9]{4}[A-Z]$` (e.g., A5267Y)
- **Email**: PHP `filter_var()` validation

## 📋 Remaining Endpoints (3.5 - 3.35)

The following endpoints still need to be implemented based on the actual TCU API documentation:

- 3.5 - ... (need API documentation for remaining endpoints)
- ...
- 3.35 - ... (final endpoint)

## 🚨 Issues Fixed

1. **XML Format**: Changed from JSON to proper XML request/response handling
2. **Authentication**: Implemented proper UsernameToken structure instead of Bearer tokens  
3. **Multiple Records**: Added support for multiple RequestParameters blocks
4. **Validation**: Updated F4/F6 patterns to match actual format with slashes
5. **HTTP Client**: Fixed Httpful configuration for XML content types

## 📝 Next Steps

1. **Get complete API documentation** for endpoints 3.3-3.35
2. **Review existing resource classes** to remove any non-documented methods
3. **Implement remaining endpoints** following the established XML pattern
4. **Update unit tests** to match new XML-based implementation
5. **Create comprehensive documentation** for all endpoints