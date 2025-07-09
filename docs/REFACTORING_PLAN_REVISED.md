# TCU API Client - Revised Step-by-Step Refactoring Plan

## Company Information
- **Company:** MBLogik
- **Author:** Ombeni Aidani <developer@mblogik.com>
- **Date:** 2025-01-09
- **Version:** 1.0.0

## 📋 Overview

This document outlines a **session-based step-by-step approach** to refactor the TCU API Client, implementing **5 endpoints per session** based on the official TCU API specification. Each webservice is documented with its exact usage context as defined in the API documentation.

## 🎯 **Implementation Strategy**

### **Session-Based Approach (5 Endpoints Per Session)**
- **Session 1:** Endpoints 3.1-3.5 (Core applicant operations)
- **Session 2:** Endpoints 3.6-3.10 (Administrative operations)
- **Session 3:** Endpoints 3.11-3.15 (Confirmation and transfers)
- **Session 4:** Endpoints 3.16-3.20 (Verification and enrollment)
- **Session 5:** Endpoints 3.21-3.25 (Graduates and staff)
- **Session 6:** Endpoints 3.26-3.30 (Non-degree and postgraduate)
- **Session 7:** Endpoints 3.31-3.35 (Foreign applicants)

### **Foundation Steps (Before Sessions)**
1. **Step 1:** Update project structure and namespaces
2. **Step 2:** Implement proper authentication system
3. **Step 3:** Create XML request/response handling

---

## 🚀 **Foundation Implementation**

### **Step 1: Project Structure Update** 🏗️
**Estimated Time:** 2-3 hours  
**Priority:** High  
**Status:** Ready to implement

#### **New Directory Structure:**
```
src/
├── Client/
│   ├── TcuApiClient.php           # Main client class
│   └── ClientFactory.php          # Client factory
├── Authentication/
│   ├── UsernameToken.php          # Authentication token
│   └── AuthenticationManager.php  # Token management
├── Resources/
│   ├── ApplicantsResource.php     # Applicant operations
│   ├── AdmissionResource.php      # Admission management
│   ├── DashboardResource.php      # Dashboard operations
│   └── BaseResource.php           # Base resource class
├── Models/
│   ├── Request/                   # Request models
│   ├── Response/                  # Response models
│   └── Data/                      # Data models
├── Xml/
│   ├── RequestBuilder.php         # XML request builder
│   ├── ResponseParser.php         # XML response parser
│   └── XmlValidator.php           # XML validation
├── Exceptions/
│   ├── TcuApiException.php        # Base exception
│   ├── AuthenticationException.php # Auth errors
│   └── ValidationException.php    # Validation errors
└── Utils/
    ├── XmlHelper.php              # XML utilities
    └── ValidationHelper.php       # Validation utilities
```

#### **Implementation Tasks:**
1. Create new directory structure
2. Set up proper namespaces
3. Create base classes for XML handling
4. Update composer.json autoloading
5. Create compatibility layer for existing code

---

### **Step 2: Authentication System** 🔐
**Estimated Time:** 3-4 hours  
**Priority:** High  
**Status:** After Step 1

#### **Components:**
```php
// Authentication/UsernameToken.php
class UsernameToken {
    public function __construct(
        public string $username,
        public string $sessionToken
    ) {}
    
    public function toXmlArray(): array;
    public function isValid(): bool;
}

// Authentication/AuthenticationManager.php
class AuthenticationManager {
    public function createToken(string $username, string $sessionToken): UsernameToken;
    public function validateToken(UsernameToken $token): bool;
    public function refreshToken(): UsernameToken;
}
```

---

### **Step 3: XML Request/Response System** 🔄
**Estimated Time:** 4-5 hours  
**Priority:** High  
**Status:** After Step 2

#### **Components:**
```php
// Xml/RequestBuilder.php
class RequestBuilder {
    public function buildRequest(UsernameToken $token, array $parameters): string;
    public function validateRequest(string $xml): bool;
}

// Xml/ResponseParser.php
class ResponseParser {
    public function parseResponse(string $xml): array;
    public function extractStatusCode(string $xml): int;
    public function extractStatusDescription(string $xml): string;
}
```

---

## 📊 **Session-Based Endpoint Implementation**

### **SESSION 1: Core Applicant Operations (3.1-3.5)**
**Estimated Time:** 6-8 hours  
**Priority:** High  
**Status:** After Foundation Steps

