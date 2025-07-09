<?php

/**
 * TCU API Client - Dashboard Resource
 * 
 * Resource class for handling dashboard-related API operations in the TCU system.
 * Provides methods for populating dashboard statistics, retrieving admission summaries,
 * and managing enrollment statistics and reporting data.
 * 
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Resource class for dashboard statistics and reporting operations
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Utils\ValidationHelper;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Models\Response\DashboardPopulateTcuResponse;

class DashboardResource extends BaseResource
{
    /**
     * Populate dashboard with programme statistics (3.7)
     * 
     * Usage Context: Undergraduate admission
     * Description: Update latest application statistics to the TCU dashboard (update once a day)
     * i.e. programme code, number of males and number of females
     * 
     * @param string $programmeCode Programme code (e.g., DM038)
     * @param int $males Number of male applicants
     * @param int $females Number of female applicants
     * @return DashboardPopulateTcuResponse
     */
    public function populate(string $programmeCode, int $males, int $females): DashboardPopulateTcuResponse
    {
        // Validate programme code format (allows both UD and DM prefixes)
        if (!preg_match('/^[A-Z]{2}[0-9]{3}$/', $programmeCode)) {
            throw new ValidationException("Invalid programme code format. Expected format: XX000 (e.g., DM038, UD023)");
        }
        
        // Validate numeric values
        if ($males < 0) {
            throw new ValidationException("Male count must be non-negative");
        }
        
        if ($females < 0) {
            throw new ValidationException("Female count must be non-negative");
        }
        
        // Prepare request parameters matching the exact API specification
        $requestParameters = [
            'ProgrammeCode' => $programmeCode,
            'Males' => (string)$males,
            'Females' => (string)$females
        ];
        
        // Make API request
        $response = $this->client->makeRequest('/dashboard/populate', $requestParameters);
        
        // Return structured response object
        return new DashboardPopulateTcuResponse($response['ResponseParameters'] ?? $response, $males, $females);
    }
    
    /**
     * Request confirmation code (3.12)
     * 
     * Usage Context: Undergraduate admission
     * Description: An applicant that had multiple admission can request confirmation code 
     * via the respective HLI online system, and the same shall be passed on to TCU
     * 
     * @param string $f4indexno Form four index number
     * @return array
     */
    public function requestConfirmationCode(string $f4indexno): array
    {
        // Validate F4 index number
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            throw new ValidationException("Invalid F4 Index Number format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'requestConfirmationCode',
            'f4indexno' => $f4indexno
        ];
        
        return $this->post('/dashboard/requestConfirmationCode', $requestData);
    }
    
    /**
     * Cancel/Reject admission (3.13)
     * 
     * Usage Context: Undergraduate admission
     * Description: An applicant that had been admitted can cancel his admission 
     * via the respective HLI online system, and the same shall be passed on to TCU
     * 
     * @param string $f4indexno Form four index number
     * @param string $reason Reason for cancellation/rejection
     * @return array
     */
    public function reject(string $f4indexno, string $reason): array
    {
        // Validate F4 index number
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            throw new ValidationException("Invalid F4 Index Number format");
        }
        
        // Validate reason
        if (empty($reason)) {
            throw new ValidationException("Rejection reason is required");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'rejectAdmission',
            'f4indexno' => $f4indexno,
            'reason' => $reason
        ];
        
        return $this->post('/dashboard/reject', $requestData);
    }
    
    /**
     * Get dashboard statistics
     * 
     * @param string|null $programmeCode Optional programme code filter
     * @return array
     */
    public function getStats(?string $programmeCode = null): array
    {
        $data = [];
        
        if ($programmeCode) {
            $data['programme_code'] = $programmeCode;
        }
        
        return $this->post('/dashboard/getStats', $data);
    }
    
    /**
     * Get admission summary for dashboard
     * 
     * @param string $academicYear
     * @param string|null $programmeCode Optional programme filter
     * @return array
     */
    public function getAdmissionSummary(string $academicYear, ?string $programmeCode = null): array
    {
        $data = [
            'academic_year' => $academicYear
        ];
        
        if ($programmeCode) {
            $data['programme_code'] = $programmeCode;
        }
        
        return $this->post('/dashboard/getAdmissionSummary', $data);
    }
    
    /**
     * Get enrollment statistics
     * 
     * @param string $programmeCode
     * @param string|null $academicYear
     * @return array
     */
    public function getEnrollmentStats(string $programmeCode, ?string $academicYear = null): array
    {
        $data = [
            'programme_code' => $programmeCode
        ];
        
        if ($academicYear) {
            $data['academic_year'] = $academicYear;
        }
        
        return $this->post('/dashboard/getEnrollmentStats', $data);
    }
}