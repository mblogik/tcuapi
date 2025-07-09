<?php

/**
 * TCU API Client - Submit Programme Response Model
 * 
 * Response model for programme submission operations from the TCU API.
 * Provides structured access to programme submission results including
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

class SubmitProgrammeTcuResponse
{
    private string $f4IndexNo;
    private int $statusCode;
    private string $statusDescription;
    private bool $isSubmitted;
    private array $submittedProgrammes;
    private string $admissionStatus;
    private string $programmeAdmitted;
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
        
        // Parse submission results
        $this->parseSubmissionResults($data);
    }

    /**
     * Parse submission results from response data
     * 
     * @param array $data
     */
    private function parseSubmissionResults(array $data): void
    {
        $description = strtolower($this->statusDescription);
        
        // Determine if successfully submitted
        $this->isSubmitted = $this->statusCode === 200 && (
            stripos($description, 'successful') !== false ||
            stripos($description, 'submitted') !== false ||
            stripos($description, 'operation was performed successfully') !== false
        );
        
        // Extract programme information
        $this->submittedProgrammes = [];
        if (isset($data['SelectedProgrammes'])) {
            $programmes = $data['SelectedProgrammes'];
            if (is_string($programmes)) {
                $this->submittedProgrammes = array_map('trim', explode(',', $programmes));
            } elseif (is_array($programmes)) {
                $this->submittedProgrammes = $programmes;
            }
        }
        
        // Extract admission information
        $this->admissionStatus = trim($data['AdmissionStatus'] ?? '');
        $this->programmeAdmitted = trim($data['ProgrammeAdmitted'] ?? '');
        
        // Extract warnings
        $this->warnings = [];
        if (isset($data['Warnings']) && is_array($data['Warnings'])) {
            $this->warnings = $data['Warnings'];
        } elseif (isset($data['warnings']) && is_string($data['warnings'])) {
            $this->warnings = explode(',', $data['warnings']);
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
     * Check if programmes were successfully submitted
     * 
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->isSubmitted;
    }

    /**
     * Get submitted programmes
     * 
     * @return array
     */
    public function getSubmittedProgrammes(): array
    {
        return $this->submittedProgrammes;
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
     * Get programme admitted to
     * 
     * @return string
     */
    public function getProgrammeAdmitted(): string
    {
        return $this->programmeAdmitted;
    }

    /**
     * Check if applicant has admission
     * 
     * @return bool
     */
    public function hasAdmission(): bool
    {
        return !empty($this->admissionStatus) || !empty($this->programmeAdmitted);
    }

    /**
     * Check if admission is provisional
     * 
     * @return bool
     */
    public function isProvisionalAdmission(): bool
    {
        return stripos($this->admissionStatus, 'provisional') !== false;
    }

    /**
     * Check if admission is confirmed
     * 
     * @return bool
     */
    public function isConfirmedAdmission(): bool
    {
        return stripos($this->admissionStatus, 'confirmed') !== false;
    }

    /**
     * Get number of programmes submitted
     * 
     * @return int
     */
    public function getProgrammeCount(): int
    {
        return count($this->submittedProgrammes);
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
     * Get submission summary
     * 
     * @return array
     */
    public function getSubmissionSummary(): array
    {
        return [
            'f4IndexNo' => $this->f4IndexNo,
            'isSuccess' => $this->isSuccess(),
            'isSubmitted' => $this->isSubmitted(),
            'programmeCount' => $this->getProgrammeCount(),
            'submittedProgrammes' => $this->submittedProgrammes,
            'hasAdmission' => $this->hasAdmission(),
            'admissionStatus' => $this->admissionStatus,
            'programmeAdmitted' => $this->programmeAdmitted,
            'isProvisional' => $this->isProvisionalAdmission(),
            'isConfirmed' => $this->isConfirmedAdmission(),
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
        
        if ($this->isSubmitted()) {
            $result = 'Successfully submitted ' . $this->getProgrammeCount() . ' programme(s)';
            
            if ($this->hasAdmission()) {
                $result .= ' with ' . strtolower($this->admissionStatus);
                if (!empty($this->programmeAdmitted)) {
                    $result .= ' to ' . $this->programmeAdmitted;
                }
            }
            
            return $result;
        }
        
        return 'Submission status unclear: ' . $this->statusDescription;
    }

    /**
     * Check if the operation was completed successfully
     * 
     * @return bool
     */
    public function isOperationComplete(): bool
    {
        return $this->isSuccess() && $this->isSubmitted();
    }

    /**
     * Get recommended next steps
     * 
     * @return array
     */
    public function getRecommendedNextSteps(): array
    {
        $steps = [];
        
        if ($this->isOperationComplete()) {
            $steps[] = 'Programme choices submitted successfully';
            
            if ($this->hasAdmission()) {
                if ($this->isProvisionalAdmission()) {
                    $steps[] = 'Provisional admission granted';
                    $steps[] = 'Monitor for confirmation updates';
                    $steps[] = 'Prepare for potential confirmation process';
                } elseif ($this->isConfirmedAdmission()) {
                    $steps[] = 'Confirmed admission granted';
                    $steps[] = 'Proceed with enrollment procedures';
                }
            } else {
                $steps[] = 'Monitor admission status updates';
                $steps[] = 'Wait for admission decision';
            }
        } else {
            $steps[] = 'Review submission status';
            if (!$this->isSuccess()) {
                $steps[] = 'Check programme codes and applicant data';
                $steps[] = 'Retry submission if necessary';
            }
        }
        
        if ($this->hasWarnings()) {
            $steps[] = 'Review and address warnings';
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
        return $this->getSubmissionSummary();
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