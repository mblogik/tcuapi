<?php

/**
 * TCU API Client - Check Status Response Model
 * 
 * Response model for applicant status check operations from the TCU API.
 * Provides structured access to applicant status information including
 * admission details, eligibility status, and recommendation data.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class CheckStatusTcuResponse
{
    private string $f4IndexNo;
    private int $statusCode;
    private string $statusDescription;
    private bool $isEligible;
    private bool $hasAdmission;
    private string $admissionStatus;
    private string $institutionCode;
    private string $programmeCode;
    private bool $isConfirmed;
    private array $rawData;

    /**
     * Constructor
     * 
     * @param array $data Response data from TCU API
     */
    public function __construct(array $data)
    {
        $this->rawData = $data;
        $this->f4IndexNo = trim($data['f4indexno'] ?? '');
        $this->statusCode = (int)($data['StatusCode'] ?? 0);
        $this->statusDescription = trim($data['StatusDescription'] ?? '');
        
        // Parse status information
        $this->parseStatusInformation($data);
    }

    /**
     * Parse status information from response data
     * 
     * @param array $data
     */
    private function parseStatusInformation(array $data): void
    {
        $description = strtolower($this->statusDescription);
        
        // Determine eligibility
        $this->isEligible = $this->statusCode === 200 || 
                           stripos($description, 'eligible') !== false ||
                           stripos($description, 'admitted') !== false;
        
        // Check for admission
        $this->hasAdmission = stripos($description, 'admitted') !== false ||
                             stripos($description, 'admission') !== false;
        
        // Extract admission status
        $this->admissionStatus = $data['AdmissionStatus'] ?? '';
        if (empty($this->admissionStatus) && $this->hasAdmission) {
            if (stripos($description, 'provisional') !== false) {
                $this->admissionStatus = 'Provisional admission';
            } elseif (stripos($description, 'confirmed') !== false) {
                $this->admissionStatus = 'Confirmed admission';
            } else {
                $this->admissionStatus = 'Admitted';
            }
        }
        
        // Extract institution and programme codes
        $this->institutionCode = trim($data['InstitutionCode'] ?? '');
        $this->programmeCode = trim($data['ProgrammeCode'] ?? '');
        
        // Check confirmation status
        $this->isConfirmed = stripos($this->admissionStatus, 'confirmed') !== false ||
                            stripos($description, 'confirmed') !== false;
    }

    /**
     * Get Form 4 index number
     * 
     * @return string
     */
    public function getF4IndexNo(): string
    {
        return $this->f4IndexNo;
    }

    /**
     * Get status code
     * 
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get status description
     * 
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->statusDescription;
    }

    /**
     * Check if the request was successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode === 200;
    }

    /**
     * Check if applicant is eligible
     * 
     * @return bool
     */
    public function isEligible(): bool
    {
        return $this->isEligible;
    }

    /**
     * Check if applicant has admission
     * 
     * @return bool
     */
    public function hasAdmission(): bool
    {
        return $this->hasAdmission;
    }

    /**
     * Get admission status
     * 
     * @return string
     */
    public function getAdmissionStatus(): string
    {
        return $this->admissionStatus;
    }

    /**
     * Get institution code
     * 
     * @return string
     */
    public function getInstitutionCode(): string
    {
        return $this->institutionCode;
    }

    /**
     * Get programme code
     * 
     * @return string
     */
    public function getProgrammeCode(): string
    {
        return $this->programmeCode;
    }

    /**
     * Check if admission is confirmed
     * 
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * Check if admission is provisional
     * 
     * @return bool
     */
    public function isProvisional(): bool
    {
        return !$this->isConfirmed && $this->hasAdmission;
    }

    /**
     * Check if applicant can apply (eligible but no admission)
     * 
     * @return bool
     */
    public function canApply(): bool
    {
        return $this->isEligible && !$this->hasAdmission;
    }

    /**
     * Get status summary
     * 
     * @return array
     */
    public function getStatusSummary(): array
    {
        return [
            'f4IndexNo' => $this->f4IndexNo,
            'isSuccess' => $this->isSuccess(),
            'isEligible' => $this->isEligible(),
            'hasAdmission' => $this->hasAdmission(),
            'canApply' => $this->canApply(),
            'admissionStatus' => $this->admissionStatus,
            'isConfirmed' => $this->isConfirmed(),
            'isProvisional' => $this->isProvisional(),
            'institutionCode' => $this->institutionCode,
            'programmeCode' => $this->programmeCode,
            'statusDescription' => $this->statusDescription
        ];
    }

    /**
     * Get the applicant's current state description
     * 
     * @return string
     */
    public function getCurrentState(): string
    {
        if (!$this->isSuccess()) {
            return 'Request failed: ' . $this->statusDescription;
        }
        
        if (!$this->isEligible()) {
            return 'Not eligible for admission';
        }
        
        if ($this->hasAdmission()) {
            if ($this->isConfirmed()) {
                return 'Confirmed admission to ' . $this->programmeCode;
            } else {
                return 'Provisional admission to ' . $this->programmeCode;
            }
        }
        
        return 'Eligible for application';
    }

    /**
     * Get raw response data
     * 
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return $this->getStatusSummary();
    }

    /**
     * Convert to JSON string
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * String representation
     * 
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            "%s: %s",
            $this->f4IndexNo,
            $this->getCurrentState()
        );
    }
}