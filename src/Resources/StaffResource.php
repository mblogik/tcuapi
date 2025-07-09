<?php

/**
 * TCU API Client - Staff Resource
 *
 * This file contains the StaffResource class which handles all staff-related
 * operations for the TCU API. It provides methods for interacting with staff
 * endpoints including staff registration, management, and verification.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles staff operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class StaffResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.24 - Register Staff Member
     *
     * Registers a new staff member in the TCU system.
     *
     * @param array $staffData Staff registration data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerStaffMember(array $staffData): array
    {
        $validationErrors = [];

        // Validate required fields
        $requiredFields = ['staff_id', 'firstname', 'surname', 'position', 'institution_code', 'department'];
        
        foreach ($requiredFields as $field) {
            if (empty($staffData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate staff ID format
        if (!empty($staffData['staff_id']) && !preg_match('/^[A-Z0-9]{6,20}$/', $staffData['staff_id'])) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        // Validate institution code
        if (!empty($staffData['institution_code']) && !$this->validator->isValidInstitutionCode($staffData['institution_code'])) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate email if provided
        if (!empty($staffData['email']) && !$this->validator->isValidEmail($staffData['email'])) {
            $validationErrors[] = 'Invalid email format';
        }

        // Validate phone number if provided
        if (!empty($staffData['phone']) && !$this->validator->isValidPhoneNumber($staffData['phone'])) {
            $validationErrors[] = 'Invalid phone number format';
        }

        // Validate position
        if (!empty($staffData['position'])) {
            $allowedPositions = [
                'professor', 'associate_professor', 'senior_lecturer', 'lecturer', 'assistant_lecturer',
                'tutorial_assistant', 'research_fellow', 'dean', 'hod', 'registrar', 'bursar',
                'librarian', 'coordinator', 'administrator', 'technician', 'support_staff'
            ];
            if (!in_array($staffData['position'], $allowedPositions)) {
                $validationErrors[] = 'Invalid staff position';
            }
        }

        // Validate employment status if provided
        if (!empty($staffData['employment_status'])) {
            $allowedStatuses = ['permanent', 'contract', 'temporary', 'visiting', 'emeritus', 'retired'];
            if (!in_array($staffData['employment_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid employment status';
            }
        }

        // Validate qualification level if provided
        if (!empty($staffData['qualification_level'])) {
            $allowedLevels = ['certificate', 'diploma', 'degree', 'masters', 'doctorate', 'postdoc'];
            if (!in_array($staffData['qualification_level'], $allowedLevels)) {
                $validationErrors[] = 'Invalid qualification level';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/register', [
            'operation' => 'registerStaffMember',
            'staff_data' => $staffData
        ], 'POST');
    }

    /**
     * 3.25 - Get Staff Details
     *
     * Retrieves detailed information about a staff member.
     *
     * @param string $staffId Staff ID
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getStaffDetails(string $staffId): array
    {
        $validationErrors = [];

        // Validate staff ID
        if (empty($staffId) || !preg_match('/^[A-Z0-9]{6,20}$/', $staffId)) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/getDetails', [
            'operation' => 'getStaffDetails',
            'staff_id' => $staffId
        ], 'POST');
    }

    /**
     * Update Staff Information
     *
     * Updates staff member information in the system.
     *
     * @param string $staffId Staff ID
     * @param array $updateData Data to update
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateStaffInformation(string $staffId, array $updateData): array
    {
        $validationErrors = [];

        // Validate staff ID
        if (empty($staffId) || !preg_match('/^[A-Z0-9]{6,20}$/', $staffId)) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        // Validate update data is not empty
        if (empty($updateData)) {
            $validationErrors[] = 'Update data cannot be empty';
        }

        // Validate specific fields if provided
        if (isset($updateData['email']) && !empty($updateData['email']) && !$this->validator->isValidEmail($updateData['email'])) {
            $validationErrors[] = 'Invalid email format';
        }

        if (isset($updateData['phone']) && !empty($updateData['phone']) && !$this->validator->isValidPhoneNumber($updateData['phone'])) {
            $validationErrors[] = 'Invalid phone number format';
        }

        if (isset($updateData['position']) && !empty($updateData['position'])) {
            $allowedPositions = [
                'professor', 'associate_professor', 'senior_lecturer', 'lecturer', 'assistant_lecturer',
                'tutorial_assistant', 'research_fellow', 'dean', 'hod', 'registrar', 'bursar',
                'librarian', 'coordinator', 'administrator', 'technician', 'support_staff'
            ];
            if (!in_array($updateData['position'], $allowedPositions)) {
                $validationErrors[] = 'Invalid staff position';
            }
        }

        if (isset($updateData['employment_status']) && !empty($updateData['employment_status'])) {
            $allowedStatuses = ['permanent', 'contract', 'temporary', 'visiting', 'emeritus', 'retired'];
            if (!in_array($updateData['employment_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid employment status';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/updateInformation', [
            'operation' => 'updateStaffInformation',
            'staff_id' => $staffId,
            'update_data' => $updateData
        ], 'POST');
    }

    /**
     * Get staff by institution
     *
     * @param string $institutionCode Institution code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getStaffByInstitution(string $institutionCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['position']) && !empty($filters['position'])) {
                $allowedPositions = [
                    'professor', 'associate_professor', 'senior_lecturer', 'lecturer', 'assistant_lecturer',
                    'tutorial_assistant', 'research_fellow', 'dean', 'hod', 'registrar', 'bursar',
                    'librarian', 'coordinator', 'administrator', 'technician', 'support_staff'
                ];
                if (!in_array($filters['position'], $allowedPositions)) {
                    $validationErrors[] = 'Invalid position filter';
                }
            }

            if (isset($filters['employment_status']) && !empty($filters['employment_status'])) {
                $allowedStatuses = ['permanent', 'contract', 'temporary', 'visiting', 'emeritus', 'retired'];
                if (!in_array($filters['employment_status'], $allowedStatuses)) {
                    $validationErrors[] = 'Invalid employment status filter';
                }
            }

            if (isset($filters['qualification_level']) && !empty($filters['qualification_level'])) {
                $allowedLevels = ['certificate', 'diploma', 'degree', 'masters', 'doctorate', 'postdoc'];
                if (!in_array($filters['qualification_level'], $allowedLevels)) {
                    $validationErrors[] = 'Invalid qualification level filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getStaffByInstitution',
            'institution_code' => $institutionCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/staff/getByInstitution', $requestData, 'POST');
    }

    /**
     * Get staff by department
     *
     * @param string $institutionCode Institution code
     * @param string $department Department name
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getStaffByDepartment(string $institutionCode, string $department, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate department
        if (empty(trim($department))) {
            $validationErrors[] = 'Department is required';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['position']) && !empty($filters['position'])) {
                $allowedPositions = [
                    'professor', 'associate_professor', 'senior_lecturer', 'lecturer', 'assistant_lecturer',
                    'tutorial_assistant', 'research_fellow', 'dean', 'hod', 'registrar', 'bursar',
                    'librarian', 'coordinator', 'administrator', 'technician', 'support_staff'
                ];
                if (!in_array($filters['position'], $allowedPositions)) {
                    $validationErrors[] = 'Invalid position filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getStaffByDepartment',
            'institution_code' => $institutionCode,
            'department' => $department
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/staff/getByDepartment', $requestData, 'POST');
    }

    /**
     * Search staff members
     *
     * @param array $searchCriteria Search criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function searchStaff(array $searchCriteria): array
    {
        $validationErrors = [];

        // Validate search criteria is not empty
        if (empty($searchCriteria)) {
            $validationErrors[] = 'Search criteria cannot be empty';
        }

        // Validate specific search fields if provided
        if (isset($searchCriteria['staff_id']) && !empty($searchCriteria['staff_id']) && 
            !preg_match('/^[A-Z0-9]{6,20}$/', $searchCriteria['staff_id'])) {
            $validationErrors[] = 'Invalid staff ID in search criteria';
        }

        if (isset($searchCriteria['institution_code']) && !empty($searchCriteria['institution_code']) && 
            !$this->validator->isValidInstitutionCode($searchCriteria['institution_code'])) {
            $validationErrors[] = 'Invalid institution code in search criteria';
        }

        if (isset($searchCriteria['email']) && !empty($searchCriteria['email']) && 
            !$this->validator->isValidEmail($searchCriteria['email'])) {
            $validationErrors[] = 'Invalid email in search criteria';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/search', [
            'operation' => 'searchStaff',
            'search_criteria' => $searchCriteria
        ], 'POST');
    }

    /**
     * Get staff statistics
     *
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getStaffStatistics(array $filters = []): array
    {
        $validationErrors = [];

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['institution_code']) && !empty($filters['institution_code']) && 
                !$this->validator->isValidInstitutionCode($filters['institution_code'])) {
                $validationErrors[] = 'Invalid institution code filter';
            }

            if (isset($filters['position']) && !empty($filters['position'])) {
                $allowedPositions = [
                    'professor', 'associate_professor', 'senior_lecturer', 'lecturer', 'assistant_lecturer',
                    'tutorial_assistant', 'research_fellow', 'dean', 'hod', 'registrar', 'bursar',
                    'librarian', 'coordinator', 'administrator', 'technician', 'support_staff'
                ];
                if (!in_array($filters['position'], $allowedPositions)) {
                    $validationErrors[] = 'Invalid position filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getStaffStatistics'
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/staff/getStatistics', $requestData, 'POST');
    }

    /**
     * Verify staff credentials
     *
     * @param string $staffId Staff ID
     * @param array $credentialsData Credentials data to verify
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function verifyStaffCredentials(string $staffId, array $credentialsData): array
    {
        $validationErrors = [];

        // Validate staff ID
        if (empty($staffId) || !preg_match('/^[A-Z0-9]{6,20}$/', $staffId)) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        // Validate credentials data
        if (empty($credentialsData)) {
            $validationErrors[] = 'Credentials data cannot be empty';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/verifyCredentials', [
            'operation' => 'verifyStaffCredentials',
            'staff_id' => $staffId,
            'credentials_data' => $credentialsData
        ], 'POST');
    }

    /**
     * Update staff employment status
     *
     * @param string $staffId Staff ID
     * @param string $employmentStatus New employment status
     * @param string $reason Reason for status change
     * @param array $additionalData Additional data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateStaffEmploymentStatus(string $staffId, string $employmentStatus, string $reason, array $additionalData = []): array
    {
        $validationErrors = [];

        // Validate staff ID
        if (empty($staffId) || !preg_match('/^[A-Z0-9]{6,20}$/', $staffId)) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        // Validate employment status
        $allowedStatuses = ['permanent', 'contract', 'temporary', 'visiting', 'emeritus', 'retired', 'terminated'];
        if (!in_array($employmentStatus, $allowedStatuses)) {
            $validationErrors[] = 'Invalid employment status';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Status change reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'updateStaffEmploymentStatus',
            'staff_id' => $staffId,
            'employment_status' => $employmentStatus,
            'reason' => $reason
        ];

        if (!empty($additionalData)) {
            $requestData['additional_data'] = $additionalData;
        }

        return $this->client->makeRequest('/staff/updateEmploymentStatus', $requestData, 'POST');
    }

    /**
     * Get staff employment history
     *
     * @param string $staffId Staff ID
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getStaffEmploymentHistory(string $staffId): array
    {
        $validationErrors = [];

        // Validate staff ID
        if (empty($staffId) || !preg_match('/^[A-Z0-9]{6,20}$/', $staffId)) {
            $validationErrors[] = 'Invalid staff ID format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/getEmploymentHistory', [
            'operation' => 'getStaffEmploymentHistory',
            'staff_id' => $staffId
        ], 'POST');
    }

    /**
     * Bulk register staff members
     *
     * @param array $staffData Array of staff data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkRegisterStaff(array $staffData): array
    {
        $validationErrors = [];

        // Validate staff data array
        if (empty($staffData)) {
            $validationErrors[] = 'Staff data array cannot be empty';
        } else {
            foreach ($staffData as $index => $staff) {
                if (!is_array($staff)) {
                    $validationErrors[] = "Staff data at index $index must be an array";
                    continue;
                }

                // Validate required fields for each staff member
                $requiredFields = ['staff_id', 'firstname', 'surname', 'position', 'institution_code', 'department'];
                
                foreach ($requiredFields as $field) {
                    if (empty($staff[$field])) {
                        $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . " is required for staff at index $index";
                    }
                }

                // Validate staff ID
                if (!empty($staff['staff_id']) && !preg_match('/^[A-Z0-9]{6,20}$/', $staff['staff_id'])) {
                    $validationErrors[] = "Invalid staff ID format for staff at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/bulkRegister', [
            'operation' => 'bulkRegisterStaff',
            'staff_data' => $staffData
        ], 'POST');
    }

    /**
     * Generate staff report
     *
     * @param array $reportCriteria Report criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function generateStaffReport(array $reportCriteria): array
    {
        $validationErrors = [];

        // Validate report criteria
        if (empty($reportCriteria)) {
            $validationErrors[] = 'Report criteria cannot be empty';
        }

        // Validate specific criteria if provided
        if (isset($reportCriteria['institution_code']) && !empty($reportCriteria['institution_code']) && 
            !$this->validator->isValidInstitutionCode($reportCriteria['institution_code'])) {
            $validationErrors[] = 'Invalid institution code in report criteria';
        }

        if (isset($reportCriteria['report_type']) && !empty($reportCriteria['report_type'])) {
            $allowedTypes = ['summary', 'detailed', 'qualification', 'department', 'employment_status'];
            if (!in_array($reportCriteria['report_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid report type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/staff/generateReport', [
            'operation' => 'generateStaffReport',
            'report_criteria' => $reportCriteria
        ], 'POST');
    }
}