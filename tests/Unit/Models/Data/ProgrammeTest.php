<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Data;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Data\Programme;

class ProgrammeTest extends TestCase
{
    public function testCanCreateProgrammeWithValidData()
    {
        $data = $this->getTestProgrammeData();
        $programme = new Programme($data);
        
        $this->assertEquals('BSCS001', $programme->getProgrammeCode());
        $this->assertEquals('Bachelor of Science in Computer Science', $programme->getProgrammeName());
        $this->assertEquals('degree', $programme->getProgrammeType());
        $this->assertEquals('undergraduate', $programme->getLevel());
        $this->assertEquals(4, $programme->getDuration());
        $this->assertEquals('Faculty of Computing', $programme->getFaculty());
        $this->assertEquals('Computer Science', $programme->getDepartment());
        $this->assertEquals('UDSM', $programme->getInstitutionCode());
        $this->assertEquals('University of Dar es Salaam', $programme->getInstitutionName());
        $this->assertEquals(100, $programme->getCapacity());
        $this->assertEquals(25, $programme->getAvailableSlots());
        $this->assertEquals('B', $programme->getMinimumGrade());
        $this->assertTrue($programme->isActive());
        $this->assertEquals('2025/2026', $programme->getAcademicYear());
        $this->assertEquals(1500000.00, $programme->getTuitionFee());
        $this->assertEquals('TZS', $programme->getCurrency());
        $this->assertEquals('Full Time', $programme->getModeOfStudy());
    }
    
    public function testFluentInterface()
    {
        $programme = (new Programme())
            ->setProgrammeCode('BSIT002')
            ->setProgrammeName('Bachelor of Science in Information Technology')
            ->setProgrammeType('degree')
            ->setLevel('undergraduate')
            ->setDuration(4)
            ->setFaculty('Faculty of Computing')
            ->setInstitutionCode('UDSM')
            ->setCapacity(80)
            ->setAvailableSlots(30)
            ->setIsActive(true)
            ->setTuitionFee(1800000.00)
            ->setCurrency('TZS');
        
        $this->assertEquals('BSIT002', $programme->getProgrammeCode());
        $this->assertEquals('Bachelor of Science in Information Technology', $programme->getProgrammeName());
        $this->assertEquals('degree', $programme->getProgrammeType());
        $this->assertEquals('undergraduate', $programme->getLevel());
        $this->assertEquals(4, $programme->getDuration());
        $this->assertEquals('Faculty of Computing', $programme->getFaculty());
        $this->assertEquals('UDSM', $programme->getInstitutionCode());
        $this->assertEquals(80, $programme->getCapacity());
        $this->assertEquals(30, $programme->getAvailableSlots());
        $this->assertTrue($programme->isActive());
        $this->assertEquals(1800000.00, $programme->getTuitionFee());
        $this->assertEquals('TZS', $programme->getCurrency());
    }
    
    public function testUtilityMethods()
    {
        $undergraduateProgramme = new Programme(['level' => 'undergraduate']);
        $this->assertTrue($undergraduateProgramme->isUndergraduate());
        $this->assertFalse($undergraduateProgramme->isPostgraduate());
        $this->assertFalse($undergraduateProgramme->isDiploma());
        $this->assertFalse($undergraduateProgramme->isCertificate());
        
        $postgraduateProgramme = new Programme(['level' => 'postgraduate']);
        $this->assertFalse($postgraduateProgramme->isUndergraduate());
        $this->assertTrue($postgraduateProgramme->isPostgraduate());
        $this->assertFalse($postgraduateProgramme->isDiploma());
        $this->assertFalse($postgraduateProgramme->isCertificate());
        
        $diplomaProgramme = new Programme(['level' => 'diploma']);
        $this->assertFalse($diplomaProgramme->isUndergraduate());
        $this->assertFalse($diplomaProgramme->isPostgraduate());
        $this->assertTrue($diplomaProgramme->isDiploma());
        $this->assertFalse($diplomaProgramme->isCertificate());
        
        $certificateProgramme = new Programme(['level' => 'certificate']);
        $this->assertFalse($certificateProgramme->isUndergraduate());
        $this->assertFalse($certificateProgramme->isPostgraduate());
        $this->assertFalse($certificateProgramme->isDiploma());
        $this->assertTrue($certificateProgramme->isCertificate());
    }
    
    public function testHasAvailableSlots()
    {
        $programmeWithSlots = new Programme(['available_slots' => 10]);
        $this->assertTrue($programmeWithSlots->hasAvailableSlots());
        
        $programmeWithoutSlots = new Programme(['available_slots' => 0]);
        $this->assertFalse($programmeWithoutSlots->hasAvailableSlots());
        
        $programmeWithNullSlots = new Programme();
        $this->assertFalse($programmeWithNullSlots->hasAvailableSlots());
    }
    
