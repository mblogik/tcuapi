<?php

/**
 * TCU API Client - Admission Resource Session 1 Unit Tests
 * 
 * Unit tests for Session 1 endpoints (3.4-3.5) of the AdmissionResource class.
 * Tests cover validation, request formatting, and response handling for
 * admission confirmation operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 1 AdmissionResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\AdmissionResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AdmissionResourceSession1Test extends TestCase
{
    private AdmissionResource $resource;
    private MockObject $mockClient;
    private Configuration $config;
    
    protected function setUp(): void
    {
        // Create mock configuration
        $this->config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'base_url' => 'https://api.tcu.go.tz',
            'timeout' => 30
        ]);
        
        // Create mock client
        $this->mockClient = $this->createMock(TCUAPIClient::class);
        $this->mockClient->method('getConfig')->willReturn($this->config);
        
        // Create resource instance
        $this->resource = new AdmissionResource($this->mockClient);
    }
    
    /**
     * Test 3.4: Confirm Applicant Selection - Valid Data
     */
    public function testConfirmAdmissionWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $confirmationCode = 'CONF123ABC';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission confirmed successfully',
            'f4indexno' => $f4indexno,
            'confirmation_code' => $confirmationCode
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/confirm',
                [
                    'operation' => 'confirmAdmission',
                    'f4indexno' => $f4indexno,
                    'confirmation_code' => $confirmationCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->confirm($f4indexno, $confirmationCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.4: Confirm Applicant Selection - Invalid F4 Index Number
     */
    public function testConfirmAdmissionWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->confirm('invalid_f4', 'CONF123ABC');
    }
    
    /**
     * Test 3.4: Confirm Applicant Selection - Invalid Confirmation Code
     */
    public function testConfirmAdmissionWithInvalidConfirmationCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid confirmation code format');
        
        $this->resource->confirm('S0123456789', 'invalid_code');
    }
    
    /**
     * Test 3.4: Confirm Applicant Selection - Empty Confirmation Code
     */
    public function testConfirmAdmissionWithEmptyConfirmationCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid confirmation code format');
        
        $this->resource->confirm('S0123456789', '');
    }
    
    /**
     * Test 3.5: Unconfirm Admission - Valid Data
     */
    public function testUnconfirmAdmissionWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Changed mind, want to attend different institution';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission unconfirmed successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/unconfirm',
                [
                    'operation' => 'unconfirmAdmission',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->unconfirm($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.5: Unconfirm Admission - Invalid F4 Index Number
     */
    public function testUnconfirmAdmissionWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->unconfirm('invalid_f4', 'Some reason');
    }
    
    /**
     * Test 3.5: Unconfirm Admission - Empty Reason
     */
    public function testUnconfirmAdmissionWithEmptyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Reason for unconfirming is required');
        
        $this->resource->unconfirm('S0123456789', '');
    }
    
    /**
     * Test 3.5: Unconfirm Admission - Whitespace Only Reason
     */
    public function testUnconfirmAdmissionWithWhitespaceOnlyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Reason for unconfirming is required');
        
        $this->resource->unconfirm('S0123456789', '   ');
    }
    
    /**
     * Test Confirm Admission - Success Response Structure
     */
    public function testConfirmAdmissionSuccessResponseStructure(): void
    {
        $f4indexno = 'S0123456789';
        $confirmationCode = 'CONF123ABC';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission confirmed successfully',
            'f4indexno' => $f4indexno,
            'confirmation_code' => $confirmationCode,
            'confirmed_at' => '2025-01-09 10:30:00',
            'institution_code' => 'INST001',
            'programme_code' => 'PROG001'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->confirm($f4indexno, $confirmationCode);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Admission confirmed successfully', $result['status_description']);
        $this->assertEquals($f4indexno, $result['f4indexno']);
        $this->assertEquals($confirmationCode, $result['confirmation_code']);
    }
    
    /**
     * Test Unconfirm Admission - Success Response Structure
     */
    public function testUnconfirmAdmissionSuccessResponseStructure(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Financial constraints';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission unconfirmed successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'unconfirmed_at' => '2025-01-09 11:45:00',
            'institution_code' => 'INST001',
            'programme_code' => 'PROG001'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->unconfirm($f4indexno, $reason);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Admission unconfirmed successfully', $result['status_description']);
        $this->assertEquals($f4indexno, $result['f4indexno']);
        $this->assertEquals($reason, $result['reason']);
    }
    
    /**
     * Test Confirm Admission - Long Confirmation Code
     */
    public function testConfirmAdmissionWithLongConfirmationCode(): void
    {
        $f4indexno = 'S0123456789';
        $confirmationCode = 'CONF123ABC456DEF';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission confirmed successfully',
            'f4indexno' => $f4indexno
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->confirm($f4indexno, $confirmationCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Unconfirm Admission - Long Reason
     */
    public function testUnconfirmAdmissionWithLongReason(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'I have decided to pursue a different career path that requires specialized training not available in the current programme. Additionally, I have received a scholarship offer from another institution that better aligns with my academic goals and financial situation.';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission unconfirmed successfully',
            'f4indexno' => $f4indexno
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->unconfirm($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
}