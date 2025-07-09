<?php

/**
 * TCU API Client - Programme Data Model
 * 
 * Represents a programme (academic program) entity in the TCU API system.
 * This model handles programme information including code, name, type, level,
 * duration, faculty, admission requirements, capacity, and various programme-specific
 * attributes with comprehensive validation and utility methods.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Data
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Data model for academic programme information with validation and utility methods
 */

namespace MBLogik\TCUAPIClient\Models\Data;

use MBLogik\TCUAPIClient\Models\BaseModel;

class Programme extends BaseModel
{
    protected array $fillable = [
        'programme_code',
        'programme_name',
        'programme_type',
        'level',
        'duration',
        'faculty',
        'department',
        'institution_code',
        'institution_name',
        'admission_requirements',
        'capacity',
        'available_slots',
        'minimum_grade',
        'subjects_required',
        'is_active',
        'academic_year',
        'intake_period',
        'application_deadline',
        'admission_start_date',
        'admission_end_date',
        'tuition_fee',
        'currency',
        'mode_of_study',
        'entry_requirements'
    ];
    
    protected array $required = [
        'programme_code',
        'programme_name',
        'programme_type',
        'level',
        'institution_code'
    ];
    
    protected array $casts = [
        'duration' => 'integer',
        'capacity' => 'integer',
        'available_slots' => 'integer',
        'subjects_required' => 'array',
        'admission_requirements' => 'array',
        'entry_requirements' => 'array',
        'is_active' => 'boolean',
        'tuition_fee' => 'float'
    ];
    
    // Getters
    public function getProgrammeCode(): ?string
    {
        return $this->get('programme_code');
    }
    
    public function getProgrammeName(): ?string
    {
        return $this->get('programme_name');
    }
    
    public function getProgrammeType(): ?string
    {
        return $this->get('programme_type');
    }
    
    public function getLevel(): ?string
    {
        return $this->get('level');
    }
    
    public function getDuration(): ?int
    {
        return $this->get('duration');
    }
    
    public function getFaculty(): ?string
    {
        return $this->get('faculty');
    }
    
    public function getDepartment(): ?string
    {
        return $this->get('department');
    }
    
    public function getInstitutionCode(): ?string
    {
        return $this->get('institution_code');
    }
    
    public function getInstitutionName(): ?string
    {
        return $this->get('institution_name');
    }
    
    public function getAdmissionRequirements(): array
    {
        return $this->get('admission_requirements', []);
    }
    
    public function getCapacity(): ?int
    {
        return $this->get('capacity');
    }
    
    public function getAvailableSlots(): ?int
    {
        return $this->get('available_slots');
    }
    
    public function getMinimumGrade(): ?string
    {
        return $this->get('minimum_grade');
    }
    
    public function getSubjectsRequired(): array
    {
        return $this->get('subjects_required', []);
    }
    
    public function isActive(): bool
    {
        return $this->get('is_active', true);
    }
    
    public function getAcademicYear(): ?string
    {
        return $this->get('academic_year');
    }
    
    public function getIntakePeriod(): ?string
    {
        return $this->get('intake_period');
    }
    
    public function getApplicationDeadline(): ?string
    {
        return $this->get('application_deadline');
    }
    
    public function getAdmissionStartDate(): ?string
    {
        return $this->get('admission_start_date');
    }
    
    public function getAdmissionEndDate(): ?string
    {
        return $this->get('admission_end_date');
    }
    
    public function getTuitionFee(): ?float
    {
        return $this->get('tuition_fee');
    }
    
    public function getCurrency(): ?string
    {
        return $this->get('currency');
    }
    
    public function getModeOfStudy(): ?string
    {
        return $this->get('mode_of_study');
    }
    
    public function getEntryRequirements(): array
    {
        return $this->get('entry_requirements', []);
    }
    
    // Setters (fluent interface)
    public function setProgrammeCode(string $programmeCode): static
    {
        return $this->set('programme_code', $programmeCode);
    }
    
    public function setProgrammeName(string $programmeName): static
    {
        return $this->set('programme_name', $programmeName);
    }
    
    public function setProgrammeType(string $programmeType): static
    {
        return $this->set('programme_type', $programmeType);
    }
    
    public function setLevel(string $level): static
    {
        return $this->set('level', $level);
    }
    
    public function setDuration(int $duration): static
    {
        return $this->set('duration', $duration);
    }
    
    public function setFaculty(string $faculty): static
    {
        return $this->set('faculty', $faculty);
    }
    
    public function setDepartment(string $department): static
    {
        return $this->set('department', $department);
    }
    
    public function setInstitutionCode(string $institutionCode): static
    {
        return $this->set('institution_code', $institutionCode);
    }
    
    public function setInstitutionName(string $institutionName): static
    {
        return $this->set('institution_name', $institutionName);
    }
    
