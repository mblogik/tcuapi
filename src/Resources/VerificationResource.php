<?php

/**
 * TCU API Client - Verification Resource
 *
 * This file contains the VerificationResource class which handles all verification-related
 * operations for the TCU API. It provides methods for interacting with verification
 * endpoints (3.16-3.20) including document verification, verification status, and
 * certificate validation.
 *
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Handles document verification and certificate validation operations
 *             for the TCU API with enterprise-level validation and error handling.
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Utils\ValidationHelper;

class VerificationResource extends BaseResource
{
    private ValidationHelper $validator;

    public function __construct(TCUAPIClient $client)
    {
        parent::__construct($client);
        $this->validator = new ValidationHelper();
    }

    /**
     * 3.16 - Verify Student Documents
     *
     * Verifies student documents for admission purposes.
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $documents Array of document information
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function verifyStudentDocuments(string $f4indexno, array $documents): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
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

                // Validate required document fields
                if (empty($document['document_type'])) {
                    $validationErrors[] = "Document type is required for document at index $index";
                }

                if (empty($document['document_number'])) {
                    $validationErrors[] = "Document number is required for document at index $index";
                }

                if (empty($document['document_data'])) {
                    $validationErrors[] = "Document data is required for document at index $index";
                }

                // Validate document type
                $allowedTypes = ['certificate', 'transcript', 'birth_certificate', 'passport', 'national_id'];
                if (!empty($document['document_type']) && !in_array($document['document_type'], $allowedTypes)) {
                    $validationErrors[] = "Invalid document type '{$document['document_type']}' for document at index $index";
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/verification/verifyStudentDocuments', [
            'operation' => 'verifyStudentDocuments',
            'f4indexno' => $f4indexno,
            'documents' => $documents
        ], 'POST');
    }

    /**
     * 3.17 - Get Verification Status
     *
     * Retrieves the verification status for a student.
     *
     * @param string $f4indexno Student's F4 index number
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getVerificationStatus(string $f4indexno): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/verification/getVerificationStatus', [
            'operation' => 'getVerificationStatus',
            'f4indexno' => $f4indexno
        ], 'POST');
    }

    /**
     * 3.18 - Validate Certificate
     *
     * Validates a certificate for authenticity.
     *
     * @param string $certificateNumber Certificate number to validate
     * @param string $certificateType Type of certificate
     * @param array $additionalData Additional validation data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function validateCertificate(string $certificateNumber, string $certificateType, array $additionalData = []): array
    {
        $validationErrors = [];

        // Validate certificate number
        if (empty(trim($certificateNumber))) {
            $validationErrors[] = 'Certificate number is required';
        }

        // Validate certificate type
        $allowedTypes = ['form_four', 'form_six', 'degree', 'diploma', 'certificate', 'masters', 'doctorate'];
        if (empty($certificateType) || !in_array($certificateType, $allowedTypes)) {
            $validationErrors[] = 'Invalid certificate type';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'validateCertificate',
            'certificate_number' => $certificateNumber,
            'certificate_type' => $certificateType
        ];

        if (!empty($additionalData)) {
            $requestData['additional_data'] = $additionalData;
        }

        return $this->client->makeRequest('/verification/validateCertificate', $requestData, 'POST');
    }

    /**
     * 3.19 - Submit Document Re-verification
     *
     * Submits documents for re-verification.
     *
     * @param string $f4indexno Student's F4 index number
     * @param array $documents Array of documents to re-verify
     * @param string $reason Reason for re-verification
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function submitDocumentReVerification(string $f4indexno, array $documents, string $reason): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
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

                if (empty($document['document_id'])) {
                    $validationErrors[] = "Document ID is required for document at index $index";
                }

                if (empty($document['document_type'])) {
                    $validationErrors[] = "Document type is required for document at index $index";
                }
            }
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Re-verification reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/verification/submitDocumentReVerification', [
            'operation' => 'submitDocumentReVerification',
            'f4indexno' => $f4indexno,
            'documents' => $documents,
            'reason' => $reason
        ], 'POST');
    }

    /**
     * 3.20 - Get Document Verification History
     *
     * Retrieves the verification history for a student's documents.
     *
     * @param string $f4indexno Student's F4 index number
     * @param string|null $documentType Optional document type filter
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getDocumentVerificationHistory(string $f4indexno, ?string $documentType = null): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate document type if provided
        if ($documentType !== null) {
            $allowedTypes = ['certificate', 'transcript', 'birth_certificate', 'passport', 'national_id'];
            if (!in_array($documentType, $allowedTypes)) {
                $validationErrors[] = 'Invalid document type';
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        $requestData = [
            'operation' => 'getDocumentVerificationHistory',
            'f4indexno' => $f4indexno
        ];

        if ($documentType !== null) {
            $requestData['document_type'] = $documentType;
        }

        return $this->client->makeRequest('/verification/getDocumentVerificationHistory', $requestData, 'POST');
    }

    /**
     * Convenience method to verify a single document
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $documentType Type of document
     * @param string $documentNumber Document number
     * @param string $documentData Document data
     * @param array $additionalData Additional document data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function verifySingleDocument(string $f4indexno, string $documentType, string $documentNumber, string $documentData, array $additionalData = []): array
    {
        $document = [
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'document_data' => $documentData
        ];

        if (!empty($additionalData)) {
            $document['additional_data'] = $additionalData;
        }

        return $this->verifyStudentDocuments($f4indexno, [$document]);
    }

    /**
     * Convenience method to re-verify a single document
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $documentId Document ID
     * @param string $documentType Document type
     * @param string $reason Reason for re-verification
     * @param array $additionalData Additional document data
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function reVerifySingleDocument(string $f4indexno, string $documentId, string $documentType, string $reason, array $additionalData = []): array
    {
        $document = [
            'document_id' => $documentId,
            'document_type' => $documentType
        ];

        if (!empty($additionalData)) {
            $document['additional_data'] = $additionalData;
        }

        return $this->submitDocumentReVerification($f4indexno, [$document], $reason);
    }

    /**
     * Get verification statistics for a programme
     *
     * @param string $programmeCode Programme code
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function getVerificationStatistics(string $programmeCode): array
    {
        $validationErrors = [];

        // Validate programme code
        if (!$this->validator->isValidProgrammeCode($programmeCode)) {
            $validationErrors[] = 'Invalid programme code format';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/verification/getVerificationStatistics', [
            'operation' => 'getVerificationStatistics',
            'programme_code' => $programmeCode
        ], 'POST');
    }

    /**
     * Update verification status
     *
     * @param string $f4indexno Student's F4 index number
     * @param string $status New verification status
     * @param string $reason Reason for status change
     * @return array API response
     * @throws ValidationException If validation fails
     */
    public function updateVerificationStatus(string $f4indexno, string $status, string $reason): array
    {
        $validationErrors = [];

        // Validate F4 index number
        if (!$this->validator->isValidF4IndexNumber($f4indexno)) {
            $validationErrors[] = 'Invalid F4 Index Number format';
        }

        // Validate status
        $allowedStatuses = ['pending', 'verified', 'rejected', 'requires_resubmission'];
        if (!in_array($status, $allowedStatuses)) {
            $validationErrors[] = 'Invalid verification status';
        }

        // Validate reason
        if (empty(trim($reason))) {
            $validationErrors[] = 'Status change reason is required';
        }

        if (!empty($validationErrors)) {
            throw new ValidationException('Validation failed', $validationErrors);
        }

        return $this->client->makeRequest('/verification/updateVerificationStatus', [
            'operation' => 'updateVerificationStatus',
            'f4indexno' => $f4indexno,
            'status' => $status,
            'reason' => $reason
        ], 'POST');
    }
}