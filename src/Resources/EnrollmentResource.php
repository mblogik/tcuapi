<?php

/**
 * TCU API Client - Enrollment Resource
 *
 * This file contains the EnrollmentResource class which handles all enrollment-related
 * operations for the TCU API. It provides methods for interacting with enrollment
 * endpoints including student enrollment, enrollment status, and enrollment management.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles student enrollment operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class EnrollmentResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * Enroll student in a programme
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $programmeCode Programme code
     * @param string $institutionCode Institution code
     * @param array $enrollmentData Additional enrollment data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function enrollStudent(string $f4indexno, string $programmeCode, string $institutionCode, array $enrollmentData = []): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate enrollment data if provided
        if (!empty($enrollmentData)) {
            if (isset($enrollmentData['academic_year']) && empty($enrollmentData['academic_year'])) {
                $validationErrors[] = 'Academic year cannot be empty';
            }

            if (isset($enrollmentData['semester']) && !in_array($enrollmentData['semester'], ['1', '2', '3'])) {
                $validationErrors[] = 'Invalid semester value';
            }

            if (isset($enrollmentData['study_mode']) && !in_array($enrollmentData['study_mode'], ['full_time', 'part_time', 'distance'])) {
                $validationErrors[] = 'Invalid study mode';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'enrollStudent',
            'f4indexno' => $f4indexno,
            'programme_code' => $programmeCode,
            'institution_code' => $institutionCode
        ];

        if (!empty($enrollmentData)) {
            $requestData['enrollment_data'] = $enrollmentData;
        }

        return $this->client->makeRequest('/enrollment/enrollStudent', $requestData, 'POST');
    }

    /**
     * Get enrollment status for a student
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getEnrollmentStatus(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/getEnrollmentStatus', [
            'operation' => 'getEnrollmentStatus',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * Update enrollment status
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $status New enrollment status
     * @param string $reason Reason for status change
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateEnrollmentStatus(string $f4indexno, string $status, string $reason): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate status
        $allowedStatuses = ['enrolled', 'deferred', 'withdrawn', 'suspended', 'graduated'];
        if (!in_array($status, $allowedStatuses)) {
            $validationErrors[] = 'Invalid enrollment status';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Status change reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/updateEnrollmentStatus', [
            'operation' => 'updateEnrollmentStatus',
            'f4indexno' => $f4indexno,
            'status' => $status,
            'reason' => $reason
        ], 'POST');
    }

    /**
     * Get enrollment statistics for a programme
     *
     * @param string $programmeCode Programme code
     * @param string|null $academicYear Optional academic year filter
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getEnrollmentStatistics(string $programmeCode, ?string $academicYear = null): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate academic year if provided
        if ($academicYear !== null && empty($academicYear)) {
            $validationErrors[] = 'Academic year cannot be empty';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getEnrollmentStatistics',
            'programme_code' => $programmeCode
        ];

        if ($academicYear !== null) {
            $requestData['academic_year'] = $academicYear;
        }

        return $this->client->makeRequest('/enrollment/getEnrollmentStatistics', $requestData, 'POST');
    }

    /**
     * Get enrolled students for a programme
     *
     * @param string $programmeCode Programme code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getEnrolledStudents(string $programmeCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['academic_year']) && empty($filters['academic_year'])) {
                $validationErrors[] = 'Academic year filter cannot be empty';
            }

            if (isset($filters['semester']) && !in_array($filters['semester'], ['1', '2', '3'])) {
                $validationErrors[] = 'Invalid semester filter value';
            }

            if (isset($filters['study_mode']) && !in_array($filters['study_mode'], ['full_time', 'part_time', 'distance'])) {
                $validationErrors[] = 'Invalid study mode filter';
            }

            if (isset($filters['status']) && !in_array($filters['status'], ['enrolled', 'deferred', 'withdrawn', 'suspended', 'graduated'])) {
                $validationErrors[] = 'Invalid enrollment status filter';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getEnrolledStudents',
            'programme_code' => $programmeCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/enrollment/getEnrolledStudents', $requestData, 'POST');
    }

    /**
     * Defer student enrollment
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $reason Reason for deferment
     * @param string $deferredUntil Deferred until date
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function deferEnrollment(string $f4indexno, string $reason, string $deferredUntil): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Deferment reason is required';
        }

        // Validate deferred until date
        if (empty($deferredUntil)) {
            $validationErrors[] = 'Deferred until date is required';
        } elseif (!$this->validator->isValidDate($deferredUntil)) {
            $validationErrors[] = 'Invalid deferred until date format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/deferEnrollment', [
            'operation' => 'deferEnrollment',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'deferred_until' => $deferredUntil
        ], 'POST');
    }

    /**
     * Withdraw student enrollment
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $reason Reason for withdrawal
     * @param string $withdrawalType Type of withdrawal
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function withdrawEnrollment(string $f4indexno, string $reason, string $withdrawalType = 'voluntary'): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Withdrawal reason is required';
        }

        // Validate withdrawal type
        $allowedTypes = ['voluntary', 'academic', 'disciplinary', 'financial'];
        if (!in_array($withdrawalType, $allowedTypes)) {
            $validationErrors[] = 'Invalid withdrawal type';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/withdrawEnrollment', [
            'operation' => 'withdrawEnrollment',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'withdrawal_type' => $withdrawalType
        ], 'POST');
    }

    /**
     * Reinstate student enrollment
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $reason Reason for reinstatement
     * @param array $reinstatementData Additional reinstatement data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function reinstateEnrollment(string $f4indexno, string $reason, array $reinstatementData = []): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Reinstatement reason is required';
        }

        // Validate reinstatement data if provided
        if (!empty($reinstatementData)) {
            if (isset($reinstatementData['effective_date']) && !$this->validator->isValidDate($reinstatementData['effective_date'])) {
                $validationErrors[] = 'Invalid effective date format';
            }

            if (isset($reinstatementData['conditions']) && !is_array($reinstatementData['conditions'])) {
                $validationErrors[] = 'Conditions must be an array';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'reinstateEnrollment',
            'f4indexno' => $f4indexno,
            'reason' => $reason
        ];

        if (!empty($reinstatementData)) {
            $requestData['reinstatement_data'] = $reinstatementData;
        }

        return $this->client->makeRequest('/enrollment/reinstateEnrollment', $requestData, 'POST');
    }

    /**
     * Get enrollment history for a student
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getEnrollmentHistory(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/getEnrollmentHistory', [
            'operation' => 'getEnrollmentHistory',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * Bulk enroll students
     *
     * @param array $enrollmentData Array of enrollment data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkEnrollStudents(array $enrollmentData): array
    {
        $validationErrors = [];

        // Validate enrollment data array
        if (empty($enrollmentData)) {
            $validationErrors[] = 'Enrollment data array cannot be empty';
        } else {
            foreach ($enrollmentData as $index => $enrollment) {
                if (!is_array($enrollment)) {
                    $validationErrors[] = "Enrollment data at index $index must be an array";
                    continue;
                }

                // Validate required fields
                if (empty($enrollment['f4indexno']) || !$this->validator->isValidF4IndexNumber($enrollment['f4indexno'])) {
                    $validationErrors[] = "Invalid F4 Index Number for enrollment at index $index";
                }

                if (empty($enrollment['programme_code']) || !$this->validator->isValidProgrammeCode($enrollment['programme_code'])) {
                    $validationErrors[] = "Invalid programme code for enrollment at index $index";
                }

                if (empty($enrollment['institution_code']) || !$this->validator->isValidInstitutionCode($enrollment['institution_code'])) {
                    $validationErrors[] = "Invalid institution code for enrollment at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/enrollment/bulkEnrollStudents', [
            'operation' => 'bulkEnrollStudents',
            'enrollment_data' => $enrollmentData
        ], 'POST');
    }
}