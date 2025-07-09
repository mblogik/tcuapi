<?php

/**
 * TCU API Client - Postgraduate Resource
 *
 * This file contains the PostgraduateResource class which handles all postgraduate-related
 * operations for the TCU API. It provides methods for interacting with postgraduate
 * endpoints including masters and doctorate program management.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles postgraduate operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class PostgraduateResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.29 - Register Postgraduate Student
     *
     * Registers a new postgraduate student in the TCU system.
     *
     * @param array $studentData Postgraduate student registration data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerPostgraduateStudent(array $studentData): array
    {
        $validationErrors = [];

        // Validate required fields
        $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'study_level'];
        
        foreach ($requiredFields as $field) {
            if (empty($studentData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate F4 index number
        if (!empty($studentData['f4indexno']) && !$this->validator->isValidF4IndexNumber($studentData['f4indexno'])) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate programme code
        if (!empty($studentData['programme_code']) && !$this->validator->isValidProgrammeCode($studentData['programme_code'])) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate institution code
        if (!empty($studentData['institution_code']) && !$this->validator->isValidInstitutionCode($studentData['institution_code'])) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate study level
        if (!empty($studentData['study_level'])) {
            $allowedLevels = ['masters', 'doctorate', 'postdoc', 'phd', 'mphil', 'msc', 'ma', 'mba'];
            if (!in_array($studentData['study_level'], $allowedLevels)) {
                $validationErrors[] = 'Invalid study level';
            }
        }

        // Validate email if provided
        if (!empty($studentData['email']) && !$this->validator->isValidEmail($studentData['email'])) {
            $validationErrors[] = 'Invalid email format';
        }

        // Validate phone number if provided
        if (!empty($studentData['phone']) && !$this->validator->isValidPhoneNumber($studentData['phone'])) {
            $validationErrors[] = 'Invalid phone number format';
        }

        // Validate study mode if provided
        if (!empty($studentData['study_mode'])) {
            $allowedModes = ['full_time', 'part_time', 'distance', 'sandwich', 'research'];
            if (!in_array($studentData['study_mode'], $allowedModes)) {
                $validationErrors[] = 'Invalid study mode';
            }
        }

        // Validate funding source if provided
        if (!empty($studentData['funding_source'])) {
            $allowedSources = ['government', 'private', 'scholarship', 'self_sponsored', 'employer', 'research_grant'];
            if (!in_array($studentData['funding_source'], $allowedSources)) {
                $validationErrors[] = 'Invalid funding source';
            }
        }

        // Validate research area if provided for doctorate/PhD
        if (!empty($studentData['study_level']) && in_array($studentData['study_level'], ['doctorate', 'phd']) && 
            empty($studentData['research_area'])) {
            $validationErrors[] = 'Research area is required for doctorate/PhD programmes';
        }

        // Validate supervisor information if provided
        if (!empty($studentData['supervisor_staff_id']) && !preg_match('/^[A-Z0-9]{6,20}$/', $studentData['supervisor_staff_id'])) {
            $validationErrors[] = 'Invalid supervisor staff ID format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/register', [
            'operation' => 'registerPostgraduateStudent',
            'student_data' => $studentData
        ], 'POST');
    }

    /**
     * 3.30 - Get Postgraduate Student Details
     *
     * Retrieves detailed information about a postgraduate student.
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getPostgraduateStudentDetails(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/getDetails', [
            'operation' => 'getPostgraduateStudentDetails',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * Update Postgraduate Student Information
     *
     * Updates postgraduate student information in the system.
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $updateData Data to update
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updatePostgraduateStudentInformation(string $f4indexno, array $updateData): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
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

        if (isset($updateData['study_mode']) && !empty($updateData['study_mode'])) {
            $allowedModes = ['full_time', 'part_time', 'distance', 'sandwich', 'research'];
            if (!in_array($updateData['study_mode'], $allowedModes)) {
                $validationErrors[] = 'Invalid study mode';
            }
        }

        if (isset($updateData['completion_status']) && !empty($updateData['completion_status'])) {
            $allowedStatuses = ['in_progress', 'completed', 'discontinued', 'deferred', 'thesis_submitted', 'viva_pending', 'corrections_pending'];
            if (!in_array($updateData['completion_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid completion status';
            }
        }

        if (isset($updateData['supervisor_staff_id']) && !empty($updateData['supervisor_staff_id']) && 
            !preg_match('/^[A-Z0-9]{6,20}$/', $updateData['supervisor_staff_id'])) {
            $validationErrors[] = 'Invalid supervisor staff ID format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/updateInformation', [
            'operation' => 'updatePostgraduateStudentInformation',
            'f4indexno' => $f4indexno,
            'update_data' => $updateData
        ], 'POST');
    }

    /**
     * Get postgraduate students by programme
     *
     * @param string $programmeCode Programme code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getPostgraduateStudentsByProgramme(string $programmeCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['study_level']) && !empty($filters['study_level'])) {
                $allowedLevels = ['masters', 'doctorate', 'postdoc', 'phd', 'mphil', 'msc', 'ma', 'mba'];
                if (!in_array($filters['study_level'], $allowedLevels)) {
                    $validationErrors[] = 'Invalid study level filter';
                }
            }

            if (isset($filters['study_mode']) && !empty($filters['study_mode'])) {
                $allowedModes = ['full_time', 'part_time', 'distance', 'sandwich', 'research'];
                if (!in_array($filters['study_mode'], $allowedModes)) {
                    $validationErrors[] = 'Invalid study mode filter';
                }
            }

            if (isset($filters['completion_status']) && !empty($filters['completion_status'])) {
                $allowedStatuses = ['in_progress', 'completed', 'discontinued', 'deferred', 'thesis_submitted', 'viva_pending', 'corrections_pending'];
                if (!in_array($filters['completion_status'], $allowedStatuses)) {
                    $validationErrors[] = 'Invalid completion status filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getPostgraduateStudentsByProgramme',
            'programme_code' => $programmeCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/postgraduate/getByProgramme', $requestData, 'POST');
    }

    /**
     * Get postgraduate students by institution
     *
     * @param string $institutionCode Institution code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getPostgraduateStudentsByInstitution(string $institutionCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['study_level']) && !empty($filters['study_level'])) {
                $allowedLevels = ['masters', 'doctorate', 'postdoc', 'phd', 'mphil', 'msc', 'ma', 'mba'];
                if (!in_array($filters['study_level'], $allowedLevels)) {
                    $validationErrors[] = 'Invalid study level filter';
                }
            }

            if (isset($filters['programme_code']) && !empty($filters['programme_code']) && 
                !$this->validator->isValidProgrammeCode($filters['programme_code'])) {
                $validationErrors[] = 'Invalid programme code filter';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getPostgraduateStudentsByInstitution',
            'institution_code' => $institutionCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/postgraduate/getByInstitution', $requestData, 'POST');
    }

    /**
     * Search postgraduate students
     *
     * @param array $searchCriteria Search criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function searchPostgraduateStudents(array $searchCriteria): array
    {
        $validationErrors = [];

        // Validate search criteria is not empty
        if (empty($searchCriteria)) {
            $validationErrors[] = 'Search criteria cannot be empty';
        }

        // Validate specific search fields if provided
        if (isset($searchCriteria['f4indexno']) && !empty($searchCriteria['f4indexno']) && 
            !$this->validator->isValidF4IndexNumber($searchCriteria['f4indexno'])) {
            $validationErrors[] = 'Invalid F4 Index Number in search criteria';
        }

        if (isset($searchCriteria['programme_code']) && !empty($searchCriteria['programme_code']) && 
            !$this->validator->isValidProgrammeCode($searchCriteria['programme_code'])) {
            $validationErrors[] = 'Invalid programme code in search criteria';
        }

        if (isset($searchCriteria['institution_code']) && !empty($searchCriteria['institution_code']) && 
            !$this->validator->isValidInstitutionCode($searchCriteria['institution_code'])) {
            $validationErrors[] = 'Invalid institution code in search criteria';
        }

        if (isset($searchCriteria['email']) && !empty($searchCriteria['email']) && 
            !$this->validator->isValidEmail($searchCriteria['email'])) {
            $validationErrors[] = 'Invalid email in search criteria';
        }

        if (isset($searchCriteria['supervisor_staff_id']) && !empty($searchCriteria['supervisor_staff_id']) && 
            !preg_match('/^[A-Z0-9]{6,20}$/', $searchCriteria['supervisor_staff_id'])) {
            $validationErrors[] = 'Invalid supervisor staff ID in search criteria';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/search', [
            'operation' => 'searchPostgraduateStudents',
            'search_criteria' => $searchCriteria
        ], 'POST');
    }

    /**
     * Get postgraduate programme statistics
     *
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getPostgraduateProgrammeStatistics(array $filters = []): array
    {
        $validationErrors = [];

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['programme_code']) && !empty($filters['programme_code']) && 
                !$this->validator->isValidProgrammeCode($filters['programme_code'])) {
                $validationErrors[] = 'Invalid programme code filter';
            }

            if (isset($filters['institution_code']) && !empty($filters['institution_code']) && 
                !$this->validator->isValidInstitutionCode($filters['institution_code'])) {
                $validationErrors[] = 'Invalid institution code filter';
            }

            if (isset($filters['study_level']) && !empty($filters['study_level'])) {
                $allowedLevels = ['masters', 'doctorate', 'postdoc', 'phd', 'mphil', 'msc', 'ma', 'mba'];
                if (!in_array($filters['study_level'], $allowedLevels)) {
                    $validationErrors[] = 'Invalid study level filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getPostgraduateProgrammeStatistics'
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/postgraduate/getStatistics', $requestData, 'POST');
    }

    /**
     * Register thesis submission
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $thesisData Thesis submission data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerThesisSubmission(string $f4indexno, array $thesisData): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate required thesis fields
        $requiredFields = ['thesis_title', 'submission_date', 'supervisor_staff_id'];
        
        foreach ($requiredFields as $field) {
            if (empty($thesisData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate supervisor staff ID
        if (!empty($thesisData['supervisor_staff_id']) && !preg_match('/^[A-Z0-9]{6,20}$/', $thesisData['supervisor_staff_id'])) {
            $validationErrors[] = 'Invalid supervisor staff ID format';
        }

        // Validate submission date format
        if (!empty($thesisData['submission_date']) && !$this->validator->isValidDate($thesisData['submission_date'])) {
            $validationErrors[] = 'Invalid submission date format';
        }

        // Validate thesis type if provided
        if (!empty($thesisData['thesis_type'])) {
            $allowedTypes = ['dissertation', 'thesis', 'project', 'research_report'];
            if (!in_array($thesisData['thesis_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid thesis type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/registerThesisSubmission', [
            'operation' => 'registerThesisSubmission',
            'f4indexno' => $f4indexno,
            'thesis_data' => $thesisData
        ], 'POST');
    }

    /**
     * Update research progress
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $progressData Research progress data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateResearchProgress(string $f4indexno, array $progressData): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate progress data
        if (empty($progressData)) {
            $validationErrors[] = 'Progress data cannot be empty';
        }

        // Validate progress percentage if provided
        if (!empty($progressData['progress_percentage']) && 
            (!is_numeric($progressData['progress_percentage']) || 
             $progressData['progress_percentage'] < 0 || 
             $progressData['progress_percentage'] > 100)) {
            $validationErrors[] = 'Invalid progress percentage (must be 0-100)';
        }

        // Validate milestone status if provided
        if (!empty($progressData['milestone_status'])) {
            $allowedStatuses = ['proposal_submitted', 'proposal_approved', 'data_collection', 'analysis', 'writing', 'review', 'completed'];
            if (!in_array($progressData['milestone_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid milestone status';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/updateResearchProgress', [
            'operation' => 'updateResearchProgress',
            'f4indexno' => $f4indexno,
            'progress_data' => $progressData
        ], 'POST');
    }

    /**
     * Assign supervisor
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $supervisorStaffId Supervisor's staff ID
     * @param string $supervisorRole Supervisor role (primary, secondary, external)
     * @param array $additionalData Additional supervisor data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function assignSupervisor(string $f4indexno, string $supervisorStaffId, string $supervisorRole = 'primary', array $additionalData = []): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate supervisor staff ID
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $supervisorStaffId)) {
            $validationErrors[] = 'Invalid supervisor staff ID format';
        }

        // Validate supervisor role
        $allowedRoles = ['primary', 'secondary', 'external', 'co_supervisor'];
        if (!in_array($supervisorRole, $allowedRoles)) {
            $validationErrors[] = 'Invalid supervisor role';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'assignSupervisor',
            'f4indexno' => $f4indexno,
            'supervisor_staff_id' => $supervisorStaffId,
            'supervisor_role' => $supervisorRole
        ];

        if (!empty($additionalData)) {
            $requestData['additional_data'] = $additionalData;
        }

        return $this->client->makeRequest('/postgraduate/assignSupervisor', $requestData, 'POST');
    }

    /**
     * Get supervisor assignments
     *
     * @param string $supervisorStaffId Supervisor's staff ID
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getSupervisorAssignments(string $supervisorStaffId): array
    {
        $validationErrors = [];

        // Validate supervisor staff ID
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $supervisorStaffId)) {
            $validationErrors[] = 'Invalid supervisor staff ID format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/getSupervisorAssignments', [
            'operation' => 'getSupervisorAssignments',
            'supervisor_staff_id' => $supervisorStaffId
        ], 'POST');
    }

    /**
     * Bulk register postgraduate students
     *
     * @param array $studentsData Array of student data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkRegisterPostgraduateStudents(array $studentsData): array
    {
        $validationErrors = [];

        // Validate students data array
        if (empty($studentsData)) {
            $validationErrors[] = 'Students data array cannot be empty';
        } else {
            foreach ($studentsData as $index => $studentData) {
                if (!is_array($studentData)) {
                    $validationErrors[] = "Student data at index $index must be an array";
                    continue;
                }

                // Validate required fields for each student
                $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'study_level'];
                
                foreach ($requiredFields as $field) {
                    if (empty($studentData[$field])) {
                        $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . " is required for student at index $index";
                    }
                }

                // Validate F4 index number
                if (!empty($studentData['f4indexno']) && !$this->validator->isValidF4IndexNumber($studentData['f4indexno'])) {
                    $validationErrors[] = "Invalid F4 Index Number for student at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/bulkRegister', [
            'operation' => 'bulkRegisterPostgraduateStudents',
            'students_data' => $studentsData
        ], 'POST');
    }

    /**
     * Generate postgraduate programme report
     *
     * @param array $reportCriteria Report criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function generatePostgraduateProgrammeReport(array $reportCriteria): array
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

        if (isset($reportCriteria['programme_code']) && !empty($reportCriteria['programme_code']) && 
            !$this->validator->isValidProgrammeCode($reportCriteria['programme_code'])) {
            $validationErrors[] = 'Invalid programme code in report criteria';
        }

        if (isset($reportCriteria['report_type']) && !empty($reportCriteria['report_type'])) {
            $allowedTypes = ['summary', 'detailed', 'research_progress', 'thesis_submissions', 'completion', 'supervision'];
            if (!in_array($reportCriteria['report_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid report type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/postgraduate/generateReport', [
            'operation' => 'generatePostgraduateProgrammeReport',
            'report_criteria' => $reportCriteria
        ], 'POST');
    }
}