    public function setAdmissionRequirements(array $admissionRequirements): static
    {
        return $this->set('admission_requirements', $admissionRequirements);
    }
    
    public function setCapacity(int $capacity): static
    {
        return $this->set('capacity', $capacity);
    }
    
    public function setAvailableSlots(int $availableSlots): static
    {
        return $this->set('available_slots', $availableSlots);
    }
    
    public function setMinimumGrade(string $minimumGrade): static
    {
        return $this->set('minimum_grade', $minimumGrade);
    }
    
    public function setSubjectsRequired(array $subjectsRequired): static
    {
        return $this->set('subjects_required', $subjectsRequired);
    }
    
    public function setIsActive(bool $isActive): static
    {
        return $this->set('is_active', $isActive);
    }
    
    public function setAcademicYear(string $academicYear): static
    {
        return $this->set('academic_year', $academicYear);
    }
    
    public function setIntakePeriod(string $intakePeriod): static
    {
        return $this->set('intake_period', $intakePeriod);
    }
    
    public function setApplicationDeadline(string $applicationDeadline): static
    {
        return $this->set('application_deadline', $applicationDeadline);
    }
    
    public function setAdmissionStartDate(string $admissionStartDate): static
    {
        return $this->set('admission_start_date', $admissionStartDate);
    }
    
    public function setAdmissionEndDate(string $admissionEndDate): static
    {
        return $this->set('admission_end_date', $admissionEndDate);
    }
    
    public function setTuitionFee(float $tuitionFee): static
    {
        return $this->set('tuition_fee', $tuitionFee);
    }
    
    public function setCurrency(string $currency): static
    {
        return $this->set('currency', $currency);
    }
    
    public function setModeOfStudy(string $modeOfStudy): static
    {
        return $this->set('mode_of_study', $modeOfStudy);
    }
    
    public function setEntryRequirements(array $entryRequirements): static
    {
        return $this->set('entry_requirements', $entryRequirements);
    }
    
    // Custom validation
    protected function customValidation(): array
    {
        $errors = [];
        
        // Programme code validation
        $programmeCode = $this->getProgrammeCode();
        if ($programmeCode && !preg_match('/^[A-Z0-9]{4,10}$/', $programmeCode)) {
            $errors[] = 'Programme code must be 4-10 alphanumeric characters';
        }
        
        // Programme type validation
        $type = $this->getProgrammeType();
        if ($type && !in_array(strtolower($type), ['degree', 'diploma', 'certificate', 'postgraduate'])) {
            $errors[] = 'Programme type must be: degree, diploma, certificate, or postgraduate';
        }
        
        // Level validation
        $level = $this->getLevel();
        if ($level && !in_array(strtolower($level), ['undergraduate', 'postgraduate', 'diploma', 'certificate'])) {
            $errors[] = 'Level must be: undergraduate, postgraduate, diploma, or certificate';
        }
        
        // Duration validation
        $duration = $this->getDuration();
        if ($duration && ($duration < 1 || $duration > 10)) {
            $errors[] = 'Duration must be between 1 and 10 years';
        }
        
        // Capacity validation
        $capacity = $this->getCapacity();
        if ($capacity && $capacity < 1) {
            $errors[] = 'Capacity must be at least 1';
        }
        
        // Available slots validation
        $availableSlots = $this->getAvailableSlots();
        if ($availableSlots !== null && $availableSlots < 0) {
            $errors[] = 'Available slots cannot be negative';
        }
        
        // Tuition fee validation
        $tuitionFee = $this->getTuitionFee();
        if ($tuitionFee !== null && $tuitionFee < 0) {
            $errors[] = 'Tuition fee cannot be negative';
        }
        
        return $errors;
    }
    
    // Utility methods
    public function isUndergraduate(): bool
    {
        return strtolower($this->getLevel() ?? '') === 'undergraduate';
    }
    
    public function isPostgraduate(): bool
    {
        return strtolower($this->getLevel() ?? '') === 'postgraduate';
    }
    
    public function isDiploma(): bool
    {
        return strtolower($this->getLevel() ?? '') === 'diploma';
    }
    
    public function isCertificate(): bool
    {
        return strtolower($this->getLevel() ?? '') === 'certificate';
    }
    
    public function hasAvailableSlots(): bool
    {
        $available = $this->getAvailableSlots();
        return $available !== null && $available > 0;
    }
    
    public function isApplicationOpen(): bool
    {
        $deadline = $this->getApplicationDeadline();
        if (!$deadline) {
            return true; // No deadline set
        }
        
        return strtotime($deadline) > time();
    }
    
    public function getOccupancyRate(): ?float
    {
        $capacity = $this->getCapacity();
        $available = $this->getAvailableSlots();
        
        if ($capacity === null || $available === null) {
            return null;
        }
        
        $occupied = $capacity - $available;
        return ($occupied / $capacity) * 100;
    }
}