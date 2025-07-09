<?php

/**
 * TCU API Client - Check Status Request Model
 * 
 * Request model for checking applicant status in the TCU API system.
 * Handles validation and preparation of data for checking admission status
 * using various identifiers like form four/six index numbers and AVN.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Request
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Request model for checking applicant admission status via TCU API
 */

namespace MBLogik\TCUAPIClient\Models\Request;

class CheckStatusRequest extends BaseRequest
{
    protected string $endpoint = '/applicants/checkStatus';
    
    protected array $fillable = [
        'form_four_index_number',
        'form_six_index_number',
        'avn',
        'form_four_index_numbers',
        'other_form_four_index_numbers',
        'other_form_six_index_numbers',
        'check_all_rounds'
    ];
    
    protected array $required = [
        'form_four_index_number'
    ];
    
    protected array $casts = [
        'form_four_index_numbers' => 'array',
        'other_form_four_index_numbers' => 'array',
        'other_form_six_index_numbers' => 'array',
        'check_all_rounds' => 'boolean'
    ];
    
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
    
    public function getFormFourIndexNumbers(): array
    {
        return $this->get('form_four_index_numbers', []);
    }
    
    public function setFormFourIndexNumbers(array $formFourIndexNumbers): static
    {
        return $this->set('form_four_index_numbers', $formFourIndexNumbers);
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
    
    public function getCheckAllRounds(): bool
    {
        return $this->get('check_all_rounds', false);
    }
    
    public function setCheckAllRounds(bool $checkAllRounds): static
    {
        return $this->set('check_all_rounds', $checkAllRounds);
    }
    
    // Factory methods
    public static function forSingleApplicant(string $formFourIndexNumber, ?string $formSixIndexNumber = null, ?string $avn = null): static
    {
        return new static([
            'form_four_index_number' => $formFourIndexNumber,
            'form_six_index_number' => $formSixIndexNumber,
            'avn' => $avn
        ]);
    }
    
    public static function forMultipleApplicants(array $formFourIndexNumbers): static
    {
        return new static([
            'form_four_index_numbers' => $formFourIndexNumbers
        ]);
    }
    
    public static function forApplicantWithMultipleResults(
        string $primaryFormFourIndex,
        array $otherFormFourIndexes = [],
        array $otherFormSixIndexes = []
    ): static {
        return new static([
            'form_four_index_number' => $primaryFormFourIndex,
            'other_form_four_index_numbers' => $otherFormFourIndexes,
            'other_form_six_index_numbers' => $otherFormSixIndexes
        ]);
    }
    
    protected function customValidation(): array
    {
        $errors = [];
        
        // Check if at least one identifier is provided
        if (!$this->getFormFourIndexNumber() && empty($this->getFormFourIndexNumbers())) {
            $errors[] = 'Either form_four_index_number or form_four_index_numbers must be provided';
        }
        
        // Validate form four index format
        $formFourIndex = $this->getFormFourIndexNumber();
        if ($formFourIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formFourIndex)) {
            $errors[] = 'Form four index number must be in format: S0123/0001/2023';
        }
        
        // Validate multiple form four indexes
        foreach ($this->getFormFourIndexNumbers() as $index) {
            if (!preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $index)) {
                $errors[] = "Invalid form four index number format: {$index}";
            }
        }
        
        // Validate form six index format
        $formSixIndex = $this->getFormSixIndexNumber();
        if ($formSixIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formSixIndex)) {
            $errors[] = 'Form six index number must be in format: S0123/0001/2025';
        }
        
        return $errors;
    }
}