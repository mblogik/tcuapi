<?php

/**
 * TCU API Client - Check Status Response Model
 * 
 * Response model for applicant status check requests from the TCU API.
 * Handles processing and validation of admission status data including
 * admission details, confirmation status, and graduation information.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Response model for applicant status check with comprehensive status methods
 */

namespace MBLogik\TCUAPIClient\Models\Response;

use MBLogik\TCUAPIClient\Enums\ResponseCode;

class CheckStatusResponse extends BaseResponse
{
    protected array $fillable = [
        'status_code',
        'status_description',
        'message',
        'data',
        'errors',
        'timestamp',
        'form_four_index_number',
        'admission_status',
        'admission_year',
        'institution_code',
        'institution_name',
        'programme_code',
        'programme_name',
        'admission_type',
        'confirmation_status',
        'graduation_status',
        'discontinuation_status',
        'transfer_status',
        'multiple_admissions'
    ];
    
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('form_four_index_number');
    }
    
    public function getAdmissionStatus(): ?string
    {
        return $this->get('admission_status');
    }
    
    public function getAdmissionYear(): ?string
    {
        return $this->get('admission_year');
    }
    
    public function getInstitutionCode(): ?string
    {
        return $this->get('institution_code');
    }
    
    public function getInstitutionName(): ?string
    {
        return $this->get('institution_name');
    }
    
    public function getProgrammeCode(): ?string
    {
        return $this->get('programme_code');
    }
    
    public function getProgrammeName(): ?string
    {
        return $this->get('programme_name');
    }
    
    public function getAdmissionType(): ?string
    {
        return $this->get('admission_type');
    }
    
    public function getConfirmationStatus(): ?string
    {
        return $this->get('confirmation_status');
    }
    
    public function getGraduationStatus(): ?string
    {
        return $this->get('graduation_status');
    }
    
    public function getDiscontinuationStatus(): ?string
    {
        return $this->get('discontinuation_status');
    }
    
    public function getTransferStatus(): ?string
    {
        return $this->get('transfer_status');
    }
    
    public function getMultipleAdmissions(): array
    {
        return $this->get('multiple_admissions', []);
    }
    
    // Status check methods
    public function hasAdmission(): bool
    {
        return !empty($this->getAdmissionStatus());
    }
    
    public function isAdmitted(): bool
    {
        return strtolower($this->getAdmissionStatus() ?? '') === 'admitted';
    }
    
    public function isConfirmed(): bool
    {
        return strtolower($this->getConfirmationStatus() ?? '') === 'confirmed';
    }
    
    public function hasGraduated(): bool
    {
        return strtolower($this->getGraduationStatus() ?? '') === 'graduated';
    }
    
    public function isDiscontinued(): bool
    {
        return strtolower($this->getDiscontinuationStatus() ?? '') === 'discontinued';
    }
    
    public function isTransferred(): bool
    {
        return !empty($this->getTransferStatus());
    }
    
    public function hasMultipleAdmissions(): bool
    {
        return !empty($this->getMultipleAdmissions());
    }
    
    public function canApply(): bool
    {
        // Can apply if no prior admission or if graduated/discontinued
        return !$this->hasAdmission() || $this->hasGraduated() || $this->isDiscontinued();
    }
    
    public function getStatusSummary(): array
    {
        return [
            'has_admission' => $this->hasAdmission(),
            'is_admitted' => $this->isAdmitted(),
            'is_confirmed' => $this->isConfirmed(),
            'has_graduated' => $this->hasGraduated(),
            'is_discontinued' => $this->isDiscontinued(),
            'is_transferred' => $this->isTransferred(),
            'has_multiple_admissions' => $this->hasMultipleAdmissions(),
            'can_apply' => $this->canApply()
        ];
    }
}