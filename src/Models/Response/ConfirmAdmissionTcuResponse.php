<?php

/**
 * TCU API Client - Confirm Admission Response Model
 * 
 * Response model for admission confirmation operations from the TCU API.
 * Provides structured access to confirmation results including
 * status information and operation success details.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class ConfirmAdmissionTcuResponse
{
    private string $f4IndexNo;
    private int $statusCode;
    private string $statusDescription;
    private bool $isConfirmed;
    private string $confirmationCode;
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
        
        // Parse confirmation results
        $this->parseConfirmationResults($data);
    }

    /**
     * Parse confirmation results from response data
     * 
     * @param array $data
     */
    private function parseConfirmationResults(array $data): void
    {
        $description = strtolower($this->statusDescription);
        
        // Determine if successfully confirmed
        $this->isConfirmed = $this->statusCode === 200 && (
            stripos($description, 'successful') !== false ||
            stripos($description, 'confirmed') !== false ||
            stripos($description, 'operation was successful') !== false
        );
        
        // Extract confirmation code if provided
        $this->confirmationCode = trim($data['ConfirmationCode'] ?? $data['confirmation_code'] ?? '');
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
     * Check if admission was successfully confirmed
     * 
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * Get confirmation code used
     * 
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }

    /**
     * Get confirmation summary
     * 
     * @return array
     */
    public function getConfirmationSummary(): array
    {
        return [
            'f4IndexNo' => $this->f4IndexNo,
            'isSuccess' => $this->isSuccess(),
            'isConfirmed' => $this->isConfirmed(),
            'confirmationCode' => $this->confirmationCode,
            'statusDescription' => $this->statusDescription,
            'statusCode' => $this->statusCode
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
        
        if ($this->isConfirmed()) {
            return 'Successfully confirmed admission';
        }
        
        return 'Confirmation status unclear: ' . $this->statusDescription;
    }

    /**
     * Check if the operation was completed successfully
     * 
     * @return bool
     */
    public function isOperationComplete(): bool
    {
        return $this->isSuccess() && $this->isConfirmed();
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
            $steps[] = 'Admission confirmed successfully';
            $steps[] = 'Proceed with enrollment procedures';
            $steps[] = 'Check institution-specific requirements';
        } else {
            $steps[] = 'Review confirmation status';
            if (!$this->isSuccess()) {
                $steps[] = 'Check confirmation code validity';
                $steps[] = 'Verify applicant eligibility';
            }
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
        return $this->getConfirmationSummary();
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