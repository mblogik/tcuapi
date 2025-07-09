<?php

/**
 * TCU API Client - Applicant Data Model
 * 
 * This file contains the Applicant data model class for the TCU API Client.
 * It represents an applicant's information with validation, utility methods,
 * and data transformation capabilities for TCU API operations.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Data
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Structured representation of applicant data with validation
 *             and helper methods for TCU admission system operations.
 */

namespace MBLogik\TCUAPIClient\Models\Data;

use MBLogik\TCUAPIClient\Models\BaseModel;

class Applicant extends BaseModel
{
    protected array $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'gender',
        'form_four_index_number',
        'form_six_index_number',
        'avn',
        'nationality',
        'year_of_birth',
        'applicant_category',
        'other_form_four_index_numbers',
        'other_form_six_index_numbers',
        'institution_code',
        'phone_number',
        'email_address',
        'physical_address',
        'date_of_birth',
        'district',
        'region',
        'ward',
        'disability_status',
        'disability_type'
    ];
    
    protected array $required = [
        'first_name',
        'surname',
        'gender',
        'form_four_index_number',
        'nationality',
        'year_of_birth',
        'applicant_category'
    ];
    
    protected array $casts = [
        'year_of_birth' => 'integer',
        'other_form_four_index_numbers' => 'array',
        'other_form_six_index_numbers' => 'array',
        'disability_status' => 'boolean'
    ];
    
    // Getters
    public function getFirstName(): ?string
    {
        return $this->get('first_name');
    }
    
    public function getMiddleName(): ?string
    {
        return $this->get('middle_name');
    }
    
    public function getSurname(): ?string
    {
        return $this->get('surname');
    }
    
    public function getFullName(): string
    {
        $parts = array_filter([
            $this->getFirstName(),
            $this->getMiddleName(),
            $this->getSurname()
        ]);
        
        return implode(' ', $parts);
    }
    
    public function getGender(): ?string
    {
        return $this->get('gender');
    }
    
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('form_four_index_number');
    }
    
    public function getFormSixIndexNumber(): ?string
    {
        return $this->get('form_six_index_number');
    }
    
    public function getAvn(): ?string
    {
        return $this->get('avn');
    }
    
    public function getNationality(): ?string
    {
        return $this->get('nationality');
    }
    
    public function getYearOfBirth(): ?int
    {
        return $this->get('year_of_birth');
    }
    
    public function getApplicantCategory(): ?string
    {
        return $this->get('applicant_category');
    }
    
    public function getOtherFormFourIndexNumbers(): array
    {
        return $this->get('other_form_four_index_numbers', []);
    }
    
    public function getOtherFormSixIndexNumbers(): array
    {
        return $this->get('other_form_six_index_numbers', []);
    }
    
    public function getInstitutionCode(): ?string
    {
        return $this->get('institution_code');
    }
    
    public function getPhoneNumber(): ?string
    {
        return $this->get('phone_number');
    }
    
    public function getEmailAddress(): ?string
    {
        return $this->get('email_address');
    }
    
    public function getPhysicalAddress(): ?string
    {
        return $this->get('physical_address');
    }
    
    public function getDateOfBirth(): ?string
    {
        return $this->get('date_of_birth');
    }
    
    public function getDisabilityStatus(): bool
    {
        return $this->get('disability_status', false);
    }
    
    public function getDisabilityType(): ?string
    {
        return $this->get('disability_type');
    }
    
    // Setters (fluent interface)
    public function setFirstName(string $firstName): static
    {
        return $this->set('first_name', $firstName);
    }
    
    public function setMiddleName(string $middleName): static
    {
        return $this->set('middle_name', $middleName);
    }
    
    public function setSurname(string $surname): static
    {
        return $this->set('surname', $surname);
    }
    
    public function setGender(string $gender): static
    {
        return $this->set('gender', $gender);
    }
    
    public function setFormFourIndexNumber(string $formFourIndexNumber): static
    {
        return $this->set('form_four_index_number', $formFourIndexNumber);
    }
    
    public function setFormSixIndexNumber(?string $formSixIndexNumber): static
    {
        return $this->set('form_six_index_number', $formSixIndexNumber);
    }
    
    public function setAvn(?string $avn): static
    {
        return $this->set('avn', $avn);
    }
    
    public function setNationality(string $nationality): static
    {
        return $this->set('nationality', $nationality);
    }
    
    public function setYearOfBirth(int $yearOfBirth): static
    {
        return $this->set('year_of_birth', $yearOfBirth);
    }
    
    public function setApplicantCategory(string $applicantCategory): static
    {
        return $this->set('applicant_category', $applicantCategory);
    }
    
    public function setOtherFormFourIndexNumbers(array $otherFormFourIndexNumbers): static
    {
        return $this->set('other_form_four_index_numbers', $otherFormFourIndexNumbers);
    }
    
    public function setOtherFormSixIndexNumbers(array $otherFormSixIndexNumbers): static
    {
        return $this->set('other_form_six_index_numbers', $otherFormSixIndexNumbers);
    }
    
    public function setInstitutionCode(?string $institutionCode): static
    {
        return $this->set('institution_code', $institutionCode);
    }
    
    public function setPhoneNumber(?string $phoneNumber): static
    {
        return $this->set('phone_number', $phoneNumber);
    }
    
    public function setEmailAddress(?string $emailAddress): static
    {
        return $this->set('email_address', $emailAddress);
    }
    
    public function setPhysicalAddress(?string $physicalAddress): static
    {
        return $this->set('physical_address', $physicalAddress);
    }
    
    public function setDateOfBirth(?string $dateOfBirth): static
    {
        return $this->set('date_of_birth', $dateOfBirth);
    }
    
    public function setDisabilityStatus(bool $disabilityStatus): static
    {
        return $this->set('disability_status', $disabilityStatus);
    }
    
    public function setDisabilityType(?string $disabilityType): static
    {
        return $this->set('disability_type', $disabilityType);
    }
    
    // Custom validation
    protected function customValidation(): array
    {
        $errors = [];
        
        // Gender validation
        $gender = $this->getGender();
        if ($gender && !in_array(strtoupper($gender), ['M', 'F', 'MALE', 'FEMALE'])) {
            $errors[] = 'Gender must be M, F, Male, or Female';
        }
        
        // Year of birth validation
        $yearOfBirth = $this->getYearOfBirth();
        if ($yearOfBirth && ($yearOfBirth < 1950 || $yearOfBirth > date('Y'))) {
            $errors[] = 'Year of birth must be between 1950 and ' . date('Y');
        }
        
        // Email validation
        $email = $this->getEmailAddress();
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address format';
        }
        
        // Phone number validation (basic)
        $phone = $this->getPhoneNumber();
        if ($phone && !preg_match('/^\+?[\d\s\-\(\)]+$/', $phone)) {
            $errors[] = 'Invalid phone number format';
        }
        
        // Form four index number validation
        $formFourIndex = $this->getFormFourIndexNumber();
        if ($formFourIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formFourIndex)) {
            $errors[] = 'Form four index number must be in format: S0123/0001/2023';
        }
        
        return $errors;
    }
    
    // Utility methods
    public function isLocal(): bool
    {
        return strtolower($this->getNationality() ?? '') === 'tanzanian';
    }
    
    public function isForeign(): bool
    {
        return !$this->isLocal();
    }
    
    public function hasFormSixResults(): bool
    {
        return !empty($this->getFormSixIndexNumber());
    }
    
    public function hasDiplomaResults(): bool
    {
        return !empty($this->getAvn());
    }
    
    public function hasDisability(): bool
    {
        return $this->getDisabilityStatus();
    }
}