<?php

/**
 * TCU API Client - Add Applicant Response Model
 * 
 * Response model for applicant addition requests from the TCU API.
 * Handles processing and validation of applicant creation responses including
 * success status, validation errors, warnings, and duplicate checking results.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Response model for applicant addition with validation and duplicate checking
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class AddApplicantResponse extends BaseResponse
{
    protected array $fillable = [
        'status_code',
        'status_description',
        'message',
        'data',
        'errors',
        'timestamp',
        'form_four_index_number',
        'applicant_id',
        'registration_number',
        'application_status',
        'validation_errors',
        'warnings',
        'duplicate_check_result',
        'next_steps'
    ];
    
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('form_four_index_number');
    }
    
    public function getApplicantId(): ?string
    {
        return $this->get('applicant_id');
    }
    
    public function getRegistrationNumber(): ?string
    {
        return $this->get('registration_number');
    }
    
    public function getApplicationStatus(): ?string
    {
        return $this->get('application_status');
    }
    
    public function getValidationErrors(): array
    {
        return $this->get('validation_errors', []);
    }
    
    public function getWarnings(): array
    {
        return $this->get('warnings', []);
    }
    
    public function getDuplicateCheckResult(): ?array
    {
        return $this->get('duplicate_check_result');
    }
    
    public function getNextSteps(): array
    {
        return $this->get('next_steps', []);
    }
    
    // Status check methods
    public function isSuccessfullyAdded(): bool
    {
        return $this->isSuccess() && !empty($this->getApplicantId());
    }
    
    public function hasValidationErrors(): bool
    {
        return !empty($this->getValidationErrors());
    }
    
    public function hasWarnings(): bool
    {
        return !empty($this->getWarnings());
    }
    
    public function isDuplicate(): bool
    {
        $duplicateResult = $this->getDuplicateCheckResult();
        return $duplicateResult !== null && ($duplicateResult['is_duplicate'] ?? false);
    }
    
    public function requiresManualReview(): bool
    {
        return strtolower($this->getApplicationStatus() ?? '') === 'pending_review';
    }
    
    public function canProceedToNext(): bool
    {
        return $this->isSuccessfullyAdded() && !$this->requiresManualReview();
    }
    
    public function getAddSummary(): array
    {
        return [
            'successfully_added' => $this->isSuccessfullyAdded(),
            'applicant_id' => $this->getApplicantId(),
            'registration_number' => $this->getRegistrationNumber(),
            'has_validation_errors' => $this->hasValidationErrors(),
            'has_warnings' => $this->hasWarnings(),
            'is_duplicate' => $this->isDuplicate(),
            'requires_manual_review' => $this->requiresManualReview(),
            'can_proceed_to_next' => $this->canProceedToNext()
        ];
    }
}