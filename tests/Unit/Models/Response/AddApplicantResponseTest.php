<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Response;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Response\AddApplicantResponse;

class AddApplicantResponseTest extends TestCase
{
    public function testCanCreateResponseWithData()
    {
        $data = [
            'status_code' => 200,
            'status_description' => 'Success',
            'message' => 'Applicant added successfully',
            'applicant_id' => 'APP123456',
            'form_four_index_number' => 'S0123/0001/2023',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'date_added' => '2025-01-09 10:00:00',
            'timestamp' => '2025-01-09 10:00:00'
        ];
        
        $response = new AddApplicantResponse($data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getStatusDescription());
        $this->assertEquals('Applicant added successfully', $response->getMessage());
        $this->assertEquals('APP123456', $response->getApplicantId());
        $this->assertEquals('S0123/0001/2023', $response->getFormFourIndexNumber());
        $this->assertEquals('John', $response->getFirstName());
        $this->assertEquals('Michael', $response->getMiddleName());
        $this->assertEquals('Doe', $response->getSurname());
        $this->assertEquals('M', $response->getGender());
        $this->assertEquals('Tanzanian', $response->getNationality());
        $this->assertEquals(2000, $response->getYearOfBirth());
        $this->assertEquals('Government', $response->getApplicantCategory());
        $this->assertEquals('INST001', $response->getInstitutionCode());
        $this->assertEquals('2025-01-09 10:00:00', $response->getDateAdded());
        $this->assertEquals('2025-01-09 10:00:00', $response->getTimestamp());
    }
    
    public function testIsSuccessReturnsTrueForSuccessfulResponse()
    {
        $response = new AddApplicantResponse(['status_code' => 200]);
        $this->assertTrue($response->isSuccess());
        
        $response = new AddApplicantResponse(['status_code' => 201]);
        $this->assertTrue($response->isSuccess());
        
        $response = new AddApplicantResponse(['status_code' => 299]);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testIsSuccessReturnsFalseForErrorResponse()
    {
        $response = new AddApplicantResponse(['status_code' => 400]);
        $this->assertFalse($response->isSuccess());
        
        $response = new AddApplicantResponse(['status_code' => 500]);
        $this->assertFalse($response->isSuccess());
    }
    
    public function testIsErrorReturnsTrueForErrorResponse()
    {
        $response = new AddApplicantResponse(['status_code' => 400]);
        $this->assertTrue($response->isError());
        
        $response = new AddApplicantResponse(['status_code' => 500]);
        $this->assertTrue($response->isError());
    }
    
    public function testIsErrorReturnsFalseForSuccessfulResponse()
    {
        $response = new AddApplicantResponse(['status_code' => 200]);
        $this->assertFalse($response->isError());
    }
    
    public function testHasApplicantIdReturnsTrueWhenApplicantIdExists()
    {
        $response = new AddApplicantResponse(['applicant_id' => 'APP123456']);
        $this->assertTrue($response->hasApplicantId());
        
        $response = new AddApplicantResponse(['applicant_id' => '']);
        $this->assertFalse($response->hasApplicantId());
        
        $response = new AddApplicantResponse([]);
        $this->assertFalse($response->hasApplicantId());
    }
    
    public function testGetFullName()
    {
        $response = new AddApplicantResponse([
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe'
        ]);
        
        $this->assertEquals('John Michael Doe', $response->getFullName());
        
        $responseWithoutMiddleName = new AddApplicantResponse([
            'first_name' => 'John',
            'surname' => 'Doe'
        ]);
        
        $this->assertEquals('John Doe', $responseWithoutMiddleName->getFullName());
    }
    
    public function testGetFullNameHandlesEmptyNames()
    {
        $response = new AddApplicantResponse([
            'first_name' => '',
            'surname' => ''
        ]);
        
        $this->assertEquals('', $response->getFullName());
        
        $responseWithOnlyFirstName = new AddApplicantResponse([
            'first_name' => 'John'
        ]);
        
        $this->assertEquals('John', $responseWithOnlyFirstName->getFullName());
    }
    
    public function testIsLocalApplicant()
    {
        $localResponse = new AddApplicantResponse(['nationality' => 'Tanzanian']);
        $this->assertTrue($localResponse->isLocalApplicant());
        
        $foreignResponse = new AddApplicantResponse(['nationality' => 'Kenyan']);
        $this->assertFalse($foreignResponse->isLocalApplicant());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isLocalApplicant());
    }
    
