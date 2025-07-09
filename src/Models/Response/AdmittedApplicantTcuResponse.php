<?php

/**
 * TCU API Client - Admitted Applicant Response Model
 * 
 * Response model for admitted applicant data from the TCU API.
 * Provides structured access to admitted applicant information including
 * form four/six index numbers, contact details, and admission status.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class AdmittedApplicantTcuResponse
{
    private string $f4IndexNo;
    private string $f6IndexNo;
    private string $mobileNumber;
    private string $emailAddress;
    private string $admissionStatus;
    private string $programmeCode;

    /**
     * Constructor
     * 
     * @param array $data Applicant data from TCU API response
     * @param string $programmeCode Programme code for this applicant
     */
    public function __construct(array $data, string $programmeCode = '')
    {
        $this->f4IndexNo = trim($data['f4indexno'] ?? '');
        $this->f6IndexNo = trim($data['f6indexno'] ?? '');
        $this->mobileNumber = trim($data['MobileNumber'] ?? '');
        $this->emailAddress = trim($data['EmailAddress'] ?? '');
        $this->admissionStatus = trim($data['AdmissionStatus'] ?? '');
        $this->programmeCode = $programmeCode;
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
     * Get Form 6 index number
     * 
     * @return string
     */
    public function getF6IndexNo(): string
    {
        return $this->f6IndexNo;
    }

    /**
     * Get mobile number
     * 
     * @return string
     */
    public function getMobileNumber(): string
    {
        return $this->mobileNumber;
    }

    /**
     * Get email address
     * 
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
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
     * Get programme code
     * 
     * @return string
     */
    public function getProgrammeCode(): string
    {
        return $this->programmeCode;
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
     * Check if applicant has valid contact information
     * 
     * @return bool
     */
    public function hasValidContactInfo(): bool
    {
        return !empty($this->mobileNumber) && !empty($this->emailAddress);
    }

    /**
     * Check if applicant has both Form 4 and Form 6 results
     * 
     * @return bool
     */
    public function hasBothFormResults(): bool
    {
        return !empty($this->f4IndexNo) && !empty($this->f6IndexNo);
    }

    /**
     * Get formatted mobile number (with country code if missing)
     * 
     * @return string
     */
    public function getFormattedMobileNumber(): string
    {
        $mobile = $this->mobileNumber;
        
        // Add Tanzania country code if missing
        if (!empty($mobile) && !str_starts_with($mobile, '+')) {
            if (str_starts_with($mobile, '0')) {
                $mobile = '+255' . substr($mobile, 1);
            } elseif (!str_starts_with($mobile, '255')) {
                $mobile = '+255' . $mobile;
            } else {
                $mobile = '+' . $mobile;
            }
        }
        
        return $mobile;
    }

    /**
     * Get applicant's full identification string
     * 
     * @return string
     */
    public function getFullIdentification(): string
    {
        $identification = "F4: {$this->f4IndexNo}";
        
        if (!empty($this->f6IndexNo)) {
            $identification .= " | F6: {$this->f6IndexNo}";
        }
        
        return $identification;
    }

    /**
     * Get applicant summary as array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'f4IndexNo' => $this->f4IndexNo,
            'f6IndexNo' => $this->f6IndexNo,
            'mobileNumber' => $this->mobileNumber,
            'emailAddress' => $this->emailAddress,
            'admissionStatus' => $this->admissionStatus,
            'programmeCode' => $this->programmeCode,
            'isProvisional' => $this->isProvisionalAdmission(),
            'isConfirmed' => $this->isConfirmedAdmission(),
            'hasValidContact' => $this->hasValidContactInfo(),
            'hasBothForms' => $this->hasBothFormResults(),
            'formattedMobile' => $this->getFormattedMobileNumber(),
            'fullIdentification' => $this->getFullIdentification()
        ];
    }

    /**
     * Get applicant summary for logging/debugging
     * 
     * @return string
     */
    public function getSummary(): string
    {
        return sprintf(
            "Applicant %s (%s) - %s - %s",
            $this->f4IndexNo,
            $this->programmeCode,
            $this->admissionStatus,
            $this->emailAddress
        );
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
        return $this->getSummary();
    }
}