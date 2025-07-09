<?php

/**
 * TCU API Client - Non-Degree Resource
 *
 * This file contains the NonDegreeResource class which handles all non-degree-related
 * operations for the TCU API. It provides methods for interacting with non-degree
 * endpoints including certificate and diploma programs management.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles non-degree operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class NonDegreeResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.26 - Register Non-Degree Student
     *
     * Registers a new non-degree student in the TCU system.
     *
     * @param array $studentData Non-degree student registration data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerNonDegreeStudent(array $studentData): array
    {
        $validationErrors = [];

        // Validate required fields
        $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'programme_type'];
        
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

        // Validate programme type
        if (!empty($studentData['programme_type'])) {
            $allowedTypes = ['certificate', 'diploma', 'short_course', 'professional_course', 'continuing_education'];
            if (!in_array($studentData['programme_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid programme type';
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
            $allowedModes = ['full_time', 'part_time', 'evening', 'weekend', 'block_release', 'distance'];
            if (!in_array($studentData['study_mode'], $allowedModes)) {
                $validationErrors[] = 'Invalid study mode';
            }
        }

        // Validate duration if provided
        if (!empty($studentData['duration_months']) && (!is_numeric($studentData['duration_months']) || $studentData['duration_months'] < 1 || $studentData['duration_months'] > 60)) {
            $validationErrors[] = 'Invalid programme duration (must be 1-60 months)';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/register', [
            'operation' => 'registerNonDegreeStudent',
            'student_data' => $studentData
        ], 'POST');
    }

    /**
     * 3.27 - Get Non-Degree Student Details
     *
     * Retrieves detailed information about a non-degree student.
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getNonDegreeStudentDetails(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/getDetails', [
            'operation' => 'getNonDegreeStudentDetails',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * 3.28 - Update Non-Degree Student Information
     *
     * Updates non-degree student information in the system.
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $updateData Data to update
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateNonDegreeStudentInformation(string $f4indexno, array $updateData): array
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
            $allowedModes = ['full_time', 'part_time', 'evening', 'weekend', 'block_release', 'distance'];
            if (!in_array($updateData['study_mode'], $allowedModes)) {
                $validationErrors[] = 'Invalid study mode';
            }
        }

        if (isset($updateData['completion_status']) && !empty($updateData['completion_status'])) {
            $allowedStatuses = ['in_progress', 'completed', 'discontinued', 'deferred', 'failed'];
            if (!in_array($updateData['completion_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid completion status';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/updateInformation', [
            'operation' => 'updateNonDegreeStudentInformation',
            'f4indexno' => $f4indexno,
            'update_data' => $updateData
        ], 'POST');
    }

    /**
     * Get non-degree students by programme
     *
     * @param string $programmeCode Programme code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getNonDegreeStudentsByProgramme(string $programmeCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['programme_type']) && !empty($filters['programme_type'])) {
                $allowedTypes = ['certificate', 'diploma', 'short_course', 'professional_course', 'continuing_education'];
                if (!in_array($filters['programme_type'], $allowedTypes)) {
                    $validationErrors[] = 'Invalid programme type filter';
                }
            }

            if (isset($filters['study_mode']) && !empty($filters['study_mode'])) {
                $allowedModes = ['full_time', 'part_time', 'evening', 'weekend', 'block_release', 'distance'];
                if (!in_array($filters['study_mode'], $allowedModes)) {
                    $validationErrors[] = 'Invalid study mode filter';
                }
            }

            if (isset($filters['completion_status']) && !empty($filters['completion_status'])) {
                $allowedStatuses = ['in_progress', 'completed', 'discontinued', 'deferred', 'failed'];
                if (!in_array($filters['completion_status'], $allowedStatuses)) {
                    $validationErrors[] = 'Invalid completion status filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getNonDegreeStudentsByProgramme',
            'programme_code' => $programmeCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/nondegree/getByProgramme', $requestData, 'POST');
    }

    /**
     * Get non-degree students by institution
     *
     * @param string $institutionCode Institution code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getNonDegreeStudentsByInstitution(string $institutionCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['programme_type']) && !empty($filters['programme_type'])) {
                $allowedTypes = ['certificate', 'diploma', 'short_course', 'professional_course', 'continuing_education'];
                if (!in_array($filters['programme_type'], $allowedTypes)) {
                    $validationErrors[] = 'Invalid programme type filter';
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
            'operation' => 'getNonDegreeStudentsByInstitution',
            'institution_code' => $institutionCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/nondegree/getByInstitution', $requestData, 'POST');
    }

    /**
     * Search non-degree students
     *
     * @param array $searchCriteria Search criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function searchNonDegreeStudents(array $searchCriteria): array
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

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/search', [
            'operation' => 'searchNonDegreeStudents',
            'search_criteria' => $searchCriteria
        ], 'POST');
    }

    /**
     * Get non-degree programme statistics
     *
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getNonDegreeProgrammeStatistics(array $filters = []): array
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

            if (isset($filters['programme_type']) && !empty($filters['programme_type'])) {
                $allowedTypes = ['certificate', 'diploma', 'short_course', 'professional_course', 'continuing_education'];
                if (!in_array($filters['programme_type'], $allowedTypes)) {
                    $validationErrors[] = 'Invalid programme type filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getNonDegreeProgrammeStatistics'
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/nondegree/getStatistics', $requestData, 'POST');
    }

    /**
     * Issue non-degree certificate
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $certificateType Type of certificate
     * @param array $certificateData Additional certificate data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function issueNonDegreeCertificate(string $f4indexno, string $certificateType, array $certificateData = []): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate certificate type
        $allowedTypes = ['completion', 'attendance', 'proficiency', 'professional', 'competency'];
        if (!in_array($certificateType, $allowedTypes)) {
            $validationErrors[] = 'Invalid certificate type';
        }

        // Validate certificate data if provided
        if (!empty($certificateData)) {
            if (isset($certificateData['grade']) && !empty($certificateData['grade'])) {
                $allowedGrades = ['A', 'B', 'C', 'D', 'F', 'PASS', 'FAIL', 'DISTINCTION', 'CREDIT'];
                if (!in_array($certificateData['grade'], $allowedGrades)) {
                    $validationErrors[] = 'Invalid grade';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'issueNonDegreeCertificate',
            'f4indexno' => $f4indexno,
            'certificate_type' => $certificateType
        ];

        if (!empty($certificateData)) {
            $requestData['certificate_data'] = $certificateData;
        }

        return $this->client->makeRequest('/nondegree/issueCertificate', $requestData, 'POST');
    }

    /**
     * Update completion status
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $completionStatus New completion status
     * @param string $reason Reason for status change
     * @param array $additionalData Additional data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateCompletionStatus(string $f4indexno, string $completionStatus, string $reason, array $additionalData = []): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate completion status
        $allowedStatuses = ['in_progress', 'completed', 'discontinued', 'deferred', 'failed'];
        if (!in_array($completionStatus, $allowedStatuses)) {
            $validationErrors[] = 'Invalid completion status';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Status change reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'updateCompletionStatus',
            'f4indexno' => $f4indexno,
            'completion_status' => $completionStatus,
            'reason' => $reason
        ];

        if (!empty($additionalData)) {
            $requestData['additional_data'] = $additionalData;
        }

        return $this->client->makeRequest('/nondegree/updateCompletionStatus', $requestData, 'POST');
    }

    /**
     * Get completion history
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getCompletionHistory(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/getCompletionHistory', [
            'operation' => 'getCompletionHistory',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * Bulk register non-degree students
     *
     * @param array $studentsData Array of student data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkRegisterNonDegreeStudents(array $studentsData): array
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
                $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'programme_type'];
                
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

        return $this->client->makeRequest('/nondegree/bulkRegister', [
            'operation' => 'bulkRegisterNonDegreeStudents',
            'students_data' => $studentsData
        ], 'POST');
    }

    /**
     * Generate non-degree programme report
     *
     * @param array $reportCriteria Report criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function generateNonDegreeProgrammeReport(array $reportCriteria): array
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
            $allowedTypes = ['summary', 'detailed', 'completion', 'enrollment', 'performance'];
            if (!in_array($reportCriteria['report_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid report type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/nondegree/generateReport', [
            'operation' => 'generateNonDegreeProgrammeReport',
            'report_criteria' => $reportCriteria
        ], 'POST');
    }
}