#### **3.1 Check Applicant Status**
- **Web Service Method:** Check Applicant Status
- **URL:** `https://api.tcu.go.tz/applicants/checkStatus`
- **Usage Context:** **Undergraduate admission**
- **Description:** Check the applicant status in TCU database to determine whether the applicant is a prior admitted student, discontinued, graduated or already admitted applicant (subsequent rounds after first round)
- **Special Requirements:** 
  - Exclude current year form six and diploma graduates for first round of application
  - However, subsequent rounds should use the end point to exclude applicants already admitted
- **Implementation:** `ApplicantsResource::checkStatus()`

#### **3.2 Add Applicant**
- **Web Service Method:** Add Applicant
- **URL:** `https://api.tcu.go.tz/applicants/add`
- **Usage Context:** **Undergraduate admission**
- **Description:** Add a new applicant by inserting institution id, gender, form 4 index number, form 6 index number (or AVN for diploma holders) and category, other form four index number(s) and other form six index number(s)
- **Special Requirements:** This endpoint is for applicants applying for bachelor degree programmes
- **Implementation:** `ApplicantsResource::add()`

#### **3.3 Submit Applicant Programme Choices**
- **Web Service Method:** Submit Applicant Programme Choices
- **URL:** `https://api.tcu.go.tz/applicants/submitProgramme`
- **Usage Context:** **Undergraduate admission**
- **Description:** After selection for admission has been done, submit the list of applicants with their choices and other contact details to TCU (TCU's template)
- **Special Requirements:** Include Institution Code, gender, form four index number, form six index number, programme choices, mobile number, email, admission status, programme of admission, reason, applicant category, other form four index number(s) and other form six index number(s)
- **Implementation:** `ApplicantsResource::submitProgrammeChoices()`

#### **3.4 Confirm Applicant Selection**
- **Web Service Method:** Confirm Applicant Admission
- **URL:** `https://api.tcu.go.tz/admission/confirm`
- **Usage Context:** **Undergraduate admission**
- **Description:** Each applicant with multiple selections shall use a TCU provided token/code to confirm selection to ONLY one HLI, and the same shall be confirmed with TCU
- **Special Requirements:** Used for applicants with multiple admission offers
- **Implementation:** `AdmissionResource::confirm()`

#### **3.5 Unconfirm Admission**
- **Web Service Method:** Unconfirm Applicant Admission
- **URL:** `https://api.tcu.go.tz/admission/unconfirm`
- **Usage Context:** **Undergraduate admission**
- **Description:** An applicant that had confirmed can reject the admission via the respective HLI online system, and the same shall be passed on to TCU
- **Special Requirements:** Allows applicants to change their minds and confirm elsewhere
- **Implementation:** `AdmissionResource::unconfirm()`

---

### **SESSION 2: Administrative Operations (3.6-3.10)**
**Estimated Time:** 6-8 hours  
**Priority:** High  
**Status:** After Session 1

#### **3.6 Resubmit Applicant Details**
- **Web Service Method:** Resubmit rectified applicants
- **URL:** `https://api.tcu.go.tz/applicants/resubmit`
- **Usage Context:** **Undergraduate admission**
- **Description:** Update TCU on applicant details that might be different from the initial submission
- **Implementation:** `ApplicantsResource::resubmit()`

#### **3.7 Populate Dashboard**
- **Web Service Method:** Populate Dashboard
- **URL:** `https://api.tcu.go.tz/dashboard/populate`
- **Usage Context:** **Undergraduate admission**
- **Description:** Update latest application statistics to the TCU dashboard (update once a day) i.e. programme code, number of males and number of females
- **Implementation:** `DashboardResource::populate()`

#### **3.8 Get Admitted Applicants**
- **Web Service Method:** Get admitted applicants
- **URL:** `https://api.tcu.go.tz/applicants/getAdmitted`
- **Usage Context:** **Undergraduate admission**
- **Description:** Download a list of applicants with their admission status
- **Implementation:** `ApplicantsResource::getAdmitted()`

#### **3.9 Get Programmes with Admitted Candidates**
- **Web Service Method:** Get programmes with Admitted candidates
- **URL:** `https://api.tcu.go.tz/admission/getProgrammes`
- **Usage Context:** **Undergraduate admission**
- **Description:** Download a list of programme codes for programmes with selected applicants
- **Implementation:** `AdmissionResource::getProgrammes()`

#### **3.10 Get applicants' Admission Status**
- **Web Service Method:** Get applicant status
- **URL:** `https://api.tcu.go.tz/applicants/getStatus`
- **Usage Context:** **Undergraduate admission**
- **Description:** Download a list of applicants with their Undergraduate admission status
- **Implementation:** `ApplicantsResource::getStatus()`

---

### **SESSION 3: Confirmation and Transfers (3.11-3.15)**
**Estimated Time:** 6-8 hours  
**Priority:** High  
**Status:** After Session 2

#### **3.11 Get a list of confirmed applicants**
- **Web Service Method:** Get a list of confirmed applicants
- **URL:** `https://api.tcu.go.tz/applicants/getConfirmed`
- **Usage Context:** **Undergraduate admission**
- **Description:** Get a list of confirmed applicants who had multiple admissions and their confirmation status per programme
- **Implementation:** `ApplicantsResource::getConfirmed()`

#### **3.12 Request confirmation code**
- **Web Service Method:** Request a confirmation code
- **URL:** `https://api.tcu.go.tz/dashboard/requestConfirmationCode`
- **Usage Context:** **Undergraduate admission**
- **Description:** An applicant that had multiple admission can request confirmation code via the respective HLI online system, and the same shall be passed on to TCU
- **Implementation:** `DashboardResource::requestConfirmationCode()`

#### **3.13 Cancel/Reject Admission**
- **Web Service Method:** Cancel/ Reject Admission
- **URL:** `https://api.tcu.go.tz/dashboard/reject`
- **Usage Context:** **Undergraduate admission**
- **Description:** An applicant that had been admitted can cancel his admission via the respective HLI online system, and the same shall be passed on to TCU
- **Implementation:** `DashboardResource::reject()`

#### **3.14 Submit Internal Transfers**
- **Web Service Method:** Submit internal transfers
- **URL:** `https://api.tcu.go.tz/admission/submitInternalTransfers`
- **Usage Context:** **Undergraduate admission**
- **Description:** An applicant who has been admitted but wishes to transfer to another programme within the same institution
- **Implementation:** `AdmissionResource::submitInternalTransfers()`

#### **3.15 Submit Inter-Institutional Transfers**
- **Web Service Method:** Submit inter institutional transfers
- **URL:** `https://api.tcu.go.tz/admission/submitInterInstitutionalTransfers`
- **Usage Context:** **Undergraduate admission**
- **Description:** An applicant who has been admitted and wishes to transfer to another institution
- **Implementation:** `AdmissionResource::submitInterInstitutionalTransfers()`

---

### **SESSION 4: Verification and Enrollment (3.16-3.20)**
**Estimated Time:** 6-8 hours  
**Priority:** High  
**Status:** After Session 3

#### **3.16 Get applicants' Verification Status**
- **Web Service Method:** Get Applicant's Verification Status
- **URL:** `https://api.tcu.go.tz/applicants/getApplicantVerificationStatus`
- **Usage Context:** **Undergraduate admission**
- **Description:** Download a list of applicants with their verification status
- **Implementation:** `ApplicantsResource::getApplicantVerificationStatus()`

#### **3.17 Submit enrolled/registered students into undergraduate programmes**
- **Web Service Method:** Submit enrolled/registered students into undergraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/submitEnrolledStudents`
- **Usage Context:** **Undergraduate**
- **Description:** A student registered/enrolled into an undergraduate programme in an academic year
- **Implementation:** `ApplicantsResource::submitEnrolledStudents()`

#### **3.18 Submit graduates**
- **Web Service Method:** Submit Graduates
- **URL:** `https://api.tcu.go.tz/applicants/submitGraduates`
- **Usage Context:** **Both Undergraduate and Postgraduate**
- **Description:** All who have graduated from a particular institution
- **Implementation:** `ApplicantsResource::submitGraduates()`

#### **3.19 Submit Institution Staff**
- **Web Service Method:** Submit Institution Staff
- **URL:** `https://api.tcu.go.tz/applicants/submitInstitutionStaff`
- **Usage Context:** **Used to submit the list of all employees**
- **Description:** Institution staff i.e. both academic and administrative staff
- **Implementation:** `ApplicantsResource::submitInstitutionStaff()`

#### **3.20 Get verification status for internally transferred students**
- **Web Service Method:** Get verification status for internally transferred students
- **URL:** `https://api.tcu.go.tz/applicants/getInternalTransferStatus`
- **Usage Context:** **Undergraduate transfers**
- **Description:** Download applicants who have requested internal transfers and their TCU verification status
- **Implementation:** `ApplicantsResource::getInternalTransferStatus()`

---

### **SESSION 5: Graduates and Staff (3.21-3.25)**
**Estimated Time:** 6-8 hours  
**Priority:** Medium  
**Status:** After Session 4

#### **3.21 Get verification status for inter-institutional transferred students**
- **Web Service Method:** Get verification status for inter-institutional transferred students
- **URL:** `https://api.tcu.go.tz/applicants/getInterInstitutionalTransferStatus`
- **Usage Context:** **Undergraduate transfers**
- **Description:** Download applicants who have requested for inter-institutional transfers and their TCU verification status
- **Implementation:** `ApplicantsResource::getInterInstitutionalTransferStatus()`

#### **3.22 Restore the cancelled admission**
- **Web Service Method:** Restore cancelled admission
- **URL:** `https://api.tcu.go.tz/admission/restoreCancelledAdmission`
- **Usage Context:** **Undergraduate admission**
- **Description:** Restore admission for applicants who previously cancelled their admissions
- **Implementation:** `AdmissionResource::restoreCancelledAdmission()`

#### **3.23 Submit students' dropouts**
- **Web Service Method:** Submit students drop-outs
- **URL:** `https://api.tcu.go.tz/applicants/submitStudentsDropOuts`
- **Usage Context:** **Undergraduate**
- **Description:** Submit students who have dropped out of their programmes
- **Implementation:** `ApplicantsResource::submitStudentsDropOuts()`

#### **3.24 Submit students who postponed studies**
- **Web Service Method:** Submit students who postponed studies
- **URL:** `https://api.tcu.go.tz/applicants/submitPostponedStudents`
- **Usage Context:** **Undergraduate**
- **Description:** Submit students who have postponed their studies
- **Implementation:** `ApplicantsResource::submitPostponedStudents()`

#### **3.25 Submit admitted students into non-degree programmes**
- **Web Service Method:** Submit admitted students into non-degree programmes (foundation, diploma, certificates)
- **URL:** `https://api.tcu.go.tz/applicants/submitAdmittedNonDegree`
- **Usage Context:** **Non-degree programmes**
- **Description:** Submit students admitted into non-degree programmes
- **Implementation:** `ApplicantsResource::submitAdmittedNonDegree()`

---

### **SESSION 6: Non-degree and Postgraduate (3.26-3.30)**
**Estimated Time:** 6-8 hours  
**Priority:** Medium  
**Status:** After Session 5

#### **3.26 Get verification status for non-degree admitted students**
- **Web Service Method:** Get verification status for non-degree admitted students
- **URL:** `https://api.tcu.go.tz/applicants/getNonDegreeAdmittedStatus`
- **Usage Context:** **Non-degree programmes**
- **Description:** Get verification status for students admitted into non-degree programmes
- **Implementation:** `ApplicantsResource::getNonDegreeAdmittedStatus()`

#### **3.27 Submit the applicants applied for Postgraduate programmes**
- **Web Service Method:** Submit the applicants applied for Postgraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/submitPostgraduateApplicants`
- **Usage Context:** **Postgraduate admission**
- **Description:** Submit applicants who have applied for postgraduate programmes
- **Implementation:** `ApplicantsResource::submitPostgraduateApplicants()`

#### **3.28 Submit admitted students into Postgraduate programmes**
- **Web Service Method:** Submit admitted students into Postgraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/submitAdmittedPostgraduate`
- **Usage Context:** **Postgraduate admission**
- **Description:** Submit students admitted into postgraduate programmes
- **Implementation:** `ApplicantsResource::submitAdmittedPostgraduate()`

#### **3.29 Get verification status for admitted students into the Postgraduate programmes**
- **Web Service Method:** Get verification status for admitted students into the Postgraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/getPostgraduateAdmittedStatus`
- **Usage Context:** **Postgraduate admission**
- **Description:** Get verification status for students admitted into postgraduate programmes
- **Implementation:** `ApplicantsResource::getPostgraduateAdmittedStatus()`

#### **3.30 Submit transferred students from one Postgraduate programme to another**
- **Web Service Method:** Submit transferred students from one Postgraduate programme to another
- **URL:** `https://api.tcu.go.tz/applicants/submitPostgraduateTransfers`
- **Usage Context:** **Postgraduate transfers**
- **Description:** Submit students who have transferred from one postgraduate programme to another
- **Implementation:** `ApplicantsResource::submitPostgraduateTransfers()`

---

### **SESSION 7: Foreign Applicants (3.31-3.35)**
**Estimated Time:** 6-8 hours  
**Priority:** Medium  
**Status:** After Session 6

#### **3.31 Get verification status for transferred students into Postgraduate programmes**
- **Web Service Method:** Get verification status for transferred students into Postgraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/getPostgraduateTransferStatus`
- **Usage Context:** **Postgraduate transfers**
- **Description:** Get verification status for students who have transferred into postgraduate programmes
- **Implementation:** `ApplicantsResource::getPostgraduateTransferStatus()`

#### **3.32 Submit enrolled/registered students into postgraduate programmes**
- **Web Service Method:** Submit enrolled/registered students into postgraduate programmes
- **URL:** `https://api.tcu.go.tz/applicants/submitEnrolledPostgraduateStudents`
- **Usage Context:** **Postgraduate**
- **Description:** Submit students enrolled/registered into postgraduate programmes
- **Implementation:** `ApplicantsResource::submitEnrolledPostgraduateStudents()`

#### **3.33 Submit foreign applicants who applied for admission to bachelor's degree programmes**
- **Web Service Method:** Submit foreign applicants who applied for admission to bachelor's degree programmes
- **URL:** `https://api.tcu.go.tz/applicants/submitForeignApplicants`
- **Usage Context:** **Undergraduate admission**
- **Description:** Submit foreign applicants who have applied for bachelor's degree programmes in Tanzania
- **Implementation:** `ApplicantsResource::submitForeignApplicants()`

#### **3.34 Submit admitted foreigners into bachelor's degree programmes under Direct Entry**
- **Web Service Method:** Submit admitted foreigners into bachelor's degree programmes under Direct Entry
- **URL:** `https://api.tcu.go.tz/applicants/submitForeignAdmittedDirect`
- **Usage Context:** **Undergraduate admission**
- **Description:** Submit foreign students admitted into bachelor's degree programmes under Direct Entry
- **Implementation:** `ApplicantsResource::submitForeignAdmittedDirect()`

#### **3.35 Submit admitted foreigners into bachelor's degree programmes under Equivalent Entry**
- **Web Service Method:** Submit admitted foreigners into bachelor's degree programmes under Equivalent Entry
- **URL:** `https://api.tcu.go.tz/applicants/submitForeignAdmittedEquivalent`
- **Usage Context:** **Undergraduate admission**
- **Description:** Submit foreign students admitted into bachelor's degree programmes under Equivalent Entry
- **Implementation:** `ApplicantsResource::submitForeignAdmittedEquivalent()`

---

## 📋 **Implementation Timeline**

### **Phase 1: Foundation (Weeks 1-2)**
- Step 1: Project Structure Update
- Step 2: Authentication System  
- Step 3: XML Request/Response System

### **Phase 2: Core Operations (Weeks 3-4)**
- Session 1: Core Applicant Operations (3.1-3.5)
- Session 2: Administrative Operations (3.6-3.10)

### **Phase 3: Advanced Features (Weeks 5-6)**
- Session 3: Confirmation and Transfers (3.11-3.15)
- Session 4: Verification and Enrollment (3.16-3.20)

### **Phase 4: Specialized Operations (Weeks 7-8)**
- Session 5: Graduates and Staff (3.21-3.25)
- Session 6: Non-degree and Postgraduate (3.26-3.30)
- Session 7: Foreign Applicants (3.31-3.35)

### **Phase 5: Testing and Documentation (Week 9)**
- Comprehensive testing
- Documentation updates
- Migration guides

---

## 🎯 **Next Steps**

**Ready to start with the Foundation Steps?**

1. **Step 1: Project Structure Update** - Create new directory structure and namespaces
2. **Step 2: Authentication System** - Implement UsernameToken authentication
3. **Step 3: XML Processing** - Build XML request/response handling

Once the foundation is complete, we'll proceed with **Session 1** (endpoints 3.1-3.5) which covers the core applicant operations.

**Which step would you like to begin with?**