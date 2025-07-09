<?php

/**
 * TCU API Client - Graduate Resource
 *
 * This file contains the GraduateResource class which handles all graduate-related
 * operations for the TCU API. It provides methods for interacting with graduate
 * endpoints including graduate registration, verification, and management.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles graduate operations for the TCU API with
 *             enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class GraduateResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.21 - Register Graduate
     *
     * Registers a new graduate in the TCU system.
     *
     * @param array $graduateData Graduate registration data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function registerGraduate(array $graduateData): array
    {
        $validationErrors = [];

        // Validate required fields
        $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'graduation_year'];
        
        foreach ($requiredFields as $field) {
            if (empty($graduateData[$field])) {
                $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate F4 index number
        if (!empty($graduateData['f4indexno']) && !$this->validator->isValidF4IndexNumber($graduateData['f4indexno'])) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate programme code
        if (!empty($graduateData['programme_code']) && !$this->validator->isValidProgrammeCode($graduateData['programme_code'])) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate institution code
        if (!empty($graduateData['institution_code']) && !$this->validator->isValidInstitutionCode($graduateData['institution_code'])) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate email if provided
        if (!empty($graduateData['email']) && !$this->validator->isValidEmail($graduateData['email'])) {
            $validationErrors[] = 'Invalid email format';
        }

        // Validate phone number if provided
        if (!empty($graduateData['phone']) && !$this->validator->isValidPhoneNumber($graduateData['phone'])) {
            $validationErrors[] = 'Invalid phone number format';
        }

        // Validate graduation year
        if (!empty($graduateData['graduation_year'])) {
            $currentYear = date('Y');
            if (!is_numeric($graduateData['graduation_year']) || 
                $graduateData['graduation_year'] < 1990 || 
                $graduateData['graduation_year'] > $currentYear + 1) {
                $validationErrors[] = 'Invalid graduation year';
            }
        }

        // Validate degree classification if provided
        if (!empty($graduateData['degree_classification'])) {
            $allowedClassifications = ['first_class', 'upper_second', 'lower_second', 'third_class', 'pass', 'distinction', 'credit'];
            if (!in_array($graduateData['degree_classification'], $allowedClassifications)) {
                $validationErrors[] = 'Invalid degree classification';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/register', [
            'operation' => 'registerGraduate',
            'graduate_data' => $graduateData
        ], 'POST');
    }

    /**
     * 3.22 - Get Graduate Details
     *
     * Retrieves detailed information about a graduate.
     *
     * @param string $f4indexno Graduate's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getGraduateDetails(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/getDetails', [
            'operation' => 'getGraduateDetails',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * 3.23 - Update Graduate Information
     *
     * Updates graduate information in the system.
     *
     * @param string $f4indexno Graduate's F4 index number
     * @param array $updateData Data to update
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateGraduateInformation(string $f4indexno, array $updateData): array
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

        if (isset($updateData['degree_classification']) && !empty($updateData['degree_classification'])) {
            $allowedClassifications = ['first_class', 'upper_second', 'lower_second', 'third_class', 'pass', 'distinction', 'credit'];
            if (!in_array($updateData['degree_classification'], $allowedClassifications)) {
                $validationErrors[] = 'Invalid degree classification';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/updateInformation', [
            'operation' => 'updateGraduateInformation',
            'f4indexno' => $f4indexno,
            'update_data' => $updateData
        ], 'POST');
    }

    /**
     * Get graduates by programme
     *
     * @param string $programmeCode Programme code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getGraduatesByProgramme(string $programmeCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['graduation_year']) && !empty($filters['graduation_year'])) {
                $currentYear = date('Y');
                if (!is_numeric($filters['graduation_year']) || 
                    $filters['graduation_year'] < 1990 || 
                    $filters['graduation_year'] > $currentYear + 1) {
                    $validationErrors[] = 'Invalid graduation year filter';
                }
            }

            if (isset($filters['degree_classification']) && !empty($filters['degree_classification'])) {
                $allowedClassifications = ['first_class', 'upper_second', 'lower_second', 'third_class', 'pass', 'distinction', 'credit'];
                if (!in_array($filters['degree_classification'], $allowedClassifications)) {
                    $validationErrors[] = 'Invalid degree classification filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getGraduatesByProgramme',
            'programme_code' => $programmeCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/graduates/getByProgramme', $requestData, 'POST');
    }

    /**
     * Get graduates by institution
     *
     * @param string $institutionCode Institution code
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getGraduatesByInstitution(string $institutionCode, array $filters = []): array
    {
        $validationErrors = [];

        // Validate institution code
        if (!$this->validator->isValidInstitutionCode($institutionCode)) {
            $validationErrors[] = 'Invalid institution code format';
        }

        // Validate filters if provided
        if (!empty($filters)) {
            if (isset($filters['graduation_year']) && !empty($filters['graduation_year'])) {
                $currentYear = date('Y');
                if (!is_numeric($filters['graduation_year']) || 
                    $filters['graduation_year'] < 1990 || 
                    $filters['graduation_year'] > $currentYear + 1) {
                    $validationErrors[] = 'Invalid graduation year filter';
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
            'operation' => 'getGraduatesByInstitution',
            'institution_code' => $institutionCode
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/graduates/getByInstitution', $requestData, 'POST');
    }

    /**
     * Search graduates
     *
     * @param array $searchCriteria Search criteria
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function searchGraduates(array $searchCriteria): array
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

        return $this->client->makeRequest('/graduates/search', [
            'operation' => 'searchGraduates',
            'search_criteria' => $searchCriteria
        ], 'POST');
    }

    /**
     * Get graduate statistics
     *
     * @param array $filters Optional filters
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getGraduateStatistics(array $filters = []): array
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

            if (isset($filters['graduation_year']) && !empty($filters['graduation_year'])) {
                $currentYear = date('Y');
                if (!is_numeric($filters['graduation_year']) || 
                    $filters['graduation_year'] < 1990 || 
                    $filters['graduation_year'] > $currentYear + 1) {
                    $validationErrors[] = 'Invalid graduation year filter';
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getGraduateStatistics'
        ];

        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }

        return $this->client->makeRequest('/graduates/getStatistics', $requestData, 'POST');
    }

    /**
     * Verify graduate credentials
     *
     * @param string $f4indexno Graduate's F4 index number
     * @param array $credentialsData Credentials data to verify
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function verifyGraduateCredentials(string $f4indexno, array $credentialsData): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate credentials data
        if (empty($credentialsData)) {
            $validationErrors[] = 'Credentials data cannot be empty';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/verifyCredentials', [
            'operation' => 'verifyGraduateCredentials',
            'f4indexno' => $f4indexno,
            'credentials_data' => $credentialsData
        ], 'POST');
    }

    /**
     * Generate graduate certificate
     *
     * @param string $f4indexno Graduate's F4 index number
     * @param string $certificateType Type of certificate to generate
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function generateGraduateCertificate(string $f4indexno, string $certificateType = 'completion'): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate certificate type
        $allowedTypes = ['completion', 'transcript', 'verification', 'degree'];
        if (!in_array($certificateType, $allowedTypes)) {
            $validationErrors[] = 'Invalid certificate type';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/generateCertificate', [
            'operation' => 'generateGraduateCertificate',
            'f4indexno' => $f4indexno,
            'certificate_type' => $certificateType
        ], 'POST');
    }

    /**
     * Bulk register graduates
     *
     * @param array $graduatesData Array of graduate data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function bulkRegisterGraduates(array $graduatesData): array
    {
        $validationErrors = [];

        // Validate graduates data array
        if (empty($graduatesData)) {
            $validationErrors[] = 'Graduates data array cannot be empty';
        } else {
            foreach ($graduatesData as $index => $graduateData) {
                if (!is_array($graduateData)) {
                    $validationErrors[] = "Graduate data at index $index must be an array";
                    continue;
                }

                // Validate required fields for each graduate
                $requiredFields = ['f4indexno', 'firstname', 'surname', 'programme_code', 'institution_code', 'graduation_year'];
                
                foreach ($requiredFields as $field) {
                    if (empty($graduateData[$field])) {
                        $validationErrors[] = ucfirst(str_replace('_', ' ', $field)) . " is required for graduate at index $index";
                    }
                }

                // Validate F4 index number
                if (!empty($graduateData['f4indexno']) && !$this->validator->isValidF4IndexNumber($graduateData['f4indexno'])) {
                    $validationErrors[] = "Invalid F4 Index Number for graduate at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/graduates/bulkRegister', [
            'operation' => 'bulkRegisterGraduates',
            'graduates_data' => $graduatesData
        ], 'POST');
    }
}