# TCU API Client - Step-by-Step Refactoring Plan

## Company Information
- **Company:** MBLogik
- **Author:** Ombeni Aidani <developer@mblogik.com>
- **Date:** 2025-01-09
- **Version:** 1.0.0

## 📋 Overview

This document outlines a step-by-step approach to refactor the TCU API Client to properly align with the official TCU API specification. Each step is designed to be implemented incrementally without breaking existing functionality.

## 🎯 **Current State vs Target State**

### **Current Implementation**
- ✅ Basic PHP client structure
- ✅ HTTP-based requests (using Httpful)
- ✅ JSON request/response handling
- ✅ Basic resource pattern
- ✅ Database logging
- ✅ Basic validation

### **Target Implementation (Per TCU API Spec)**
- 🎯 XML-based requests/responses
- 🎯 Username/SessionToken authentication
- 🎯 Proper resource organization (4 main resources)
- 🎯 35 complete endpoints
- 🎯 Comprehensive data models
- 🎯 Batch processing support
- 🎯 SOAP/WSDL compliance

## 📝 **Step-by-Step Implementation Plan**

### **Step 1: Project Structure Update** 🏗️
**Estimated Time:** 2-3 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Update namespace structure** to match API specification
2. **Reorganize directory structure** for better API alignment
3. **Create new base classes** for XML handling
4. **Update composer.json** with proper metadata

#### **Changes:**
```
src/
├── Authentication/           # NEW - Authentication management
├── Client/                   # NEW - Main client logic
├── Resources/               # UPDATED - 4 main resources
├── Models/                  # UPDATED - TCU-specific models
├── Xml/                     # NEW - XML processing
├── Exceptions/              # UPDATED - TCU-specific exceptions
└── Utils/                   # NEW - Helper utilities
```

#### **Deliverables:**
- Updated directory structure
- New namespace organization
- Updated autoloading configuration
- Compatibility layer for existing code

---

### **Step 2: Authentication System** 🔐
**Estimated Time:** 3-4 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Create UsernameToken authentication** system
2. **Implement session management** for TCU tokens
3. **Update configuration** to handle TCU credentials
4. **Create authentication middleware**

#### **New Classes:**
```php
// Authentication/UsernameToken.php
class UsernameToken {
    public string $username;
    public string $sessionToken;
}

// Authentication/AuthenticationManager.php
class AuthenticationManager {
    public function authenticate(): UsernameToken;
    public function refreshToken(): void;
    public function validateToken(): bool;
}
```

#### **Deliverables:**
- Complete authentication system
- Token management
- Configuration updates
- Backward compatibility maintained

---

### **Step 3: XML Request/Response System** 🔄
**Estimated Time:** 4-5 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Create XML request builder** for TCU API format
2. **Implement XML response parser** 
3. **Add XML validation** and error handling
4. **Create XML templates** for common requests

#### **New Classes:**
```php
// Xml/RequestBuilder.php
class RequestBuilder {
    public function buildRequest(array $data): string;
    public function addUsernameToken(UsernameToken $token): self;
    public function addRequestParameters(array $params): self;
}

// Xml/ResponseParser.php
class ResponseParser {
    public function parseResponse(string $xml): array;
    public function extractStatusCode(string $xml): int;
    public function extractData(string $xml): array;
}
```

#### **Templates:**
- Basic request template
- Batch request template
- Authentication template
- Error response handling

#### **Deliverables:**
- Complete XML processing system
- Request/response templates
- Validation and error handling
- Unit tests for XML processing

---

### **Step 4: Resource Class Refactoring** 🎯
**Estimated Time:** 5-6 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Reorganize into 4 main resources** per API spec
2. **Implement all 35 endpoints** with proper structure
3. **Add batch processing support** where applicable
4. **Update method signatures** to match API requirements

#### **Resource Structure:**
```php
// Resources/ApplicantsResource.php (25 endpoints)
class ApplicantsResource {
    // Core operations
    public function checkStatus($indexNumbers): CheckStatusResponse;
    public function add($applicants): AddApplicantResponse;
    public function submitProgrammes($programmes): SubmitProgrammeResponse;
    public function resubmit($applicants): ResubmitResponse;
    
    // Student lifecycle
    public function submitEnrolledStudents($students): EnrollmentResponse;
    public function submitGraduates($graduates): GraduateResponse;
    public function submitDropouts($dropouts): DropoutResponse;
    
    // Specialized operations
    public function submitForeignApplicants($foreigners): ForeignResponse;
    public function submitPostgraduateApplicants($postgrads): PostgradResponse;
    // ... and more
}

// Resources/AdmissionResource.php (7 endpoints)
class AdmissionResource {
    public function confirm($confirmations): ConfirmResponse;
    public function unconfirm($unconfirmations): UnconfirmResponse;
    public function getProgrammes($criteria): ProgrammeResponse;
    public function getAdmitted($criteria): AdmittedResponse;
    // ... and more
}

// Resources/DashboardResource.php (3 endpoints)
class DashboardResource {
    public function populate($data): DashboardResponse;
    public function reject($rejections): RejectResponse;
    public function requestConfirmationCode($requests): ConfirmationCodeResponse;
}
```

#### **Deliverables:**
- 4 complete resource classes
- All 35 endpoints implemented
- Batch processing support
- Proper error handling

---

### **Step 5: Data Models Update** 📊
**Estimated Time:** 6-8 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Create TCU-specific data models** based on API specification
2. **Implement validation rules** per TCU requirements
3. **Add data transformation methods** for XML conversion
4. **Create model factories** for common use cases