    public function testGetOccupancyRate()
    {
        $programme = new Programme([
            'capacity' => 100,
            'available_slots' => 25
        ]);
        
        $this->assertEquals(75.0, $programme->getOccupancyRate());
        
        $programmeWithNullValues = new Programme();
        $this->assertNull($programmeWithNullValues->getOccupancyRate());
    }
    
    public function testIsApplicationOpen()
    {
        $futureDeadline = date('Y-m-d', strtotime('+30 days'));
        $programme = new Programme(['application_deadline' => $futureDeadline]);
        $this->assertTrue($programme->isApplicationOpen());
        
        $pastDeadline = date('Y-m-d', strtotime('-30 days'));
        $programmePast = new Programme(['application_deadline' => $pastDeadline]);
        $this->assertFalse($programmePast->isApplicationOpen());
        
        $programmeNoDeadline = new Programme();
        $this->assertTrue($programmeNoDeadline->isApplicationOpen());
    }
    
    public function testValidationWithValidData()
    {
        $data = $this->getTestProgrammeData();
        $programme = new Programme($data);
        
        $errors = $programme->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationWithMissingRequiredFields()
    {
        $programme = new Programme([
            'programme_name' => 'Test Programme'
        ]);
        
        $errors = $programme->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Field \'programme_code\' is required', $errors);
        $this->assertContains('Field \'programme_type\' is required', $errors);
        $this->assertContains('Field \'level\' is required', $errors);
        $this->assertContains('Field \'institution_code\' is required', $errors);
    }
    
    public function testValidationWithInvalidProgrammeCode()
    {
        $data = $this->getTestProgrammeData();
        $data['programme_code'] = 'INVALID@CODE';
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Programme code must be 4-10 alphanumeric characters', $errors);
    }
    
    public function testValidationWithInvalidProgrammeType()
    {
        $data = $this->getTestProgrammeData();
        $data['programme_type'] = 'invalid_type';
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Programme type must be: degree, diploma, certificate, or postgraduate', $errors);
    }
    
    public function testValidationWithInvalidLevel()
    {
        $data = $this->getTestProgrammeData();
        $data['level'] = 'invalid_level';
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Level must be: undergraduate, postgraduate, diploma, or certificate', $errors);
    }
    
    public function testValidationWithInvalidDuration()
    {
        $data = $this->getTestProgrammeData();
        $data['duration'] = 15;
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Duration must be between 1 and 10 years', $errors);
    }
    
    public function testValidationWithInvalidCapacity()
    {
        $data = $this->getTestProgrammeData();
        $data['capacity'] = 0;
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Capacity must be at least 1', $errors);
    }
    
    public function testValidationWithNegativeAvailableSlots()
    {
        $data = $this->getTestProgrammeData();
        $data['available_slots'] = -5;
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Available slots cannot be negative', $errors);
    }
    
    public function testValidationWithNegativeTuitionFee()
    {
        $data = $this->getTestProgrammeData();
        $data['tuition_fee'] = -1000.00;
        
        $programme = new Programme($data);
        $errors = $programme->validate();
        
        $this->assertContains('Tuition fee cannot be negative', $errors);
    }
    
    public function testArraysAreProperlyCast()
    {
        $programme = new Programme([
            'subjects_required' => ['Mathematics', 'Physics', 'Chemistry'],
            'admission_requirements' => ['Form Four Certificate', 'Form Six Certificate'],
            'entry_requirements' => ['Minimum Grade C', 'Pass in English']
        ]);
        
        $this->assertIsArray($programme->getSubjectsRequired());
        $this->assertIsArray($programme->getAdmissionRequirements());
        $this->assertIsArray($programme->getEntryRequirements());
        $this->assertEquals(['Mathematics', 'Physics', 'Chemistry'], $programme->getSubjectsRequired());
        $this->assertEquals(['Form Four Certificate', 'Form Six Certificate'], $programme->getAdmissionRequirements());
        $this->assertEquals(['Minimum Grade C', 'Pass in English'], $programme->getEntryRequirements());
    }
    
    public function testNumericFieldsAreProperlyCast()
    {
        $programme = new Programme([
            'duration' => '4',
            'capacity' => '100',
            'available_slots' => '25',
            'tuition_fee' => '1500000.00',
            'is_active' => 'true'
        ]);
        
        $this->assertIsInt($programme->getDuration());
        $this->assertIsInt($programme->getCapacity());
        $this->assertIsInt($programme->getAvailableSlots());
        $this->assertIsFloat($programme->getTuitionFee());
        $this->assertIsBool($programme->isActive());
        
        $this->assertEquals(4, $programme->getDuration());
        $this->assertEquals(100, $programme->getCapacity());
        $this->assertEquals(25, $programme->getAvailableSlots());
        $this->assertEquals(1500000.00, $programme->getTuitionFee());
        $this->assertTrue($programme->isActive());
    }
}