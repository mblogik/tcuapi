<?php

/**
 * TCU API Client - Admission Resource
 * 
 * Resource class for handling admission-related API operations in the TCU system.
 * Provides methods for confirming admissions, managing admission status,
 * handling transfers, and processing admission-related operations.
 * 
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Resource class for admission management and confirmation operations
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Utils\ValidationHelper;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Models\Response\AdmittedApplicantTcuResponse;
use MBLogik\TCUAPIClient\Models\Response\ConfirmAdmissionTcuResponse;

class AdmissionResource extends BaseResource
{
    /**
     * Confirm applicant selection (3.4)
     * 
     * Usage Context: Undergraduate admission
     * Description: Each applicant with multiple selections shall use a TCU provided 
     * token/code to confirm selection to ONLY one HLI, and the same shall be confirmed with TCU
     * 
     * @param string $f4indexno Form four index number
     * @param string $confirmationCode TCU provided confirmation code
     * @return ConfirmAdmissionTcuResponse
     */
    public function confirm(string $f4indexno, string $confirmationCode): ConfirmAdmissionTcuResponse
    {
        // Validate input
        $errors = [];
        
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            $errors[] = "Invalid F4 Index Number format";
        }
        
        if (!$this->validateConfirmationCode($confirmationCode)) {
            $errors[] = "Invalid confirmation code format";
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        // Prepare request parameters
        $requestParameters = [
            'f4indexno' => $f4indexno,
            'ConfirmationCode' => $confirmationCode
        ];
        
        // Make API request
        $response = $this->client->makeRequest('/admission/confirm', $requestParameters);
        
        // Return structured response object
        $responseData = $response['ResponseParameters'] ?? $response;
        $responseData['ConfirmationCode'] = $confirmationCode; // Include original confirmation code
        return new ConfirmAdmissionTcuResponse($responseData);
    }
    
    /**
     * Validate confirmation code format
     * TCU sends confirmation codes to applicants which may be mixed alphanumeric
     * with uppercase or lowercase letters. Only requirements:
     * - Must not be empty
     * - Must not be exactly 4 characters (to avoid conflicts with other codes)
     */
    private function validateConfirmationCode(string $confirmationCode): bool
    {
        // Check if empty
        if (empty(trim($confirmationCode))) {
            return false;
        }
        
        // Check if exactly 4 characters (not allowed)
        if (strlen($confirmationCode) === 4) {
            return false;
        }
        
        // Allow any alphanumeric characters with uppercase or lowercase letters
        return preg_match('/^[a-zA-Z0-9]+$/', $confirmationCode) === 1;
    }
    
    /**
     * Unconfirm admission (3.5)
     * 
     * Usage Context: Undergraduate admission
     * Description: An applicant that had confirmed can reject the admission via 
     * the respective HLI online system, and the same shall be passed on to TCU
     * 
     * @param string $f4indexno Form four index number
     * @param string $confirmationCode TCU provided confirmation code
     * @return array
     */
    public function unconfirm(string $f4indexno, string $confirmationCode): array
    {
        // Validate input
        $errors = [];
        
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            $errors[] = "Invalid F4 Index Number format";
        }
        
        if (!$this->validateConfirmationCode($confirmationCode)) {
            $errors[] = "Invalid confirmation code format";
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        // Prepare request parameters
        $requestParameters = [
            'f4indexno' => $f4indexno,
            'ConfirmationCode' => $confirmationCode
        ];
        
        return $this->client->makeRequest('/admission/unconfirm', $requestParameters);
    }
    
    /**
     * Get admitted applicants (3.8)
     * 
     * Usage Context: Undergraduate admission
     * Description: Download a list of applicants admitted to a specific programme
     * with their contact details and admission status
     * 
     * @param string $programmeCode Programme code (e.g., DM023)
     * @return AdmittedApplicantTcuResponse[]
     */
    public function getAdmitted(string $programmeCode): array
    {
        // Validate programme code format (allows both UD and DM prefixes)
        if (!preg_match('/^[A-Z]{2}[0-9]{3}$/', $programmeCode)) {
            throw new ValidationException("Invalid programme code format. Expected format: XX000 (e.g., DM023, UD038)");
        }
        
        // Prepare request parameters matching the exact API specification
        $requestParameters = [
            'ProgrammeCode' => $programmeCode
        ];
        
        // Make API request
        $response = $this->client->makeRequest('/admission/getAdmitted', $requestParameters);
        
        // Convert response to array of AdmittedApplicantTcuResponse objects
        return $this->parseAdmittedApplicantsResponse($response, $programmeCode);
    }
    
    /**
     * Parse admitted applicants response into structured objects
     * 
     * @param array $response Raw API response
     * @param string $programmeCode Programme code for context
     * @return AdmittedApplicantTcuResponse[]
     */
    private function parseAdmittedApplicantsResponse(array $response, string $programmeCode): array
    {
        $admittedApplicants = [];
        
        // Check if response has applicant data
        if (!isset($response['ResponseParameters']['Applicant'])) {
            return $admittedApplicants; // Return empty array if no applicants
        }
        
        $applicantData = $response['ResponseParameters']['Applicant'];
        
        // Handle both single applicant and multiple applicants
        if (isset($applicantData['f4indexno'])) {
            // Single applicant - wrap in array for consistent processing
            $applicantData = [$applicantData];
        }
        
        // Convert each applicant to AdmittedApplicantTcuResponse object
        foreach ($applicantData as $applicant) {
            $admittedApplicants[] = new AdmittedApplicantTcuResponse($applicant, $programmeCode);
        }
        
        return $admittedApplicants;
    }
    
    /**
     * Get programmes with admitted candidates (3.9)
     * 
     * Usage Context: Undergraduate admission
     * Description: Download a list of programme codes for programmes with selected applicants
     * 
     * @return array
     */
    public function getProgrammes(): array
    {
        // Prepare request data
        $requestData = [
            'operation' => 'getProgrammesWithAdmitted'
        ];
        
        return $this->post('/admission/getProgrammes', $requestData);
    }
    
    /**
     * Get applicants' admission status
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getStatus(string $programmeCode): array
    {
        return $this->post('/applicants/getStatus', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Get a list of confirmed applicants
     * 
     * @param string $programmeCode
     * @return array
     */
    public function getConfirmed(string $programmeCode): array
    {
        return $this->post('/applicants/getConfirmed', [
            'programme_code' => $programmeCode
        ]);
    }
    
    /**
     * Request confirmation code for multiple admission students
     * 
     * @param string $formFourIndexNumber
     * @return array
     */
    public function requestConfirmationCode(string $formFourIndexNumber): array
    {
        return $this->post('/admission/requestConfirmationCode', [
            'form_four_index_number' => $formFourIndexNumber
        ]);
    }
    
    /**
     * Cancel/Reject admission
     * 
     * @param string $formFourIndexNumber
     * @param string $reason
     * @return array
     */
    public function reject(string $formFourIndexNumber, string $reason = ''): array
    {
        $data = [
            'form_four_index_number' => $formFourIndexNumber,
            'reason' => $reason
        ];
        
        return $this->post('/admission/reject', $data);
    }
    
    /**
     * Submit internal transfers
     * 
     * @param array $transfers Array of internal transfer data
     * @return array
     */
    public function submitInternalTransfers(array $transfers): array
    {
        return $this->post('/admission/submitInternalTransfers', [
            'transfers' => $transfers
        ]);
    }
    
    /**
     * Submit inter-institutional transfers
     * 
     * @param array $transfers Array of inter-institutional transfer data
     * @return array
     */
    public function submitInterInstitutionalTransfers(array $transfers): array
    {
        return $this->post('/admission/submitInterInstitutionalTransfers', [
            'transfers' => $transfers
        ]);
    }
    
    /**
     * Restore cancelled admission
     * 
     * @param string $formFourIndexNumber
     * @param string $reason
     * @return array
     */
    public function restoreCancelledAdmission(string $formFourIndexNumber, string $reason = ''): array
    {
        $data = [
            'form_four_index_number' => $formFourIndexNumber,
            'reason' => $reason
        ];
        
        return $this->post('/admission/restoreCancelledAdmission', $data);
    }
}