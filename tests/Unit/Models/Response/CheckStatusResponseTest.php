<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models\Response;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\Response\CheckStatusResponse;

class CheckStatusResponseTest extends TestCase
{
    public function testCanCreateResponseWithData()
    {
        $data = [
            'status_code' => 200,
            'status_description' => 'Success',
            'message' => 'Request processed successfully',
            'form_four_index_number' => 'S0123/0001/2023',
            'admission_status' => 'admitted',
            'admission_year' => '2023',
            'institution_code' => 'UDSM',
            'institution_name' => 'University of Dar es Salaam',
            'programme_code' => 'BSCS001',
            'programme_name' => 'Bachelor of Science in Computer Science',
            'confirmation_status' => 'confirmed',
            'graduation_status' => 'not_graduated',
            'discontinuation_status' => 'active',
            'timestamp' => '2025-01-09 10:00:00'
        ];
        
        $response = new CheckStatusResponse($data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getStatusDescription());
        $this->assertEquals('Request processed successfully', $response->getMessage());
        $this->assertEquals('S0123/0001/2023', $response->getFormFourIndexNumber());
        $this->assertEquals('admitted', $response->getAdmissionStatus());
        $this->assertEquals('2023', $response->getAdmissionYear());
        $this->assertEquals('UDSM', $response->getInstitutionCode());
        $this->assertEquals('University of Dar es Salaam', $response->getInstitutionName());
        $this->assertEquals('BSCS001', $response->getProgrammeCode());
        $this->assertEquals('Bachelor of Science in Computer Science', $response->getProgrammeName());
        $this->assertEquals('confirmed', $response->getConfirmationStatus());
        $this->assertEquals('not_graduated', $response->getGraduationStatus());
        $this->assertEquals('active', $response->getDiscontinuationStatus());
        $this->assertEquals('2025-01-09 10:00:00', $response->getTimestamp());
    }
    
    public function testIsSuccessReturnsTrueForSuccessfulResponse()
    {
        $response = new CheckStatusResponse(['status_code' => 200]);
        $this->assertTrue($response->isSuccess());
        
        $response = new CheckStatusResponse(['status_code' => 201]);
        $this->assertTrue($response->isSuccess());
        
        $response = new CheckStatusResponse(['status_code' => 299]);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testIsSuccessReturnsFalseForErrorResponse()
    {
        $response = new CheckStatusResponse(['status_code' => 400]);
        $this->assertFalse($response->isSuccess());
        
        $response = new CheckStatusResponse(['status_code' => 500]);
        $this->assertFalse($response->isSuccess());
    }
    
    public function testIsErrorReturnsTrueForErrorResponse()
    {
        $response = new CheckStatusResponse(['status_code' => 400]);
        $this->assertTrue($response->isError());
        
        $response = new CheckStatusResponse(['status_code' => 500]);
        $this->assertTrue($response->isError());
    }
    
    public function testIsErrorReturnsFalseForSuccessfulResponse()
    {
        $response = new CheckStatusResponse(['status_code' => 200]);
        $this->assertFalse($response->isError());
    }
    
    public function testHasAdmissionReturnsTrueWhenAdmissionStatusExists()
    {
        $response = new CheckStatusResponse(['admission_status' => 'admitted']);
        $this->assertTrue($response->hasAdmission());
        
        $response = new CheckStatusResponse(['admission_status' => 'discontinued']);
        $this->assertTrue($response->hasAdmission());
    }
    
    public function testHasAdmissionReturnsFalseWhenAdmissionStatusEmpty()
    {
        $response = new CheckStatusResponse(['admission_status' => '']);
        $this->assertFalse($response->hasAdmission());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->hasAdmission());
    }
    
    public function testIsAdmittedReturnsTrueForAdmittedStatus()
    {
        $response = new CheckStatusResponse(['admission_status' => 'admitted']);
        $this->assertTrue($response->isAdmitted());
        
        $response = new CheckStatusResponse(['admission_status' => 'ADMITTED']);
        $this->assertTrue($response->isAdmitted());
    }
    
