<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Data;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Data\Applicant;

class ApplicantTest extends TestCase
{
    public function testCanCreateApplicantWithValidData()
    {
        $data = $this->getTestApplicantData();
        $applicant = new Applicant($data);
        
        $this->assertEquals('John', $applicant->getFirstName());
        $this->assertEquals('Michael', $applicant->getMiddleName());
        $this->assertEquals('Doe', $applicant->getSurname());
        $this->assertEquals('John Michael Doe', $applicant->getFullName());
        $this->assertEquals('M', $applicant->getGender());
        $this->assertEquals('S0123/0001/2023', $applicant->getFormFourIndexNumber());
        $this->assertEquals('S0123/0001/2025', $applicant->getFormSixIndexNumber());
        $this->assertEquals('Tanzanian', $applicant->getNationality());
        $this->assertEquals(2000, $applicant->getYearOfBirth());
        $this->assertEquals('Government', $applicant->getApplicantCategory());
        $this->assertEquals('+255123456789', $applicant->getPhoneNumber());
        $this->assertEquals('john.doe@example.com', $applicant->getEmailAddress());
        $this->assertFalse($applicant->getDisabilityStatus());
    }
    
    public function testFluentInterface()
    {
        $applicant = (new Applicant())
            ->setFirstName('Jane')
            ->setMiddleName('Marie')
            ->setSurname('Smith')
            ->setGender('F')
            ->setFormFourIndexNumber('S0124/0002/2023')
            ->setNationality('Tanzanian')
            ->setYearOfBirth(1999)
            ->setApplicantCategory('Private');
        
        $this->assertEquals('Jane', $applicant->getFirstName());
        $this->assertEquals('Marie', $applicant->getMiddleName());
        $this->assertEquals('Smith', $applicant->getSurname());
        $this->assertEquals('Jane Marie Smith', $applicant->getFullName());
        $this->assertEquals('F', $applicant->getGender());
        $this->assertEquals('S0124/0002/2023', $applicant->getFormFourIndexNumber());
        $this->assertEquals('Tanzanian', $applicant->getNationality());
        $this->assertEquals(1999, $applicant->getYearOfBirth());
        $this->assertEquals('Private', $applicant->getApplicantCategory());
    }
    
    public function testUtilityMethods()
    {
        $localApplicant = new Applicant(['nationality' => 'Tanzanian']);
        $this->assertTrue($localApplicant->isLocal());
        $this->assertFalse($localApplicant->isForeign());
        
        $foreignApplicant = new Applicant(['nationality' => 'Kenyan']);
        $this->assertFalse($foreignApplicant->isLocal());
        $this->assertTrue($foreignApplicant->isForeign());
        
        $applicantWithFormSix = new Applicant(['form_six_index_number' => 'S0123/0001/2025']);
        $this->assertTrue($applicantWithFormSix->hasFormSixResults());
        
        $applicantWithoutFormSix = new Applicant();
        $this->assertFalse($applicantWithoutFormSix->hasFormSixResults());
        
        $applicantWithAvn = new Applicant(['avn' => 'AVN123456']);
        $this->assertTrue($applicantWithAvn->hasDiplomaResults());
        
        $applicantWithoutAvn = new Applicant();
        $this->assertFalse($applicantWithoutAvn->hasDiplomaResults());
        
        $applicantWithDisability = new Applicant(['disability_status' => true]);
        $this->assertTrue($applicantWithDisability->hasDisability());
        
        $applicantWithoutDisability = new Applicant(['disability_status' => false]);
        $this->assertFalse($applicantWithoutDisability->hasDisability());
    }
    
    public function testValidationWithValidData()
    {
        $data = $this->getTestApplicantData();
        $applicant = new Applicant($data);
        
        $errors = $applicant->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationWithMissingRequiredFields()
    {
        $applicant = new Applicant([
            'middle_name' => 'Michael',
            'gender' => 'M'
        ]);
        
        $errors = $applicant->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Field \'first_name\' is required', $errors);
        $this->assertContains('Field \'surname\' is required', $errors);
        $this->assertContains('Field \'form_four_index_number\' is required', $errors);
        $this->assertContains('Field \'nationality\' is required', $errors);
        $this->assertContains('Field \'year_of_birth\' is required', $errors);
        $this->assertContains('Field \'applicant_category\' is required', $errors);
    }
    
    public function testValidationWithInvalidGender()
    {
        $data = $this->getTestApplicantData();
        $data['gender'] = 'Invalid';
        
        $applicant = new Applicant($data);
        $errors = $applicant->validate();
        
        $this->assertContains('Gender must be M, F, Male, or Female', $errors);
    }
    
    public function testValidationWithInvalidYearOfBirth()
    {
        $data = $this->getTestApplicantData();
        $data['year_of_birth'] = 1900;
        
        $applicant = new Applicant($data);
        $errors = $applicant->validate();
        
        $this->assertContains('Year of birth must be between 1950 and ' . date('Y'), $errors);
    }
    
    public function testValidationWithInvalidEmail()
    {
        $data = $this->getTestApplicantData();
        $data['email_address'] = 'invalid-email';
        
        $applicant = new Applicant($data);
        $errors = $applicant->validate();
        
        $this->assertContains('Invalid email address format', $errors);
    }
    
    public function testValidationWithInvalidPhoneNumber()
    {
        $data = $this->getTestApplicantData();
        $data['phone_number'] = 'invalid-phone';
        
        $applicant = new Applicant($data);
        $errors = $applicant->validate();
        
        $this->assertContains('Invalid phone number format', $errors);
    }
    
    public function testValidationWithInvalidFormFourIndexNumber()
    {
        $data = $this->getTestApplicantData();
        $data['form_four_index_number'] = 'INVALID';
        
        $applicant = new Applicant($data);
        $errors = $applicant->validate();
        
        $this->assertContains('Form four index number must be in format: S0123/0001/2023', $errors);
    }
    
    public function testGetFullNameWithOnlyFirstAndLastName()
    {
        $applicant = new Applicant([
            'first_name' => 'John',
            'surname' => 'Doe'
        ]);
        
        $this->assertEquals('John Doe', $applicant->getFullName());
    }
    
    public function testGetFullNameWithAllNames()
    {
        $applicant = new Applicant([
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe'
        ]);
        
        $this->assertEquals('John Michael Doe', $applicant->getFullName());
    }
    
    public function testArraysAreProperlyCast()
    {
        $applicant = new Applicant([
            'other_form_four_index_numbers' => ['S0124/0001/2023', 'S0125/0001/2023'],
            'other_form_six_index_numbers' => ['S0124/0001/2025']
        ]);
        
        $this->assertIsArray($applicant->getOtherFormFourIndexNumbers());
        $this->assertIsArray($applicant->getOtherFormSixIndexNumbers());
        $this->assertEquals(['S0124/0001/2023', 'S0125/0001/2023'], $applicant->getOtherFormFourIndexNumbers());
        $this->assertEquals(['S0124/0001/2025'], $applicant->getOtherFormSixIndexNumbers());
    }
    
    public function testDisabilityStatusIsProperlyCast()
    {
        $applicant = new Applicant(['disability_status' => 'true']);
        
        $this->assertIsBool($applicant->getDisabilityStatus());
        $this->assertTrue($applicant->getDisabilityStatus());
    }
}