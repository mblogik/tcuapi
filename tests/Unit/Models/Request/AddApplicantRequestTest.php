<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Request;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;

class AddApplicantRequestTest extends TestCase
{
    public function testCanCreateForLocalApplicant()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        
        $this->assertEquals('INST001', $request->getInstitutionCode());
        $this->assertEquals('John', $request->getFirstName());
        $this->assertEquals('Doe', $request->getSurname());
        $this->assertEquals('M', $request->getGender());
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals(2000, $request->getYearOfBirth());
        $this->assertEquals('Government', $request->getApplicantCategory());
        $this->assertEquals('Tanzanian', $request->getNationality());
        $this->assertEquals('/applicants/add', $request->getEndpoint());
        $this->assertEquals('POST', $request->getMethod());
    }
    
    public function testCanCreateForForeignApplicant()
    {
        $request = AddApplicantRequest::forForeignApplicant(
            'INST001',
            'Jane',
            'Smith',
            'F',
            'PASS123456',
            'Kenyan',
            2001,
            'Private'
        );
        
        $this->assertEquals('INST001', $request->getInstitutionCode());
        $this->assertEquals('Jane', $request->getFirstName());
        $this->assertEquals('Smith', $request->getSurname());
        $this->assertEquals('F', $request->getGender());
        $this->assertEquals('PASS123456', $request->getPassportNumber());
        $this->assertEquals('Kenyan', $request->getNationality());
        $this->assertEquals(2001, $request->getYearOfBirth());
        $this->assertEquals('Private', $request->getApplicantCategory());
        $this->assertEquals('/applicants/add', $request->getEndpoint());
        $this->assertEquals('POST', $request->getMethod());
    }
    
    public function testCanCreateWithMiddleName()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government',
            'Michael'
        );
        
        $this->assertEquals('Michael', $request->getMiddleName());
    }
    
    public function testCanSetAllFields()
    {
        $request = new AddApplicantRequest();
        
        $request->setInstitutionCode('INST001')
                ->setFirstName('John')
                ->setMiddleName('Michael')
                ->setSurname('Doe')
                ->setGender('M')
                ->setFormFourIndexNumber('S0123/0001/2023')
                ->setFormSixIndexNumber('S0123/0001/2025')
                ->setAvn('AVN123456')
                ->setPassportNumber('PASS123456')
                ->setNationality('Tanzanian')
                ->setYearOfBirth(2000)
                ->setApplicantCategory('Government')
                ->setDateOfBirth('2000-01-01')
                ->setPlaceOfBirth('Dar es Salaam')
                ->setDisabilityStatus(false)
                ->setDisabilityDescription(null)
                ->setPhoneNumber('+255712345678')
                ->setEmailAddress('john.doe@example.com')
                ->setAddress('123 Main Street')
                ->setRegion('Dar es Salaam')
                ->setWard('Kinondoni')
                ->setGuardianName('Jane Doe')
                ->setGuardianPhone('+255712345679')
                ->setNecta('NECTA123456')
                ->setRegion('Dar es Salaam')
                ->setWard('Kinondoni');
        
        $this->assertEquals('INST001', $request->getInstitutionCode());
        $this->assertEquals('John', $request->getFirstName());
        $this->assertEquals('Michael', $request->getMiddleName());
        $this->assertEquals('Doe', $request->getSurname());
        $this->assertEquals('M', $request->getGender());
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals('S0123/0001/2025', $request->getFormSixIndexNumber());
        $this->assertEquals('AVN123456', $request->getAvn());
        $this->assertEquals('PASS123456', $request->getPassportNumber());
        $this->assertEquals('Tanzanian', $request->getNationality());
        $this->assertEquals(2000, $request->getYearOfBirth());
        $this->assertEquals('Government', $request->getApplicantCategory());
        $this->assertEquals('2000-01-01', $request->getDateOfBirth());
        $this->assertEquals('Dar es Salaam', $request->getPlaceOfBirth());
        $this->assertFalse($request->getDisabilityStatus());
        $this->assertNull($request->getDisabilityDescription());
        $this->assertEquals('+255712345678', $request->getPhoneNumber());
        $this->assertEquals('john.doe@example.com', $request->getEmailAddress());
        $this->assertEquals('123 Main Street', $request->getAddress());
        $this->assertEquals('Dar es Salaam', $request->getRegion());
        $this->assertEquals('Kinondoni', $request->getWard());
        $this->assertEquals('Jane Doe', $request->getGuardianName());
        $this->assertEquals('+255712345679', $request->getGuardianPhone());
        $this->assertEquals('NECTA123456', $request->getNecta());
    }
    
    public function testPrepareForApiRemovesNullValues()
    {
        $request = new AddApplicantRequest([
            'institution_code' => 'INST001',
            'first_name' => 'John',
            'middle_name' => null,
            'surname' => 'Doe',
            'gender' => 'M',
            'form_four_index_number' => 'S0123/0001/2023',
            'form_six_index_number' => null,
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government'
        ]);
        
        $prepared = $request->prepareForApi();
        
        $this->assertArrayHasKey('institution_code', $prepared);
        $this->assertArrayHasKey('first_name', $prepared);
        $this->assertArrayHasKey('surname', $prepared);
        $this->assertArrayHasKey('gender', $prepared);
        $this->assertArrayHasKey('form_four_index_number', $prepared);
        $this->assertArrayHasKey('nationality', $prepared);
        $this->assertArrayHasKey('year_of_birth', $prepared);
        $this->assertArrayHasKey('applicant_category', $prepared);
        $this->assertArrayNotHasKey('middle_name', $prepared);
        $this->assertArrayNotHasKey('form_six_index_number', $prepared);
    }
    
    public function testValidationPassesWithValidLocalApplicant()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationPassesWithValidForeignApplicant()
    {
        $request = AddApplicantRequest::forForeignApplicant(
            'INST001',
            'Jane',
            'Smith',
            'F',
            'PASS123456',
            'Kenyan',
            2001,
            'Private'
        );
        
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationFailsWithMissingRequiredFields()
    {
        $request = new AddApplicantRequest([
            'first_name' => 'John'
        ]);
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Field \'institution_code\' is required', $errors);
        $this->assertContains('Field \'surname\' is required', $errors);
        $this->assertContains('Field \'gender\' is required', $errors);
        $this->assertContains('Field \'nationality\' is required', $errors);
        $this->assertContains('Field \'year_of_birth\' is required', $errors);
        $this->assertContains('Field \'applicant_category\' is required', $errors);
    }
    
    public function testValidationFailsWithInvalidInstitutionCode()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INVALID@CODE',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Institution code must be 4-10 alphanumeric characters', $errors);
    }
    
    public function testValidationFailsWithInvalidGender()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'Invalid',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Gender must be M, F, Male, or Female', $errors);
    }
    
    public function testValidationFailsWithInvalidFormFourIndexNumber()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'INVALID_FORMAT',
            2000,
            'Government'
        );
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Form four index number must be in format: S0123/0001/2023', $errors);
    }
    
    public function testValidationFailsWithInvalidFormSixIndexNumber()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        $request->setFormSixIndexNumber('INVALID_FORMAT');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Form six index number must be in format: S0123/0001/2025', $errors);
    }
    
    public function testValidationFailsWithInvalidYearOfBirth()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            1900,
            'Government'
        );
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Year of birth must be between 1950 and ' . date('Y'), $errors);
    }
    
    public function testValidationFailsWithInvalidApplicantCategory()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Invalid'
        );
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Applicant category must be: Government, Private, or Special', $errors);
    }
    
    public function testValidationFailsWithInvalidEmailAddress()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        $request->setEmailAddress('invalid-email');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Email address must be a valid email format', $errors);
    }
    
    public function testValidationFailsWithInvalidPhoneNumber()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        $request->setPhoneNumber('invalid-phone');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Phone number must be in format: +255712345678', $errors);
    }
    
    public function testValidationFailsWithInvalidDateOfBirth()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        $request->setDateOfBirth('invalid-date');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Date of birth must be in format: YYYY-MM-DD', $errors);
    }
    
    public function testCastingWorksCorrectly()
    {
        $request = new AddApplicantRequest([
            'year_of_birth' => '2000',
            'disability_status' => 'true'
        ]);
        
        $this->assertIsInt($request->getYearOfBirth());
        $this->assertIsBool($request->getDisabilityStatus());
        $this->assertEquals(2000, $request->getYearOfBirth());
        $this->assertTrue($request->getDisabilityStatus());
    }
    
    public function testLocalApplicantRequiresFormFourIndexNumber()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );
        
        // Clear the form four index number
        $request->setFormFourIndexNumber('');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Local applicants must have form four index number', $errors);
    }
    
    public function testForeignApplicantRequiresPassportNumber()
    {
        $request = AddApplicantRequest::forForeignApplicant(
            'INST001',
            'Jane',
            'Smith',
            'F',
            'PASS123456',
            'Kenyan',
            2001,
            'Private'
        );
        
        // Clear the passport number
        $request->setPassportNumber('');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Foreign applicants must have passport number', $errors);
    }
}