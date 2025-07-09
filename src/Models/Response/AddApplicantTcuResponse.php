<?php

/**
 * TCU API Client - Add Applicant Response Model
 * 
 * Response model for add applicant operations from the TCU API.
 * Provides structured access to applicant addition results including
 * status information, validation results, and operation success details.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class AddApplicantTcuResponse
{
    private string $f4IndexNo;
    private int $statusCode;
    private string $statusDescription;
    private bool $isSuccessfullyAdded;
    private bool $isDuplicate;
    private string $applicantId;
    private array $warnings;
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
        
        // Parse operation results
        $this->parseOperationResults($data);
    }

    /**
     * Parse operation results from response data
     * 
     * @param array $data
     */
    private function parseOperationResults(array $data): void
    {
        $description = strtolower($this->statusDescription);
        
        // Determine if successfully added
        $this->isSuccessfullyAdded = $this->statusCode === 200 || 
                                    stripos($description, 'successful') !== false ||
                                    stripos($description, 'added') !== false ||
                                    stripos($description, 'created') !== false;
        
        // Check for duplicate
        $this->isDuplicate = stripos($description, 'duplicate') !== false ||
                            stripos($description, 'already exists') !== false ||
                            stripos($description, 'exists') !== false;
        
        // Extract applicant ID if provided
        $this->applicantId = trim($data['ApplicantId'] ?? $data['applicant_id'] ?? '');
        
        // Extract warnings
        $this->warnings = [];
        if (isset($data['Warnings']) && is_array($data['Warnings'])) {
            $this->warnings = $data['Warnings'];
        } elseif (isset($data['warnings']) && is_string($data['warnings'])) {
            $this->warnings = explode(',', $data['warnings']);
        }
        
        // Add status-based warnings
        if ($this->isDuplicate) {
            $this->warnings[] = 'Applicant already exists in the system';
        }
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
     * Check if applicant was successfully added
     * 
     * @return bool
     */
    public function isSuccessfullyAdded(): bool
    {
        return $this->isSuccessfullyAdded;
    }

    /**
     * Check if applicant is a duplicate
     * 
     * @return bool
     */
    public function isDuplicate(): bool
    {
        return $this->isDuplicate;
    }

    /**
     * Get applicant ID (if provided by API)
     * 
     * @return string
     */
    public function getApplicantId(): string
    {
        return $this->applicantId;
    }

    /**
     * Check if there are warnings
     * 
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    /**
     * Get warnings
     * 
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Get registration status
     * 
     * @return string
     */
    public function getRegistrationStatus(): string
    {
        if (!$this->isSuccess()) {
            return 'failed';
        }
        
        if ($this->isDuplicate()) {
            return 'duplicate';
        }
        
        if ($this->isSuccessfullyAdded()) {
            return 'success';
        }
        
        return 'unknown';
    }

    /**
     * Get operation summary
     * 
     * @return array
     */
    public function getAddSummary(): array
    {
        return [
            'f4IndexNo' => $this->f4IndexNo,
            'isSuccess' => $this->isSuccess(),
            'isSuccessfullyAdded' => $this->isSuccessfullyAdded(),
            'isDuplicate' => $this->isDuplicate(),
            'registrationStatus' => $this->getRegistrationStatus(),
            'applicantId' => $this->applicantId,
            'hasWarnings' => $this->hasWarnings(),
            'warnings' => $this->warnings,
            'statusDescription' => $this->statusDescription
        ];
    }

    /**
     * Get the operation result description
     * 
     * @return string
     */
    public function getOperationResult(): string
    {
        if (!$this->isSuccess()) {
            return 'Failed: ' . $this->statusDescription;
        }
        
        if ($this->isDuplicate()) {
            return 'Duplicate: Applicant already exists in system';
        }
        
        if ($this->isSuccessfullyAdded()) {
            $result = 'Successfully added applicant';
            if (!empty($this->applicantId)) {
                $result .= ' (ID: ' . $this->applicantId . ')';
            }
            return $result;
        }
        
        return 'Unknown result: ' . $this->statusDescription;
    }

    /**
     * Check if the operation requires follow-up action
     * 
     * @return bool
     */
    public function requiresFollowUp(): bool
    {
        return $this->hasWarnings() || $this->isDuplicate() || !$this->isSuccessfullyAdded();
    }

    /**
     * Get recommended next steps
     * 
     * @return array
     */
    public function getRecommendedNextSteps(): array
    {
        $steps = [];
        
        if ($this->isDuplicate()) {
            $steps[] = 'Check existing applicant data for updates';
            $steps[] = 'Consider using update operation instead';
        }
        
        if ($this->hasWarnings()) {
            $steps[] = 'Review warnings and address any data issues';
        }
        
        if (!$this->isSuccessfullyAdded() && $this->isSuccess()) {
            $steps[] = 'Verify applicant data and retry operation';
        }
        
        if ($this->isSuccessfullyAdded()) {
            $steps[] = 'Proceed with programme submission';
        }
        
        return $steps;
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
        return $this->getAddSummary();
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
            $this->getOperationResult()
        );
    }
}