    public function testIsAdmittedReturnsFalseForNonAdmittedStatus()
    {
        $response = new CheckStatusResponse(['admission_status' => 'not_admitted']);
        $this->assertFalse($response->isAdmitted());
        
        $response = new CheckStatusResponse(['admission_status' => 'discontinued']);
        $this->assertFalse($response->isAdmitted());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->isAdmitted());
    }
    
    public function testIsConfirmedReturnsTrueForConfirmedStatus()
    {
        $response = new CheckStatusResponse(['confirmation_status' => 'confirmed']);
        $this->assertTrue($response->isConfirmed());
        
        $response = new CheckStatusResponse(['confirmation_status' => 'CONFIRMED']);
        $this->assertTrue($response->isConfirmed());
    }
    
    public function testIsConfirmedReturnsFalseForNonConfirmedStatus()
    {
        $response = new CheckStatusResponse(['confirmation_status' => 'not_confirmed']);
        $this->assertFalse($response->isConfirmed());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->isConfirmed());
    }
    
    public function testHasGraduatedReturnsTrueForGraduatedStatus()
    {
        $response = new CheckStatusResponse(['graduation_status' => 'graduated']);
        $this->assertTrue($response->hasGraduated());
        
        $response = new CheckStatusResponse(['graduation_status' => 'GRADUATED']);
        $this->assertTrue($response->hasGraduated());
    }
    
    public function testHasGraduatedReturnsFalseForNonGraduatedStatus()
    {
        $response = new CheckStatusResponse(['graduation_status' => 'not_graduated']);
        $this->assertFalse($response->hasGraduated());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->hasGraduated());
    }
    
    public function testIsDiscontinuedReturnsTrueForDiscontinuedStatus()
    {
        $response = new CheckStatusResponse(['discontinuation_status' => 'discontinued']);
        $this->assertTrue($response->isDiscontinued());
        
        $response = new CheckStatusResponse(['discontinuation_status' => 'DISCONTINUED']);
        $this->assertTrue($response->isDiscontinued());
    }
    
    public function testIsDiscontinuedReturnsFalseForNonDiscontinuedStatus()
    {
        $response = new CheckStatusResponse(['discontinuation_status' => 'active']);
        $this->assertFalse($response->isDiscontinued());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->isDiscontinued());
    }
    
    public function testIsTransferredReturnsTrueWhenTransferStatusExists()
    {
        $response = new CheckStatusResponse(['transfer_status' => 'transferred']);
        $this->assertTrue($response->isTransferred());
        
        $response = new CheckStatusResponse(['transfer_status' => 'internal_transfer']);
        $this->assertTrue($response->isTransferred());
    }
    
    public function testIsTransferredReturnsFalseWhenTransferStatusEmpty()
    {
        $response = new CheckStatusResponse(['transfer_status' => '']);
        $this->assertFalse($response->isTransferred());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->isTransferred());
    }
    
    public function testHasMultipleAdmissionsReturnsTrueWhenMultipleAdmissionsExist()
    {
        $response = new CheckStatusResponse(['multiple_admissions' => [
            ['institution' => 'UDSM', 'programme' => 'BSCS001'],
            ['institution' => 'UDOM', 'programme' => 'BSIT002']
        ]]);
        
        $this->assertTrue($response->hasMultipleAdmissions());
    }
    
    public function testHasMultipleAdmissionsReturnsFalseWhenNoMultipleAdmissions()
    {
        $response = new CheckStatusResponse(['multiple_admissions' => []]);
        $this->assertFalse($response->hasMultipleAdmissions());
        
        $response = new CheckStatusResponse([]);
        $this->assertFalse($response->hasMultipleAdmissions());
    }
    
    public function testCanApplyLogic()
    {
        // Can apply when no admission
        $response = new CheckStatusResponse([]);
        $this->assertTrue($response->canApply());
        
        // Can apply when graduated
        $response = new CheckStatusResponse([
            'admission_status' => 'admitted',
            'graduation_status' => 'graduated'
        ]);
        $this->assertTrue($response->canApply());
        
        // Can apply when discontinued
        $response = new CheckStatusResponse([
            'admission_status' => 'admitted',
            'discontinuation_status' => 'discontinued'
        ]);
        $this->assertTrue($response->canApply());
        
        // Cannot apply when currently admitted and active
        $response = new CheckStatusResponse([
            'admission_status' => 'admitted',
            'graduation_status' => 'not_graduated',
            'discontinuation_status' => 'active'
        ]);
        $this->assertFalse($response->canApply());
    }
    
    public function testGetStatusSummary()
    {
        $response = new CheckStatusResponse([
            'admission_status' => 'admitted',
            'confirmation_status' => 'confirmed',
            'graduation_status' => 'not_graduated',
            'discontinuation_status' => 'active',
            'multiple_admissions' => [
                ['institution' => 'UDSM', 'programme' => 'BSCS001']
            ]
        ]);
        
        $summary = $response->getStatusSummary();
        
        $this->assertIsArray($summary);
        $this->assertTrue($summary['has_admission']);
        $this->assertTrue($summary['is_admitted']);
        $this->assertTrue($summary['is_confirmed']);
        $this->assertFalse($summary['has_graduated']);
        $this->assertFalse($summary['is_discontinued']);
        $this->assertFalse($summary['is_transferred']);
        $this->assertTrue($summary['has_multiple_admissions']);
        $this->assertFalse($summary['can_apply']);
    }
    
    public function testGetMultipleAdmissions()
    {
        $multipleAdmissions = [
            ['institution' => 'UDSM', 'programme' => 'BSCS001'],
            ['institution' => 'UDOM', 'programme' => 'BSIT002']
        ];
        
        $response = new CheckStatusResponse(['multiple_admissions' => $multipleAdmissions]);
        
        $this->assertEquals($multipleAdmissions, $response->getMultipleAdmissions());
    }
    
    public function testGetMultipleAdmissionsReturnsEmptyArrayWhenNotSet()
    {
        $response = new CheckStatusResponse([]);
        
        $this->assertEquals([], $response->getMultipleAdmissions());
    }
}