#### **Core Models:**
```php
// Models/Applicant.php - Enhanced for TCU
class Applicant {
    public string $f4indexno;        // Form 4 index (required)
    public ?string $f6indexno;       // Form 6 index (optional)
    public string $gender;           // M/F (required)
    public ?string $category;        // A/D/F/B (optional)
    public ?string $nationality;     // Nationality
    public ?string $otherf4indexno;  // Other Form 4 numbers
    public ?string $otherf6indexno;  // Other Form 6 numbers
    
    // Validation methods
    public function validateF4IndexNumber(): bool;
    public function validateGender(): bool;
    public function validateCategory(): bool;
    
    // XML conversion
    public function toXmlArray(): array;
    public static function fromXmlArray(array $data): self;
}

// Models/EnrolledStudent.php - New for student enrollment
class EnrolledStudent {
    public string $fname;
    public ?string $mname;
    public string $surname;
    public string $f4indexno;
    public string $gender;
    public string $nationality;
    public string $dateOfBirth;
    public string $programmeCategory;
    public string $fieldOfSpecialization;
    public string $yearOfStudy;
    public string $studyMode;
    public string $registrationNumber;
    public string $programmeCode;
    public string $academicYear;
    // ... plus validation and XML methods
}

// Models/Graduate.php - New for graduate submission
// Models/InstitutionStaff.php - New for staff submission
// Models/TransferStudent.php - New for transfers
```

#### **Response Models:**
```php
// Models/Response/TcuApiResponse.php
class TcuApiResponse {
    public int $statusCode;
    public string $statusDescription;
    public ?array $data;
    
    public function isSuccess(): bool;
    public function getErrorMessage(): ?string;
    public function getData(): array;
}
```

#### **Deliverables:**
- Complete set of TCU data models
- Validation for all models
- XML conversion methods
- Model factories and utilities

---

### **Step 6: Endpoint Implementation** 🔗
**Estimated Time:** 8-10 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Implement all 35 endpoints** with proper URL structure
2. **Add endpoint-specific validation** 
3. **Implement batch processing** where supported
4. **Create endpoint documentation** with examples

#### **Endpoint Categories:**
- **Applicants** (25 endpoints) - Core applicant operations
- **Admission** (7 endpoints) - Admission management
- **Dashboard** (3 endpoints) - Administrative operations

#### **Implementation Pattern:**
```php
public function checkStatus($indexNumbers): CheckStatusResponse 
{
    // 1. Validate input
    $this->validateIndexNumbers($indexNumbers);
    
    // 2. Build XML request
    $request = $this->xmlBuilder
        ->addUsernameToken($this->auth->getToken())
        ->addRequestParameters(['f4indexno' => $indexNumbers])
        ->buildRequest();
    
    // 3. Send request
    $response = $this->httpClient->post('/applicants/checkStatus', $request);
    
    // 4. Parse XML response
    $parsed = $this->xmlParser->parseResponse($response);
    
    // 5. Return typed response
    return new CheckStatusResponse($parsed);
}
```

#### **Deliverables:**
- All 35 endpoints implemented
- Proper URL structure
- Input validation for each endpoint
- Comprehensive error handling

---

### **Step 7: Response Code System** 📋
**Estimated Time:** 2-3 hours  
**Priority:** High  
**Status:** Pending

#### **Tasks:**
1. **Update response codes** to match TCU specification
2. **Implement status code mapping** (200-217+)
3. **Create response code utilities** 
4. **Add proper error messages**

#### **Enhanced Response Codes:**
```php
// Enums/TcuResponseCode.php
class TcuResponseCode {
    public const SUCCESSFUL = 200;
    public const PRIOR_ADMISSION = 201;
    public const CLEAR = 202;
    public const ALREADY_ADMITTED = 203;
    public const SESSION_TOKEN_DOES_NOT_EXIST = 204;
    public const MALFORMED_XML_REQUEST = 205;
    // ... all TCU codes
    
    public static function getMessage(int $code): string;
    public static function isSuccess(int $code): bool;
    public static function requiresAction(int $code): bool;
}
```

#### **Deliverables:**
- Complete response code system
- Proper error handling
- Status code utilities
- Documentation for each code

---

### **Step 8: Testing & Documentation** ✅
**Estimated Time:** 4-6 hours  
**Priority:** Medium  
**Status:** Pending

#### **Tasks:**
1. **Create comprehensive unit tests** for all components
2. **Add integration tests** for API endpoints
3. **Update documentation** with new examples
4. **Create migration guide** from old to new structure

#### **Test Coverage:**
- XML processing (request/response)
- Authentication system
- All resource classes
- Data model validation
- Error handling scenarios

#### **Documentation Updates:**
- API endpoint reference
- Usage examples for all 35 endpoints
- Migration guide
- Troubleshooting guide

---

## 🚀 **Implementation Strategy**

### **Phase 1: Foundation (Steps 1-3)**
**Goal:** Establish the core infrastructure
- Project structure
- Authentication
- XML processing

### **Phase 2: Core Functionality (Steps 4-5)**
**Goal:** Implement the main API features
- Resource classes
- Data models

### **Phase 3: Completion (Steps 6-8)**
**Goal:** Complete implementation and testing
- All endpoints
- Response handling
- Testing & documentation

## ⚠️ **Compatibility Strategy**

### **Backward Compatibility**
- Keep existing classes during transition
- Create adapter layer for old code
- Gradual migration approach
- Clear deprecation notices

### **Migration Path**
1. **Parallel Implementation** - New classes alongside old
2. **Adapter Layer** - Bridge between old and new
3. **Gradual Migration** - Endpoint by endpoint
4. **Full Transition** - Remove old classes

## 📞 **Next Steps**

**Ready to start with Step 1: Project Structure Update?**

This will involve:
1. Creating new directory structure
2. Setting up proper namespaces
3. Creating base classes for XML handling
4. Maintaining backward compatibility

Let me know when you're ready to proceed with Step 1!