<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Request;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;

class CheckStatusRequestTest extends TestCase
{
    public function testCanCreateForSingleApplicant()
    {
        $request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023');
        
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals('/applicants/checkStatus', $request->getEndpoint());
        $this->assertEquals('POST', $request->getMethod());
    }
    
    public function testCanCreateForSingleApplicantWithOptionalFields()
    {
        $request = CheckStatusRequest::forSingleApplicant(
            'S0123/0001/2023',
            'S0123/0001/2025',
            'AVN123456'
        );
        
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals('S0123/0001/2025', $request->getFormSixIndexNumber());
        $this->assertEquals('AVN123456', $request->getAvn());
    }
    
    public function testCanCreateForMultipleApplicants()
    {
        $indexNumbers = ['S0123/0001/2023', 'S0124/0002/2023', 'S0125/0003/2023'];
        $request = CheckStatusRequest::forMultipleApplicants($indexNumbers);
        
        $this->assertEquals($indexNumbers, $request->getFormFourIndexNumbers());
        $this->assertEquals('/applicants/checkStatus', $request->getEndpoint());
        $this->assertEquals('POST', $request->getMethod());
    }
    
    public function testCanCreateForApplicantWithMultipleResults()
    {
        $request = CheckStatusRequest::forApplicantWithMultipleResults(
            'S0123/0001/2023',
            ['S0124/0001/2023', 'S0125/0001/2023'],
            ['S0123/0001/2025']
        );
        
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals(['S0124/0001/2023', 'S0125/0001/2023'], $request->getOtherFormFourIndexNumbers());
        $this->assertEquals(['S0123/0001/2025'], $request->getOtherFormSixIndexNumbers());
    }
    
    public function testCanSetAllFields()
    {
        $request = new CheckStatusRequest();
        
        $request->setFormFourIndexNumber('S0123/0001/2023')
                ->setFormSixIndexNumber('S0123/0001/2025')
                ->setAvn('AVN123456')
                ->setFormFourIndexNumbers(['S0124/0001/2023'])
                ->setOtherFormFourIndexNumbers(['S0125/0001/2023'])
                ->setOtherFormSixIndexNumbers(['S0126/0001/2025'])
                ->setCheckAllRounds(true);
        
        $this->assertEquals('S0123/0001/2023', $request->getFormFourIndexNumber());
        $this->assertEquals('S0123/0001/2025', $request->getFormSixIndexNumber());
        $this->assertEquals('AVN123456', $request->getAvn());
        $this->assertEquals(['S0124/0001/2023'], $request->getFormFourIndexNumbers());
        $this->assertEquals(['S0125/0001/2023'], $request->getOtherFormFourIndexNumbers());
        $this->assertEquals(['S0126/0001/2025'], $request->getOtherFormSixIndexNumbers());
        $this->assertTrue($request->getCheckAllRounds());
    }
    
    public function testPrepareForApiRemovesNullValues()
    {
        $request = new CheckStatusRequest([
            'form_four_index_number' => 'S0123/0001/2023',
            'form_six_index_number' => null,
            'avn' => 'AVN123456'
        ]);
        
        $prepared = $request->prepareForApi();
        
        $this->assertArrayHasKey('form_four_index_number', $prepared);
        $this->assertArrayHasKey('avn', $prepared);
        $this->assertArrayNotHasKey('form_six_index_number', $prepared);
    }
    
    public function testValidationPassesWithValidSingleApplicant()
    {
        $request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023');
        
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationPassesWithValidMultipleApplicants()
    {
        $request = CheckStatusRequest::forMultipleApplicants(['S0123/0001/2023', 'S0124/0002/2023']);
        
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
    
    public function testValidationFailsWithoutAnyIndexNumber()
    {
        $request = new CheckStatusRequest();
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Either form_four_index_number or form_four_index_numbers must be provided', $errors);
    }
    
    public function testValidationFailsWithInvalidFormFourIndexFormat()
    {
        $request = CheckStatusRequest::forSingleApplicant('INVALID_FORMAT');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Form four index number must be in format: S0123/0001/2023', $errors);
    }
    
    public function testValidationFailsWithInvalidFormSixIndexFormat()
    {
        $request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023', 'INVALID_FORMAT');
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Form six index number must be in format: S0123/0001/2025', $errors);
    }
    
    public function testValidationFailsWithInvalidMultipleIndexNumbers()
    {
        $request = CheckStatusRequest::forMultipleApplicants(['S0123/0001/2023', 'INVALID_FORMAT']);
        
        $errors = $request->validate();
        $this->assertNotEmpty($errors);
        $this->assertContains('Invalid form four index number format: INVALID_FORMAT', $errors);
    }
    
    public function testArraysAreProperlyCast()
    {
        $request = new CheckStatusRequest([
            'form_four_index_numbers' => ['S0123/0001/2023', 'S0124/0002/2023'],
            'other_form_four_index_numbers' => ['S0125/0003/2023'],
            'other_form_six_index_numbers' => ['S0126/0004/2025'],
            'check_all_rounds' => 'true'
        ]);
        
        $this->assertIsArray($request->getFormFourIndexNumbers());
        $this->assertIsArray($request->getOtherFormFourIndexNumbers());
        $this->assertIsArray($request->getOtherFormSixIndexNumbers());
        $this->assertIsBool($request->getCheckAllRounds());
        
        $this->assertEquals(['S0123/0001/2023', 'S0124/0002/2023'], $request->getFormFourIndexNumbers());
        $this->assertEquals(['S0125/0003/2023'], $request->getOtherFormFourIndexNumbers());
        $this->assertEquals(['S0126/0004/2025'], $request->getOtherFormSixIndexNumbers());
        $this->assertTrue($request->getCheckAllRounds());
    }
    
    public function testEmptyArraysReturnEmptyArrays()
    {
        $request = new CheckStatusRequest();
        
        $this->assertEquals([], $request->getFormFourIndexNumbers());
        $this->assertEquals([], $request->getOtherFormFourIndexNumbers());
        $this->assertEquals([], $request->getOtherFormSixIndexNumbers());
        $this->assertFalse($request->getCheckAllRounds());
    }
}