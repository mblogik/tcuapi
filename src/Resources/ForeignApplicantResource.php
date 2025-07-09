<?php

/**
 * TCU API Client - Foreign Applicant Resource
 *
 * This file contains the ForeignApplicantResource class which handles all foreign applicant-related
 * operations for the TCU API. It provides methods for interacting with foreign applicant
 * endpoints including registration, visa processing, and international student management.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles foreign applicant operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class ForeignApplicantResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.31 - Register Foreign Applicant
     *
     * Registers a new foreign applicant in the TCU system.
     *
     * @param array $applicantData Foreign applicant registration data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerForeignApplicant(array $applicantData): array
    {
        $validationErrors = [];

        // Validate required fields
        $requiredFields = ['passport_number', 'firstname', 'surname', 'nationality', 'country_of_origin', 'programme_code', 'institution_code'];
        
        foreach ($requiredFields as $field) {
            if (empty($applicantData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate passport number
        if (!empty($applicantData['passport_number']) && !preg_match('/^[A-Z0-9]{6,20}$/', $applicantData['passport_number'])) {
            $validationErrors[] = 'Invalid passport number format';
        }

        // Validate programme code
        if (!empty($applicantData['programme_code']) && !$this->validator->isValidProgrammeCode($applicantData['programme_code'])) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate institution code
        if (!empty($applicantData['institution_code']) && !$this->validator->isValidInstitutionCode($applicantData['institution_code'])) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate email if provided
        if (!empty($applicantData['email']) && !$this->validator->isValidEmail($applicantData['email'])) {
            $validationErrors[] = 'Invalid email format';
        }

        // Validate phone number if provided
        if (!empty($applicantData['phone']) && !$this->validator->isValidPhoneNumber($applicantData['phone'])) {
            $validationErrors[] = 'Invalid phone number format';
        }

        // Validate nationality
        if (!empty($applicantData['nationality']) && !preg_match('/^[A-Z]{2,3}$/', $applicantData['nationality'])) {
            $validationErrors[] = 'Invalid nationality format (use ISO country code)';
        }

        // Validate country of origin
        if (!empty($applicantData['country_of_origin']) && !preg_match('/^[A-Z]{2,3}$/', $applicantData['country_of_origin'])) {
            $validationErrors[] = 'Invalid country of origin format (use ISO country code)';
        }

        // Validate visa status if provided
        if (!empty($applicantData['visa_status'])) {
            $allowedStatuses = ['pending', 'approved', 'rejected', 'expired', 'not_required'];
            if (!in_array($applicantData['visa_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid visa status';
            }
        }

        // Validate study level if provided
        if (!empty($applicantData['study_level'])) {
            $allowedLevels = ['undergraduate', 'postgraduate', 'masters', 'doctorate', 'certificate', 'diploma'];
            if (!in_array($applicantData['study_level'], $allowedLevels)) {
                $validationErrors[] = 'Invalid study level';
            }
        }

        // Validate funding source if provided
        if (!empty($applicantData['funding_source'])) {
            $allowedSources = ['government', 'private', 'scholarship', 'self_sponsored', 'sponsor', 'international_organization'];
            if (!in_array($applicantData['funding_source'], $allowedSources)) {
                $validationErrors[] = 'Invalid funding source';
            }
        }

        // Validate passport expiry date if provided
        if (!empty($applicantData['passport_expiry_date']) && !$this->validator->isValidDate($applicantData['passport_expiry_date'])) {
            $validationErrors[] = 'Invalid passport expiry date format';
        }

        // Validate date of birth if provided
        if (!empty($applicantData['date_of_birth']) && !$this->validator->isValidDate($applicantData['date_of_birth'])) {
            $validationErrors[] = 'Invalid date of birth format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/register', [
            'operation' => 'registerForeignApplicant',
            'applicant_data' => $applicantData
        ], 'POST');
    }

    /**
     * 3.32 - Get Foreign Applicant Details
     *
     * Retrieves detailed information about a foreign applicant.
     *
     * @param string $passportNumber Applicant's passport number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getForeignApplicantDetails(string $passportNumber): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/getDetails', [
            'operation' => 'getForeignApplicantDetails',
            'passport_number' => $passportNumber
        ], 'POST');
    }

    /**
     * 3.33 - Update Foreign Applicant Information
     *
     * Updates foreign applicant information in the system.
     *
     * @param string $passportNumber Applicant's passport number
     * @param array $updateData Data to update
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateForeignApplicantInformation(string $passportNumber, array $updateData): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
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

        if (isset($updateData['visa_status']) && !empty($updateData['visa_status'])) {
            $allowedStatuses = ['pending', 'approved', 'rejected', 'expired', 'not_required'];
            if (!in_array($updateData['visa_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid visa status';
            }
        }

        if (isset($updateData['application_status']) && !empty($updateData['application_status'])) {
            $allowedStatuses = ['pending', 'approved', 'rejected', 'under_review', 'conditional_offer', 'enrolled'];
            if (!in_array($updateData['application_status'], $allowedStatuses)) {
                $validationErrors[] = 'Invalid application status';
            }
        }

        if (isset($updateData['passport_expiry_date']) && !empty($updateData['passport_expiry_date']) && 
            !$this->validator->isValidDate($updateData['passport_expiry_date'])) {
            $validationErrors[] = 'Invalid passport expiry date format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/updateInformation', [
            'operation' => 'updateForeignApplicantInformation',
            'passport_number' => $passportNumber,
            'update_data' => $updateData
        ], 'POST');
    }

    /**
     * 3.34 - Process Visa Application
     *
     * Processes visa application for foreign applicants.
     *
     * @param string $passportNumber Applicant's passport number
     * @param array $visaData Visa application data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function processVisaApplication(string $passportNumber, array $visaData): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        // Validate required visa fields
        $requiredFields = ['visa_type', 'intended_arrival_date', 'intended_departure_date'];
        
        foreach ($requiredFields as $field) {
            if (empty($visaData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate visa type
        if (!empty($visaData['visa_type'])) {
            $allowedTypes = ['student', 'visitor', 'business', 'transit', 'multiple_entry'];
            if (!in_array($visaData['visa_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid visa type';
            }
        }

        // Validate dates
        if (!empty($visaData['intended_arrival_date']) && !$this->validator->isValidDate($visaData['intended_arrival_date'])) {
            $validationErrors[] = 'Invalid intended arrival date format';
        }

        if (!empty($visaData['intended_departure_date']) && !$this->validator->isValidDate($visaData['intended_departure_date'])) {
            $validationErrors[] = 'Invalid intended departure date format';
        }

        // Validate that departure date is after arrival date
        if (!empty($visaData['intended_arrival_date']) && !empty($visaData['intended_departure_date'])) {
            $arrivalDate = new \DateTime($visaData['intended_arrival_date']);
            $departureDate = new \DateTime($visaData['intended_departure_date']);
            
            if ($departureDate <= $arrivalDate) {
                $validationErrors[] = 'Intended departure date must be after intended arrival date';
            }
        }

        // Validate duration of stay if provided
        if (!empty($visaData['duration_of_stay_days']) && 
            (!is_numeric($visaData['duration_of_stay_days']) || $visaData['duration_of_stay_days'] < 1 || $visaData['duration_of_stay_days'] > 1825)) {
            $validationErrors[] = 'Invalid duration of stay (must be 1-1825 days)';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/processVisaApplication', [
            'operation' => 'processVisaApplication',
            'passport_number' => $passportNumber,
            'visa_data' => $visaData
        ], 'POST');
    }

    /**
     * 3.35 - Get Visa Status
     *
     * Retrieves visa status for a foreign applicant.
     *
     * @param string $passportNumber Applicant's passport number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getVisaStatus(string $passportNumber): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/getVisaStatus', [
            'operation' => 'getVisaStatus',
            'passport_number' => $passportNumber
        ], 'POST');
    }

    /**
     * Get foreign applicants by programme
     *
     * @param string $programmeCode Programme code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getForeignApplicantsByProgramme(string $programmeCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['nationality']) && !empty($filters['nationality']) && 
                !preg_match('/^[A-Z]{2,3}$/', $filters['nationality'])) {
                $validationErrors[] = 'Invalid nationality filter format';
            }

            if (isset($filters['visa_status']) && !empty($filters['visa_status'])) {
                $allowedStatuses = ['pending', 'approved', 'rejected', 'expired', 'not_required'];
                if (!in_array($filters['visa_status'], $allowedStatuses)) {
                    $validationErrors[] = 'Invalid visa status filter';
                }
            }

            if (isset($filters['application_status']) && !empty($filters['application_status'])) {
                $allowedStatuses = ['pending', 'approved', 'rejected', 'under_review', 'conditional_offer', 'enrolled'];
                if (!in_array($filters['application_status'], $allowedStatuses)) {
                    $validationErrors[] = 'Invalid application status filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getForeignApplicantsByProgramme',
            'programme_code' => $programmeCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/foreign/getByProgramme', $requestData, 'POST');
    }

    /**
     * Get foreign applicants by institution
     *
     * @param string $institutionCode Institution code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getForeignApplicantsByInstitution(string $institutionCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['nationality']) && !empty($filters['nationality']) && 
                !preg_match('/^[A-Z]{2,3}$/', $filters['nationality'])) {
                $validationErrors[] = 'Invalid nationality filter format';
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
            'operation' => 'getForeignApplicantsByInstitution',
            'institution_code' => $institutionCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/foreign/getByInstitution', $requestData, 'POST');
    }

    /**
     * Search foreign applicants
     *
     * @param array $searchCriteria Search criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function searchForeignApplicants(array $searchCriteria): array
    {
        $validationErrors = [];

        // Validate search criteria is not empty
        if (empty($searchCriteria)) {
            $validationErrors[] = 'Search criteria cannot be empty';
        }

        // Validate specific search fields if provided
        if (isset($searchCriteria['passport_number']) && !empty($searchCriteria['passport_number']) && 
            !preg_match('/^[A-Z0-9]{6,20}$/', $searchCriteria['passport_number'])) {
            $validationErrors[] = 'Invalid passport number in search criteria';
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

        if (isset($searchCriteria['nationality']) && !empty($searchCriteria['nationality']) && 
            !preg_match('/^[A-Z]{2,3}$/', $searchCriteria['nationality'])) {
            $validationErrors[] = 'Invalid nationality in search criteria';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/search', [
            'operation' => 'searchForeignApplicants',
            'search_criteria' => $searchCriteria
        ], 'POST');
    }

    /**
     * Get foreign applicant statistics
     *
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getForeignApplicantStatistics(array $filters = []): array
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

            if (isset($filters['nationality']) && !empty($filters['nationality']) && 
                !preg_match('/^[A-Z]{2,3}$/', $filters['nationality'])) {
                $validationErrors[] = 'Invalid nationality filter';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getForeignApplicantStatistics'
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/foreign/getStatistics', $requestData, 'POST');
    }

    /**
     * Update application status
     *
     * @param string $passportNumber Applicant's passport number
     * @param string $applicationStatus New application status
     * @param string $reason Reason for status change
     * @param array $additionalData Additional data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateApplicationStatus(string $passportNumber, string $applicationStatus, string $reason, array $additionalData = []): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        // Validate application status
        $allowedStatuses = ['pending', 'approved', 'rejected', 'under_review', 'conditional_offer', 'enrolled'];
        if (!in_array($applicationStatus, $allowedStatuses)) {
            $validationErrors[] = 'Invalid application status';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Status change reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'updateApplicationStatus',
            'passport_number' => $passportNumber,
            'application_status' => $applicationStatus,
            'reason' => $reason
        ];

        if (!empty($additionalData)) {
            $requestData['additional_data'] = $additionalData;
        }

        return $this->client->makeRequest('/foreign/updateApplicationStatus', $requestData, 'POST');
    }

    /**
     * Get application history
     *
     * @param string $passportNumber Applicant's passport number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getApplicationHistory(string $passportNumber): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/getApplicationHistory', [
            'operation' => 'getApplicationHistory',
            'passport_number' => $passportNumber
        ], 'POST');
    }

    /**
     * Submit document verification
     *
     * @param string $passportNumber Applicant's passport number
     * @param array $documents Array of documents to verify
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function submitDocumentVerification(string $passportNumber, array $documents): array
    {
        $validationErrors = [];

        // Validate passport number
        if (!preg_match('/^[A-Z0-9]{6,20}$/', $passportNumber)) {
            $validationErrors[] = 'Invalid passport number format';
        }

        // Validate documents array
        if (empty($documents)) {
            $validationErrors[] = 'Documents array cannot be empty';
        } else {
            foreach ($documents as $index => $document) {
                if (!is_array($document)) {
                    $validationErrors[] = "Document at index $index must be an array";
                    continue;
                }

                if (empty($document['document_type'])) {
                    $validationErrors[] = "Document type is required for document at index $index";
                }

                if (empty($document['document_number'])) {
                    $validationErrors[] = "Document number is required for document at index $index";
                }

                // Validate document type
                $allowedTypes = ['passport', 'academic_transcript', 'degree_certificate', 'language_certificate', 'financial_statement', 'medical_certificate'];
                if (!empty($document['document_type']) && !in_array($document['document_type'], $allowedTypes)) {
                    $validationErrors[] = "Invalid document type for document at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/submitDocumentVerification', [
            'operation' => 'submitDocumentVerification',
            'passport_number' => $passportNumber,
            'documents' => $documents
        ], 'POST');
    }

    /**
     * Bulk register foreign applicants
     *
     * @param array $applicantsData Array of applicant data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkRegisterForeignApplicants(array $applicantsData): array
    {
        $validationErrors = [];

        // Validate applicants data array
        if (empty($applicantsData)) {
            $validationErrors[] = 'Applicants data array cannot be empty';
        } else {
            foreach ($applicantsData as $index => $applicantData) {
                if (!is_array($applicantData)) {
                    $validationErrors[] = "Applicant data at index $index must be an array";
                    continue;
                }

                // Validate required fields for each applicant
                $requiredFields = ['passport_number', 'firstname', 'surname', 'nationality', 'country_of_origin', 'programme_code', 'institution_code'];
                
                foreach ($requiredFields as $field) {
                    if (empty($applicantData[$field])) {
                        $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . " is required for applicant at index $index";
                    }
                }

                // Validate passport number
                if (!empty($applicantData['passport_number']) && !preg_match('/^[A-Z0-9]{6,20}$/', $applicantData['passport_number'])) {
                    $validationErrors[] = "Invalid passport number for applicant at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/bulkRegister', [
            'operation' => 'bulkRegisterForeignApplicants',
            'applicants_data' => $applicantsData
        ], 'POST');
    }

    /**
     * Generate foreign applicant report
     *
     * @param array $reportCriteria Report criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function generateForeignApplicantReport(array $reportCriteria): array
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
            $allowedTypes = ['summary', 'detailed', 'visa_status', 'application_status', 'nationality', 'programme_enrollment'];
            if (!in_array($reportCriteria['report_type'], $allowedTypes)) {
                $validationErrors[] = 'Invalid report type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/generateReport', [
            'operation' => 'generateForeignApplicantReport',
            'report_criteria' => $reportCriteria
        ], 'POST');
    }

    /**
     * Get visa requirements
     *
     * @param string $nationality Applicant's nationality
     * @param string $studyLevel Study level
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getVisaRequirements(string $nationality, string $studyLevel): array
    {
        $validationErrors = [];

        // Validate nationality
        if (!preg_match('/^[A-Z]{2,3}$/', $nationality)) {
            $validationErrors[] = 'Invalid nationality format (use ISO country code)';
        }

        // Validate study level
        $allowedLevels = ['undergraduate', 'postgraduate', 'masters', 'doctorate', 'certificate', 'diploma'];
        if (!in_array($studyLevel, $allowedLevels)) {
            $validationErrors[] = 'Invalid study level';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/foreign/getVisaRequirements', [
            'operation' => 'getVisaRequirements',
            'nationality' => $nationality,
            'study_level' => $studyLevel
        ], 'POST');
    }
}