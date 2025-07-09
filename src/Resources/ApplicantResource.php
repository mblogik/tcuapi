<?php

/**
 * TCU API Client - Applicant Resource
 * 
 * Resource class for handling applicant-related API operations in the TCU system.
 * Provides methods for checking status, adding applicants, submitting programmes,
 * and managing various applicant lifecycle operations including enrollment and graduation.
 * 
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Resource class for comprehensive applicant management operations
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Models\Data\Applicant;
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;
use MBLogik\TCUAPIClient\Models\Request\SubmitProgrammeRequest;
use MBLogik\TCUAPIClient\Models\Response\CheckStatusResponse;
use MBLogik\TCUAPIClient\Models\Response\AddApplicantResponse;
use MBLogik\TCUAPIClient\Authentication\UsernameToken;
use MBLogik\TCUAPIClient\Authentication\AuthenticationManager;
use MBLogik\TCUAPIClient\Xml\RequestBuilder;
use MBLogik\TCUAPIClient\Xml\ResponseParser;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;

class ApplicantResource extends BaseResource
{
    /**
     * Check applicant status in TCU database (3.1)
     * 
     * Usage Context: Undergraduate admission
     * Description: Check the applicant status in TCU database to determine whether 
     * the applicant is a prior admitted student, discontinued, graduated or already 
     * admitted applicant (subsequent rounds after first round)
     * 
     * @param string|array $f4indexno Single F4 index number or array of multiple F4 index numbers
     * @return array
     */
    public function checkStatus($f4indexno): array
    {
        // Handle both single and multiple F4 index numbers
        if (is_string($f4indexno)) {
            $f4indexNumbers = [$f4indexno];
        } elseif (is_array($f4indexno)) {
            $f4indexNumbers = $f4indexno;
        } else {
            throw new ValidationException("F4 index number must be string or array");
        }
        
        // Validate all F4 index numbers
        $errors = [];
        foreach ($f4indexNumbers as $index => $f4index) {
            if (!ValidationHelper::validateF4IndexNo($f4index)) {
                $errors[] = "Invalid F4 Index Number format at position $index: $f4index";
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        // Prepare request parameters - handle multiple f4indexno
        $requestParameters = [];
        if (count($f4indexNumbers) === 1) {
            $requestParameters['f4indexno'] = $f4indexNumbers[0];
        } else {
            $requestParameters['f4indexno'] = $f4indexNumbers;
        }
        
        return $this->client->makeRequest('/applicants/checkStatus', $requestParameters);
    }
    
    /**
     * Add a new applicant to the system (3.2)
     * 
     * Usage Context: Undergraduate admission
     * Description: Add a new applicant by inserting institution id, gender, 
     * form 4 index number, form 6 index number (or AVN for diploma holders) 
     * and category, other form four index number(s) and other form six index number(s)
     * 
     * @param array $applicantData Single applicant data or array of multiple applicants
     * @return array
     */
    public function add($applicantData): array
    {
        // Handle both single applicant and multiple applicants
        if (isset($applicantData['f4indexno'])) {
            // Single applicant data
            $applicants = [$applicantData];
        } else {
            // Multiple applicants data
            $applicants = $applicantData;
        }
        
        // Validate all applicants
        $errors = [];
        foreach ($applicants as $index => $applicant) {
            $applicantErrors = $this->validateApplicantData($applicant, $index);
            $errors = array_merge($errors, $applicantErrors);
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Applicant validation failed", $errors);
        }
        
        // Prepare request parameters for XML generation
        $requestParameters = [];
        foreach ($applicants as $applicant) {
            $requestParameters[] = $this->prepareApplicantRequestData($applicant);
        }
        
        return $this->client->makeRequest('/applicants/add', $requestParameters);
    }
    
    /**
     * Validate individual applicant data
     */
    private function validateApplicantData(array $applicant, int $index = 0): array
    {
        $errors = [];
        
        // Required fields
        $requiredFields = ['f4indexno', 'f6indexno', 'Gender', 'Category'];
        
        foreach ($requiredFields as $field) {
            if (empty($applicant[$field])) {
                $errors[] = "Missing required field '$field' for applicant at index $index";
            }
        }
        
        // Validate F4 index number
        if (!empty($applicant['f4indexno']) && !ValidationHelper::validateF4IndexNo($applicant['f4indexno'])) {
            $errors[] = "Invalid F4 index number format for applicant at index $index: {$applicant['f4indexno']}";
        }
        
        // Validate F6 index number
        if (!empty($applicant['f6indexno']) && !ValidationHelper::validateF6IndexNo($applicant['f6indexno'])) {
            $errors[] = "Invalid F6 index number format for applicant at index $index: {$applicant['f6indexno']}";
        }
        
        // Validate Gender
        if (!empty($applicant['Gender']) && !in_array($applicant['Gender'], ['M', 'F'])) {
            $errors[] = "Invalid Gender for applicant at index $index. Must be 'M' or 'F'";
        }
        
        // Validate Category
        if (!empty($applicant['Category']) && !in_array($applicant['Category'], ['A', 'B', 'C'])) {
            $errors[] = "Invalid Category for applicant at index $index. Must be 'A', 'B', or 'C'";
        }
        
        // Validate Other F4 index numbers if provided
        if (!empty($applicant['Otherf4indexno'])) {
            $otherF4Numbers = array_map('trim', explode(',', $applicant['Otherf4indexno']));
            foreach ($otherF4Numbers as $otherF4) {
                if (!ValidationHelper::validateF4IndexNo($otherF4)) {
                    $errors[] = "Invalid Other F4 index number format for applicant at index $index: $otherF4";
                }
            }
        }
        
        // Validate Other F6 index numbers if provided
        if (!empty($applicant['Otherf6indexno'])) {
            $otherF6Numbers = array_map('trim', explode(',', $applicant['Otherf6indexno']));
            foreach ($otherF6Numbers as $otherF6) {
                if (!ValidationHelper::validateF6IndexNo($otherF6)) {
                    $errors[] = "Invalid Other F6 index number format for applicant at index $index: $otherF6";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Prepare applicant data for XML request
     */
    private function prepareApplicantRequestData(array $applicant): array
    {
        $requestData = [
            'f4indexno' => $applicant['f4indexno'],
            'f6indexno' => $applicant['f6indexno'],
            'Gender' => $applicant['Gender'],
            'Category' => $applicant['Category']
        ];
        
        // Add optional fields if provided
        if (!empty($applicant['Otherf4indexno'])) {
            $requestData['Otherf4indexno'] = $applicant['Otherf4indexno'];
        }
        
        if (!empty($applicant['Otherf6indexno'])) {
            $requestData['Otherf6indexno'] = $applicant['Otherf6indexno'];
        }
        
        return $requestData;
    }
    
    /**
     * Submit applicant programme choices (3.3)
     * 
     * Usage Context: Undergraduate admission
     * Description: After selection for admission has been done, submit the list 
     * of applicants with their choices and other contact details to TCU
     * 
     * @param array $applicantData Single applicant data or array of multiple applicants
     * @return array
     */
    public function submitProgrammeChoices($applicantData): array
    {
        // Handle both single applicant and multiple applicants
        if (isset($applicantData['f4indexno'])) {
            // Single applicant data
            $applicants = [$applicantData];
        } else {
            // Multiple applicants data
            $applicants = $applicantData;
        }
        
        // Validate all applicants
        $errors = [];
        foreach ($applicants as $index => $applicant) {
            $applicantErrors = $this->validateProgrammeSubmissionData($applicant, $index);
            $errors = array_merge($errors, $applicantErrors);
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Programme submission validation failed", $errors);
        }
        
        // Prepare request parameters for XML generation
        $requestParameters = [];
        foreach ($applicants as $applicant) {
            $requestParameters[] = $this->prepareProgrammeSubmissionData($applicant);
        }
        
        return $this->client->makeRequest('/applicants/submitProgramme', $requestParameters);
    }
    
    /**
     * Resubmit applicant programme choices (3.6)
     * 
     * Usage Context: Undergraduate admission
     * Description: Resubmit applicant programme choices and contact details 
     * when updates or corrections are needed after initial submission
     * 
     * @param array $applicantData Single applicant data or array of multiple applicants
     * @return array
     */
    public function resubmit($applicantData): array
    {
        // Handle both single applicant and multiple applicants
        if (isset($applicantData['f4indexno'])) {
            // Single applicant data
            $applicants = [$applicantData];
        } else {
            // Multiple applicants data
            $applicants = $applicantData;
        }
        
        // Validate all applicants using the same validation as submitProgrammeChoices
        $errors = [];
        foreach ($applicants as $index => $applicant) {
            $applicantErrors = $this->validateProgrammeSubmissionData($applicant, $index);
            $errors = array_merge($errors, $applicantErrors);
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Programme resubmission validation failed", $errors);
        }
        
        // Prepare request parameters for XML generation
        $requestParameters = [];
        foreach ($applicants as $applicant) {
            $requestParameters[] = $this->prepareProgrammeSubmissionData($applicant);
        }
        
        return $this->client->makeRequest('/applicants/resubmit', $requestParameters);
    }
    
    /**
     * Validate programme submission data
     */
    private function validateProgrammeSubmissionData(array $applicant, int $index = 0): array
    {
        $errors = [];
        
        // Required fields
        $requiredFields = ['f4indexno', 'f6indexno', 'Gender', 'SelectedProgrammes', 'Category'];
        
        foreach ($requiredFields as $field) {
            if (empty($applicant[$field])) {
                $errors[] = "Missing required field '$field' for applicant at index $index";
            }
        }
        
        // Validate F4 index number
        if (!empty($applicant['f4indexno']) && !ValidationHelper::validateF4IndexNo($applicant['f4indexno'])) {
            $errors[] = "Invalid F4 index number format for applicant at index $index: {$applicant['f4indexno']}";
        }
        
        // Validate F6 index number
        if (!empty($applicant['f6indexno']) && !ValidationHelper::validateF6IndexNo($applicant['f6indexno'])) {
            $errors[] = "Invalid F6 index number format for applicant at index $index: {$applicant['f6indexno']}";
        }
        
        // Validate Gender
        if (!empty($applicant['Gender']) && !in_array($applicant['Gender'], ['M', 'F'])) {
            $errors[] = "Invalid Gender for applicant at index $index. Must be 'M' or 'F'";
        }
        
        // Validate Category
        if (!empty($applicant['Category']) && !in_array($applicant['Category'], ['A', 'B', 'C'])) {
            $errors[] = "Invalid Category for applicant at index $index. Must be 'A', 'B', or 'C'";
        }
        
        // Validate SelectedProgrammes (comma-separated programme codes)
        if (!empty($applicant['SelectedProgrammes'])) {
            $programmes = array_map('trim', explode(',', $applicant['SelectedProgrammes']));
            foreach ($programmes as $programme) {
                if (!preg_match('/^[A-Z]{2}[0-9]{3}$/', $programme)) {
                    $errors[] = "Invalid programme code format for applicant at index $index: $programme (expected format: UD023)";
                }
            }
        }
        
        // Validate MobileNumber if provided
        if (!empty($applicant['MobileNumber']) && !preg_match('/^[0-9]{10}$/', $applicant['MobileNumber'])) {
            $errors[] = "Invalid mobile number format for applicant at index $index (expected 10 digits)";
        }
        
        // Validate OtherMobileNumber if provided
        if (!empty($applicant['OtherMobileNumber']) && !preg_match('/^[0-9]{10}$/', $applicant['OtherMobileNumber'])) {
            $errors[] = "Invalid other mobile number format for applicant at index $index (expected 10 digits)";
        }
        
        // Validate EmailAddress if provided
        if (!empty($applicant['EmailAddress']) && !filter_var($applicant['EmailAddress'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address format for applicant at index $index";
        }
        
        // Validate DateOfBirth if provided
        if (!empty($applicant['DateOfBirth']) && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $applicant['DateOfBirth'])) {
            $errors[] = "Invalid date of birth format for applicant at index $index (expected YYYY-MM-DD)";
        }
        
        // Validate NationalIdNumber if provided
        if (!empty($applicant['NationalIdNumber']) && !preg_match('/^[0-9]{8}-[0-9]{5}-[0-9]{5}-[0-9]{2}$/', $applicant['NationalIdNumber'])) {
            $errors[] = "Invalid national ID number format for applicant at index $index (expected XXXXXXXX-XXXXX-XXXXX-XX)";
        }
        
        // Validate Other F4 index numbers if provided
        if (!empty($applicant['Otherf4indexno'])) {
            $otherF4Numbers = array_map('trim', explode(',', $applicant['Otherf4indexno']));
            foreach ($otherF4Numbers as $otherF4) {
                if (!ValidationHelper::validateF4IndexNo($otherF4)) {
                    $errors[] = "Invalid Other F4 index number format for applicant at index $index: $otherF4";
                }
            }
        }
        
        // Validate Other F6 index numbers if provided
        if (!empty($applicant['Otherf6indexno'])) {
            $otherF6Numbers = array_map('trim', explode(',', $applicant['Otherf6indexno']));
            foreach ($otherF6Numbers as $otherF6) {
                if (!ValidationHelper::validateF6IndexNo($otherF6)) {
                    $errors[] = "Invalid Other F6 index number format for applicant at index $index: $otherF6";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Prepare programme submission data for XML request
     */
    private function prepareProgrammeSubmissionData(array $applicant): array
    {
        $requestData = [
            'f4indexno' => $applicant['f4indexno'],
            'f6indexno' => $applicant['f6indexno'],
            'Gender' => $applicant['Gender'],
            'SelectedProgrammes' => $applicant['SelectedProgrammes'],
            'Category' => $applicant['Category']
        ];
        
        // Add optional fields if provided
        $optionalFields = [
            'MobileNumber', 'OtherMobileNumber', 'EmailAddress', 'AdmissionStatus',
            'ProgrammeAdmitted', 'Reason', 'Nationality', 'Impairment', 'DateOfBirth',
            'NationalIdNumber', 'Otherf4indexno', 'Otherf6indexno'
        ];
        
        foreach ($optionalFields as $field) {
            if (!empty($applicant[$field])) {
                $requestData[$field] = $applicant[$field];
            }
        }
        
        return $requestData;
    }
    
    /**
     * Resubmit applicant details (3.6)
     * 
     * Usage Context: Undergraduate admission
     * Description: Update TCU on applicant details that might be different from the initial submission
     * 
     * @param array $applicantData Updated applicant data
     * @return array
     */
    public function resubmit(array $applicantData): array
    {
        // Validate applicant data
        $errors = ValidationHelper::validateApplicantData($applicantData);
        if (!empty($errors)) {
            throw new ValidationException("Applicant validation failed", $errors);
        }
        
        // F4 index number is required for resubmission
        if (empty($applicantData['f4indexno'])) {
            throw new ValidationException("F4 index number is required for resubmission");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'resubmitApplicant',
            'applicant' => $applicantData
        ];
        
        return $this->post('/applicants/resubmit', $requestData);
    }
    
    /**
     * Get admitted applicants (3.8)
     * 
     * Usage Context: Undergraduate admission
     * Description: Download a list of applicants with their admission status
     * 
     * @param string $programmeCode Programme code to filter by
     * @return array
     */
    public function getAdmitted(string $programmeCode): array
    {
        // Validate programme code
        if (!ValidationHelper::validateProgrammeCode($programmeCode)) {
            throw new ValidationException("Invalid programme code format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getAdmittedApplicants',
            'programme_code' => $programmeCode
        ];
        
        return $this->post('/applicants/getAdmitted', $requestData);
    }
    
    /**
     * Get applicants' admission status (3.10)
     * 
     * Usage Context: Undergraduate admission
     * Description: Download a list of applicants with their Undergraduate admission status
     * 
     * @param string $programmeCode Programme code to filter by
     * @return array
     */
    public function getStatus(string $programmeCode): array
    {
        // Validate programme code
        if (!ValidationHelper::validateProgrammeCode($programmeCode)) {
            throw new ValidationException("Invalid programme code format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getApplicantStatus',
            'programme_code' => $programmeCode
        ];
        
        return $this->post('/applicants/getStatus', $requestData);
    }
    
    /**
     * Get a list of confirmed applicants (3.11)
     * 
     * Usage Context: Undergraduate admission
     * Description: Get a list of confirmed applicants who had multiple admissions 
     * and their confirmation status per programme
     * 
     * @param string $programmeCode Programme code to filter by
     * @return array
     */
    public function getConfirmed(string $programmeCode): array
    {
        // Validate programme code
        if (!ValidationHelper::validateProgrammeCode($programmeCode)) {
            throw new ValidationException("Invalid programme code format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getConfirmedApplicants',
            'programme_code' => $programmeCode
        ];
        
        return $this->post('/applicants/getConfirmed', $requestData);
    }
    
    /**
     * Get applicant verification status
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getVerificationStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getApplicantVerificationStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Submit enrolled/registered students for undergraduate programmes
     * 
     * @param array $students Array of enrolled students
     * @return array
     */
    public function submitEnrolledStudents(array $students): array
    {
        return $this->post('/applicants/submitEnrolledStudents', [
            'students' => $students
        ]);
    }
    
    /**
     * Submit graduates
     * 
     * @param array $graduates Array of graduate data
     * @return array
     */
    public function submitGraduates(array $graduates): array
    {
        return $this->post('/applicants/submitGraduates', [
            'graduates' => $graduates
        ]);
    }
    
    /**
     * Submit institution staff
     * 
     * @param array $staff Array of staff data
     * @return array
     */
    public function submitInstitutionStaff(array $staff): array
    {
        return $this->post('/applicants/submitInstitutionStaff', [
            'staff' => $staff
        ]);
    }
    
    /**
     * Get internal transfer status
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getInternalTransferStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getInternalTransferStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Get inter-institutional transfer status
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getInterInstitutionalTransferStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getInterInstitutionalTransferStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Submit students dropouts
     * 
     * @param array $dropouts Array of dropout data
     * @return array
     */
    public function submitDropouts(array $dropouts): array
    {
        return $this->post('/applicants/submitStudentsDropOuts', [
            'dropouts' => $dropouts
        ]);
    }
    
    /**
     * Submit students who postponed studies
     * 
     * @param array $postponedStudents Array of postponed students data
     * @return array
     */
    public function submitPostponedStudents(array $postponedStudents): array
    {
        return $this->post('/applicants/submitPostponedStudents', [
            'postponed_students' => $postponedStudents
        ]);
    }
    
    /**
     * Submit admitted students into non-degree programmes
     * 
     * @param array $students Array of non-degree students
     * @return array
     */
    public function submitAdmittedNonDegree(array $students): array
    {
        return $this->post('/applicants/submitAdmittedNonDegree', [
            'students' => $students
        ]);
    }
    
    /**
     * Get verification status for non-degree admitted students
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getNonDegreeAdmittedStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getNonDegreeAdmittedStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Submit postgraduate applicants
     * 
     * @param array $applicants Array of postgraduate applicants
     * @return array
     */
    public function submitPostgraduateApplicants(array $applicants): array
    {
        return $this->post('/applicants/submitPostgraduateApplicants', [
            'applicants' => $applicants
        ]);
    }
    
    /**
     * Submit admitted students into postgraduate programmes
     * 
     * @param array $students Array of postgraduate students
     * @return array
     */
    public function submitAdmittedPostgraduate(array $students): array
    {
        return $this->post('/applicants/submitAdmittedPostgraduate', [
            'students' => $students
        ]);
    }
    
    /**
     * Get verification status for postgraduate admitted students
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getPostgraduateAdmittedStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getPostgraduateAdmittedStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Submit foreign applicants for bachelor's degree
     * 
     * @param array $applicants Array of foreign applicants
     * @return array
     */
    public function submitForeignApplicants(array $applicants): array
    {
        return $this->post('/applicants/submitForeignApplicants', [
            'applicants' => $applicants
        ]);
    }
    
    /**
     * Submit admitted foreigners into bachelor's degree programmes (Direct Entry)
     * 
     * @param array $students Array of foreign students
     * @return array
     */
    public function submitForeignAdmittedDirect(array $students): array
    {
        return $this->post('/applicants/submitForeignAdmittedDirect', [
            'students' => $students
        ]);
    }
    
    /**
     * Submit admitted foreigners into bachelor's degree programmes (Equivalent Entry)
     * 
     * @param array $students Array of foreign students
     * @return array
     */
    public function submitForeignAdmittedEquivalent(array $students): array
    {
        return $this->post('/applicants/submitForeignAdmittedEquivalent', [
            'students' => $students
        ]);
    }
}