<?php

/**
 * TCU API Client - Add Applicant Request Model
 * 
 * Request model for adding new applicants to the TCU API system.
 * Handles validation and preparation of applicant data including personal information,
 * educational background, contact details, and various identifiers.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Request
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Request model for adding new applicants with comprehensive validation
 */

namespace MBLogik\TCUAPIClient\Models\Request;

use MBLogik\TCUAPIClient\Models\Data\Applicant;

class AddApplicantRequest extends BaseRequest
{
    protected string $endpoint = '/applicants/add';
    
    protected array $fillable = [
        'institution_code',
        'gender',
        'first_name',
        'middle_name',
        'surname',
        'form_four_index_number',
        'form_six_index_number',
        'avn',
        'nationality',
        'year_of_birth',
        'applicant_category',
        'other_form_four_index_numbers',
        'other_form_six_index_numbers',
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
        'institution_code',
        'gender',
        'first_name',
        'surname',
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
    
    public function getInstitutionCode(): ?string
    {
        return $this->get('institution_code');
    }
    
    public function setInstitutionCode(string $institutionCode): static
    {
        return $this->set('institution_code', $institutionCode);
    }
    
    public function getGender(): ?string
    {
        return $this->get('gender');
    }
    
    public function setGender(string $gender): static
    {
        return $this->set('gender', $gender);
    }
    
    public function getFirstName(): ?string
    {
        return $this->get('first_name');
    }
    
    public function setFirstName(string $firstName): static
    {
        return $this->set('first_name', $firstName);
    }
    
    public function getMiddleName(): ?string
    {
        return $this->get('middle_name');
    }
    
    public function setMiddleName(?string $middleName): static
    {
        return $this->set('middle_name', $middleName);
    }
    
    public function getSurname(): ?string
    {
        return $this->get('surname');
    }
    
    public function setSurname(string $surname): static
    {
        return $this->set('surname', $surname);
    }
    
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('form_four_index_number');
    }
    
    public function setFormFourIndexNumber(string $formFourIndexNumber): static
    {
        return $this->set('form_four_index_number', $formFourIndexNumber);
    }
    
    public function getFormSixIndexNumber(): ?string
    {
        return $this->get('form_six_index_number');
    }
    
    public function setFormSixIndexNumber(?string $formSixIndexNumber): static
    {
        return $this->set('form_six_index_number', $formSixIndexNumber);
    }
    
    public function getAvn(): ?string
    {
        return $this->get('avn');
    }
    
    public function setAvn(?string $avn): static
    {
        return $this->set('avn', $avn);
    }
    
    public function getNationality(): ?string
    {
        return $this->get('nationality');
    }
    
    public function setNationality(string $nationality): static
    {
        return $this->set('nationality', $nationality);
    }
    
    public function getYearOfBirth(): ?int
    {
        return $this->get('year_of_birth');
    }
    
    public function setYearOfBirth(int $yearOfBirth): static
    {
        return $this->set('year_of_birth', $yearOfBirth);
    }
    
    public function getApplicantCategory(): ?string
    {
        return $this->get('applicant_category');
    }
    
    public function setApplicantCategory(string $applicantCategory): static
    {
        return $this->set('applicant_category', $applicantCategory);
    }
    
    public function getOtherFormFourIndexNumbers(): array
    {
        return $this->get('other_form_four_index_numbers', []);
    }
    
    public function setOtherFormFourIndexNumbers(array $otherFormFourIndexNumbers): static
    {
        return $this->set('other_form_four_index_numbers', $otherFormFourIndexNumbers);
    }
    
    public function getOtherFormSixIndexNumbers(): array
    {
        return $this->get('other_form_six_index_numbers', []);
    }
    
    public function setOtherFormSixIndexNumbers(array $otherFormSixIndexNumbers): static
    {
        return $this->set('other_form_six_index_numbers', $otherFormSixIndexNumbers);
    }
    
    public function getPhoneNumber(): ?string
    {
        return $this->get('phone_number');
    }
    
