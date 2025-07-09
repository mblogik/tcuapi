<?php

/**
 * TCU API Client - Submit Programme Request Model
 * 
 * Request model for submitting programme selections for applicants in the TCU API system.
 * Handles validation and preparation of programme submission data including programme codes,
 * priorities, and related submission details.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Request
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Request model for submitting programme selections with priority validation
 */

namespace MBLogik\TCUAPIClient\Models\Request;

class SubmitProgrammeRequest extends BaseRequest
{
    protected string $endpoint = '/applicants/submitProgramme';
    
    protected array $fillable = [
        'form_four_index_number',
        'programmes',
        'academic_year',
        'application_round',
        'submission_date'
    ];
    
    protected array $required = [
        'form_four_index_number',
        'programmes'
    ];
    
    protected array $casts = [
        'programmes' => 'array'
    ];
    
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('form_four_index_number');
    }
    
    public function setFormFourIndexNumber(string $formFourIndexNumber): static
    {
        return $this->set('form_four_index_number', $formFourIndexNumber);
    }
    
    public function getProgrammes(): array
    {
        return $this->get('programmes', []);
    }
    
    public function setProgrammes(array $programmes): static
    {
        return $this->set('programmes', $programmes);
    }
    
    public function getAcademicYear(): ?string
    {
        return $this->get('academic_year');
    }
    
    public function setAcademicYear(?string $academicYear): static
    {
        return $this->set('academic_year', $academicYear);
    }
    
    public function getApplicationRound(): ?string
    {
        return $this->get('application_round');
    }
    
    public function setApplicationRound(?string $applicationRound): static
    {
        return $this->set('application_round', $applicationRound);
    }
    
    public function getSubmissionDate(): ?string
    {
        return $this->get('submission_date');
    }
    
    public function setSubmissionDate(?string $submissionDate): static
    {
        return $this->set('submission_date', $submissionDate);
    }
    
    // Programme management methods
    public function addProgramme(string $programmeCode, int $priority): static
    {
        $programmes = $this->getProgrammes();
        $programmes[] = [
            'programme_code' => $programmeCode,
            'priority' => $priority
        ];
        
        return $this->setProgrammes($programmes);
    }
    
    public function addProgrammeWithDetails(string $programmeCode, int $priority, array $additionalDetails = []): static
    {
        $programmes = $this->getProgrammes();
        $programmes[] = array_merge([
            'programme_code' => $programmeCode,
            'priority' => $priority
        ], $additionalDetails);
        
        return $this->setProgrammes($programmes);
    }
    
    public function removeProgramme(string $programmeCode): static
    {
        $programmes = $this->getProgrammes();
        $programmes = array_filter($programmes, function ($programme) use ($programmeCode) {
            return $programme['programme_code'] !== $programmeCode;
        });
        
        return $this->setProgrammes(array_values($programmes));
    }
    
    public function updateProgrammePriority(string $programmeCode, int $newPriority): static
    {
        $programmes = $this->getProgrammes();
        
        foreach ($programmes as &$programme) {
            if ($programme['programme_code'] === $programmeCode) {
                $programme['priority'] = $newPriority;
                break;
            }
        }
        
        return $this->setProgrammes($programmes);
    }
    
    public function sortProgrammesByPriority(): static
    {
        $programmes = $this->getProgrammes();
        
        usort($programmes, function ($a, $b) {
            return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
        });
        
        return $this->setProgrammes($programmes);
    }
    
    public function getProgrammeCount(): int
    {
        return count($this->getProgrammes());
    }
    
    // Factory methods
    public static function forApplicant(string $formFourIndexNumber, array $programmes): static
    {
        return new static([
            'form_four_index_number' => $formFourIndexNumber,
            'programmes' => $programmes
        ]);
    }
    
    public static function withSingleProgramme(string $formFourIndexNumber, string $programmeCode): static
    {
        return new static([
            'form_four_index_number' => $formFourIndexNumber,
            'programmes' => [
                [
                    'programme_code' => $programmeCode,
                    'priority' => 1
                ]
            ]
        ]);
    }
    
    public static function withMultipleProgrammes(string $formFourIndexNumber, array $programmeCodes): static
    {
        $programmes = [];
        foreach ($programmeCodes as $index => $programmeCode) {
            $programmes[] = [
                'programme_code' => $programmeCode,
                'priority' => $index + 1
            ];
        }
        
        return new static([
            'form_four_index_number' => $formFourIndexNumber,
            'programmes' => $programmes
        ]);
    }
    
    protected function customValidation(): array
    {
        $errors = [];
        
        // Form four index number validation
        $formFourIndex = $this->getFormFourIndexNumber();
        if ($formFourIndex && !preg_match('/^[A-Z]\d{4}\/\d{4}\/\d{4}$/', $formFourIndex)) {
            $errors[] = 'Form four index number must be in format: S0123/0001/2023';
        }
        
        // Programmes validation
        $programmes = $this->getProgrammes();
        if (empty($programmes)) {
            $errors[] = 'At least one programme must be specified';
        }
        
        if (count($programmes) > 5) {
            $errors[] = 'Maximum 5 programmes can be selected';
        }
        
        $priorities = [];
        $programmeCodes = [];
        
        foreach ($programmes as $index => $programme) {
            // Check required fields
            if (!isset($programme['programme_code']) || empty($programme['programme_code'])) {
                $errors[] = "Programme code is required for programme at index {$index}";
                continue;
            }
            
            if (!isset($programme['priority']) || !is_numeric($programme['priority'])) {
                $errors[] = "Priority is required for programme at index {$index}";
                continue;
            }
            
            $programmeCode = $programme['programme_code'];
            $priority = (int)$programme['priority'];
            
            // Check programme code format
            if (!preg_match('/^[A-Z0-9]{4,10}$/', $programmeCode)) {
                $errors[] = "Invalid programme code format: {$programmeCode}";
            }
            
            // Check for duplicate programme codes
            if (in_array($programmeCode, $programmeCodes)) {
                $errors[] = "Duplicate programme code: {$programmeCode}";
            }
            $programmeCodes[] = $programmeCode;
            
            // Check priority range
            if ($priority < 1 || $priority > count($programmes)) {
                $errors[] = "Priority must be between 1 and " . count($programmes) . " for programme {$programmeCode}";
            }
            
            // Check for duplicate priorities
            if (in_array($priority, $priorities)) {
                $errors[] = "Duplicate priority {$priority} found";
            }
            $priorities[] = $priority;
        }
        
        return $errors;
    }
}