    public function testIsForeignApplicant()
    {
        $foreignResponse = new AddApplicantResponse(['nationality' => 'Kenyan']);
        $this->assertTrue($foreignResponse->isForeignApplicant());
        
        $localResponse = new AddApplicantResponse(['nationality' => 'Tanzanian']);
        $this->assertFalse($localResponse->isForeignApplicant());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isForeignApplicant());
    }
    
    public function testIsGovernmentApplicant()
    {
        $governmentResponse = new AddApplicantResponse(['applicant_category' => 'Government']);
        $this->assertTrue($governmentResponse->isGovernmentApplicant());
        
        $privateResponse = new AddApplicantResponse(['applicant_category' => 'Private']);
        $this->assertFalse($privateResponse->isGovernmentApplicant());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isGovernmentApplicant());
    }
    
    public function testIsPrivateApplicant()
    {
        $privateResponse = new AddApplicantResponse(['applicant_category' => 'Private']);
        $this->assertTrue($privateResponse->isPrivateApplicant());
        
        $governmentResponse = new AddApplicantResponse(['applicant_category' => 'Government']);
        $this->assertFalse($governmentResponse->isPrivateApplicant());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isPrivateApplicant());
    }
    
    public function testIsSpecialApplicant()
    {
        $specialResponse = new AddApplicantResponse(['applicant_category' => 'Special']);
        $this->assertTrue($specialResponse->isSpecialApplicant());
        
        $governmentResponse = new AddApplicantResponse(['applicant_category' => 'Government']);
        $this->assertFalse($governmentResponse->isSpecialApplicant());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isSpecialApplicant());
    }
    
    public function testIsMale()
    {
        $maleResponse = new AddApplicantResponse(['gender' => 'M']);
        $this->assertTrue($maleResponse->isMale());
        
        $maleResponse2 = new AddApplicantResponse(['gender' => 'Male']);
        $this->assertTrue($maleResponse2->isMale());
        
        $femaleResponse = new AddApplicantResponse(['gender' => 'F']);
        $this->assertFalse($femaleResponse->isMale());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isMale());
    }
    
    public function testIsFemale()
    {
        $femaleResponse = new AddApplicantResponse(['gender' => 'F']);
        $this->assertTrue($femaleResponse->isFemale());
        
        $femaleResponse2 = new AddApplicantResponse(['gender' => 'Female']);
        $this->assertTrue($femaleResponse2->isFemale());
        
        $maleResponse = new AddApplicantResponse(['gender' => 'M']);
        $this->assertFalse($maleResponse->isFemale());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->isFemale());
    }
    
    public function testGetAge()
    {
        $currentYear = date('Y');
        $response = new AddApplicantResponse(['year_of_birth' => $currentYear - 23]);
        
        $this->assertEquals(23, $response->getAge());
        
        $responseWithoutBirthYear = new AddApplicantResponse([]);
        $this->assertNull($responseWithoutBirthYear->getAge());
    }
    
    public function testWasAddedToday()
    {
        $todayResponse = new AddApplicantResponse(['date_added' => date('Y-m-d H:i:s')]);
        $this->assertTrue($todayResponse->wasAddedToday());
        
        $yesterdayResponse = new AddApplicantResponse(['date_added' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        $this->assertFalse($yesterdayResponse->wasAddedToday());
        
        $unknownResponse = new AddApplicantResponse([]);
        $this->assertFalse($unknownResponse->wasAddedToday());
    }
    
    public function testGetDateAddedFormatted()
    {
        $response = new AddApplicantResponse(['date_added' => '2025-01-09 10:30:45']);
        
        $this->assertEquals('09/01/2025', $response->getDateAddedFormatted());
        $this->assertEquals('09-01-2025', $response->getDateAddedFormatted('d-m-Y'));
        $this->assertEquals('2025-01-09 10:30:45', $response->getDateAddedFormatted('Y-m-d H:i:s'));
        
        $responseWithoutDate = new AddApplicantResponse([]);
        $this->assertNull($responseWithoutDate->getDateAddedFormatted());
    }
    
    public function testGetApplicantSummary()
    {
        $response = new AddApplicantResponse([
            'applicant_id' => 'APP123456',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'form_four_index_number' => 'S0123/0001/2023'
        ]);
        
        $summary = $response->getApplicantSummary();
        
        $this->assertIsArray($summary);
        $this->assertEquals('APP123456', $summary['applicant_id']);
        $this->assertEquals('John Michael Doe', $summary['full_name']);
        $this->assertEquals('M', $summary['gender']);
        $this->assertEquals('Tanzanian', $summary['nationality']);
        $this->assertEquals(2000, $summary['year_of_birth']);
        $this->assertEquals('Government', $summary['applicant_category']);
        $this->assertEquals('INST001', $summary['institution_code']);
        $this->assertEquals('S0123/0001/2023', $summary['form_four_index_number']);
        $this->assertTrue($summary['is_local']);
        $this->assertFalse($summary['is_foreign']);
        $this->assertTrue($summary['is_government']);
        $this->assertFalse($summary['is_private']);
        $this->assertFalse($summary['is_special']);
        $this->assertTrue($summary['is_male']);
        $this->assertFalse($summary['is_female']);
    }
    
    public function testHasValidationErrors()
    {
        $responseWithErrors = new AddApplicantResponse([
            'status_code' => 400,
            'validation_errors' => [
                'first_name' => 'First name is required',
                'surname' => 'Surname is required'
            ]
        ]);
        
        $this->assertTrue($responseWithErrors->hasValidationErrors());
        
        $responseWithoutErrors = new AddApplicantResponse(['status_code' => 200]);
        $this->assertFalse($responseWithoutErrors->hasValidationErrors());
    }
    
    public function testGetValidationErrors()
    {
        $validationErrors = [
            'first_name' => 'First name is required',
            'surname' => 'Surname is required'
        ];
        
        $response = new AddApplicantResponse(['validation_errors' => $validationErrors]);
        
        $this->assertEquals($validationErrors, $response->getValidationErrors());
        
        $responseWithoutErrors = new AddApplicantResponse([]);
        $this->assertEquals([], $responseWithoutErrors->getValidationErrors());
    }
    
    public function testCastingWorksCorrectly()
    {
        $response = new AddApplicantResponse([
            'year_of_birth' => '2000',
            'status_code' => '200'
        ]);
        
        $this->assertIsInt($response->getYearOfBirth());
        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(2000, $response->getYearOfBirth());
        $this->assertEquals(200, $response->getStatusCode());
    }
}