    public function setPhoneNumber(?string $phoneNumber): static
    {
        return $this->set('phone_number', $phoneNumber);
    }
    
    public function getEmailAddress(): ?string
    {
        return $this->get('email_address');
    }
    
    public function setEmailAddress(?string $emailAddress): static
    {
        return $this->set('email_address', $emailAddress);
    }
    
    public function getPhysicalAddress(): ?string
    {
        return $this->get('physical_address');
    }
    
    public function setPhysicalAddress(?string $physicalAddress): static
    {
        return $this->set('physical_address', $physicalAddress);
    }
    
    public function getDateOfBirth(): ?string
    {
        return $this->get('date_of_birth');
    }
    
    public function setDateOfBirth(?string $dateOfBirth): static
    {
        return $this->set('date_of_birth', $dateOfBirth);
    }
    
    public function getDisabilityStatus(): bool
    {
        return $this->get('disability_status', false);
    }
    
    public function setDisabilityStatus(bool $disabilityStatus): static
    {
        return $this->set('disability_status', $disabilityStatus);
    }
    
    public function getDisabilityType(): ?string
    {
        return $this->get('disability_type');
    }
    
    public function setDisabilityType(?string $disabilityType): static
    {
        return $this->set('disability_type', $disabilityType);
    }
    
    // Factory methods
    public static function fromApplicant(Applicant $applicant, string $institutionCode): static
    {
        $request = new static($applicant->toArray());
        $request->setInstitutionCode($institutionCode);
        return $request;
    }
    
    public static function forLocalApplicant(
        string $institutionCode,
        string $firstName,
        string $surname,
        string $gender,
        string $formFourIndexNumber,
        int $yearOfBirth,
        string $applicantCategory,
        ?string $formSixIndexNumber = null
    ): static {
        return new static([
            'institution_code' => $institutionCode,
            'first_name' => $firstName,
            'surname' => $surname,
            'gender' => $gender,
            'form_four_index_number' => $formFourIndexNumber,
            'form_six_index_number' => $formSixIndexNumber,
            'nationality' => 'Tanzanian',
            'year_of_birth' => $yearOfBirth,
            'applicant_category' => $applicantCategory
        ]);
    }
    
    public static function forForeignApplicant(
        string $institutionCode,
        string $firstName,
        string $surname,
        string $gender,
        string $nationality,
        int $yearOfBirth,
        string $applicantCategory,
        ?string $avn = null
    ): static {
        return new static([
            'institution_code' => $institutionCode,
            'first_name' => $firstName,
            'surname' => $surname,
            'gender' => $gender,
            'nationality' => $nationality,
            'year_of_birth' => $yearOfBirth,
            'applicant_category' => $applicantCategory,
            'avn' => $avn
        ]);
    }
    
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
        
        // Phone number validation
        $phone = $this->getPhoneNumber();
        if ($phone && !preg_match('/^\+?[\d\s\-\(\)]+$/', $phone)) {
            $errors[] = 'Invalid phone number format';
        }
        
        // Form four index number validation
        $formFourIndex = $this->getFormFourIndexNumber();
        if ($formFourIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formFourIndex)) {
            $errors[] = 'Form four index number must be in format: S0123/0001/2023';
        }
        
        // Form six index number validation
        $formSixIndex = $this->getFormSixIndexNumber();
        if ($formSixIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formSixIndex)) {
            $errors[] = 'Form six index number must be in format: S0123/0001/2025';
        }
        
        // Institution code validation
        $institutionCode = $this->getInstitutionCode();
        if ($institutionCode && !preg_match('/^[A-Z0-9]{3,10}$/', $institutionCode)) {
            $errors[] = 'Institution code must be 3-10 alphanumeric characters';
        }
        
        // Applicant category validation
        $category = $this->getApplicantCategory();
        if ($category && !in_array(strtolower($category), ['government', 'private', 'loan', 'scholarship'])) {
            $errors[] = 'Applicant category must be: government, private, loan, or scholarship';
        }
        
        return $errors;
    }
}