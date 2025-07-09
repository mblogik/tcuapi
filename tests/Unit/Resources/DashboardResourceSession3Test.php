<?php

/**
 * TCU API Client - Dashboard Resource Session 3 Unit Tests
 * 
 * Unit tests for Session 3 endpoints (3.12-3.13) of the DashboardResource class.
 * Tests cover validation, request formatting, and response handling for
 * confirmation code and rejection operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 3 DashboardResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\DashboardResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DashboardResourceSession3Test extends TestCase
{
    private DashboardResource $resource;
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
        $this->resource = new DashboardResource($this->mockClient);
    }
    
    /**
     * Test 3.12: Request Confirmation Code - Valid F4 Index Number
     */
    public function testRequestConfirmationCodeWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmation code requested successfully',
            'f4indexno' => $f4indexno,
            'confirmation_code' => 'CONF123ABC',
            'expires_at' => '2025-01-10 10:30:00',
            'can_request_again_at' => '2025-01-09 22:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/dashboard/requestConfirmationCode',
                [
                    'operation' => 'requestConfirmationCode',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->requestConfirmationCode($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.12: Request Confirmation Code - Invalid F4 Index Number
     */
    public function testRequestConfirmationCodeWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->requestConfirmationCode('invalid_f4');
    }
    
    /**
     * Test 3.12: Request Confirmation Code - Empty F4 Index Number
     */
    public function testRequestConfirmationCodeWithEmptyF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->requestConfirmationCode('');
    }
    
    /**
     * Test 3.12: Request Confirmation Code - Response Structure
     */
    public function testRequestConfirmationCodeResponseStructure(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmation code requested successfully',
            'f4indexno' => $f4indexno,
            'confirmation_code' => 'CONF123ABC',
            'code_type' => 'multiple_admission',
            'expires_at' => '2025-01-10 10:30:00',
            'can_request_again_at' => '2025-01-09 22:30:00',
            'request_count' => 1,
            'max_requests_per_day' => 3,
            'institutions' => [
                [
                    'institution_code' => 'INST001',
                    'institution_name' => 'University A',
                    'programme_code' => 'PROG001',
                    'programme_name' => 'Computer Science'
                ],
                [
                    'institution_code' => 'INST002',
                    'institution_name' => 'University B',
                    'programme_code' => 'PROG002',
                    'programme_name' => 'Engineering'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->requestConfirmationCode($f4indexno);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Confirmation code requested successfully', $result['status_description']);
        $this->assertEquals($f4indexno, $result['f4indexno']);
        $this->assertEquals('CONF123ABC', $result['confirmation_code']);
        $this->assertIsArray($result['institutions']);
        $this->assertCount(2, $result['institutions']);
    }
    
    /**
     * Test 3.12: Request Confirmation Code - Rate Limit Error
     */
    public function testRequestConfirmationCodeRateLimitError(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 429,
            'status_description' => 'Rate limit exceeded',
            'f4indexno' => $f4indexno,
            'error_details' => 'Maximum requests per day exceeded',
            'can_request_again_at' => '2025-01-10 00:00:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->requestConfirmationCode($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(429, $result['status_code']);
        $this->assertEquals('Rate limit exceeded', $result['status_description']);
    }
    
    /**
     * Test 3.13: Reject Admission - Valid Data
     */
    public function testRejectAdmissionWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Financial constraints prevent me from attending';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission rejected successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'rejected_at' => '2025-01-09 14:30:00',
            'can_restore_until' => '2025-01-16 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/dashboard/reject',
                [
                    'operation' => 'rejectAdmission',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.13: Reject Admission - Invalid F4 Index Number
     */
    public function testRejectAdmissionWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->reject('invalid_f4', 'Some reason');
    }
    
    /**
     * Test 3.13: Reject Admission - Empty Reason
     */
    public function testRejectAdmissionWithEmptyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rejection reason is required');
        
        $this->resource->reject('S0123456789', '');
    }
    
    /**
     * Test 3.13: Reject Admission - Whitespace Only Reason
     */
    public function testRejectAdmissionWithWhitespaceOnlyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rejection reason is required');
        
        $this->resource->reject('S0123456789', '   ');
    }
    
    /**
     * Test 3.13: Reject Admission - Response Structure
     */
    public function testRejectAdmissionResponseStructure(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'I have decided to pursue a different career path';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission rejected successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'rejected_at' => '2025-01-09 14:30:00',
            'can_restore_until' => '2025-01-16 14:30:00',
            'institution_code' => 'INST001',
            'programme_code' => 'PROG001',
            'programme_name' => 'Computer Science',
            'academic_year' => '2025/2026',
            'rejection_id' => 'REJ_2025_001234'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Admission rejected successfully', $result['status_description']);
        $this->assertEquals($f4indexno, $result['f4indexno']);
        $this->assertEquals($reason, $result['reason']);
        $this->assertArrayHasKey('rejected_at', $result);
        $this->assertArrayHasKey('can_restore_until', $result);
    }
    
    /**
     * Test 3.13: Reject Admission - Long Reason
     */
    public function testRejectAdmissionWithLongReason(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'I have carefully considered my options and have decided that this programme does not align with my long-term career goals. After extensive research and consultation with my family and career advisors, I believe that pursuing a different field of study would be more beneficial for my future. I deeply appreciate the opportunity that was offered to me and I respect the institution, but I must decline this admission offer at this time.';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admission rejected successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals($reason, $result['reason']);
    }
    
    /**
     * Test 3.13: Reject Admission - Already Rejected Error
     */
    public function testRejectAdmissionAlreadyRejectedError(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Changed my mind';
        $expectedResponse = [
            'status_code' => 409,
            'status_description' => 'Admission already rejected',
            'f4indexno' => $f4indexno,
            'error_details' => 'This admission has already been rejected',
            'rejected_at' => '2025-01-08 10:00:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(409, $result['status_code']);
        $this->assertEquals('Admission already rejected', $result['status_description']);
    }
    
    /**
     * Test 3.13: Reject Admission - Not Found Error
     */
    public function testRejectAdmissionNotFoundError(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Personal reasons';
        $expectedResponse = [
            'status_code' => 404,
            'status_description' => 'Admission not found',
            'f4indexno' => $f4indexno,
            'error_details' => 'No admission found for this applicant'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(404, $result['status_code']);
        $this->assertEquals('Admission not found', $result['status_description']);
    }
    
    /**
     * Test Request Confirmation Code - Request Format Validation
     */
    public function testRequestConfirmationCodeRequestFormat(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'confirmation_code' => 'CONF123ABC'
        ];
        
        // Verify the exact request format
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                $this->equalTo('/dashboard/requestConfirmationCode'),
                $this->equalTo([
                    'operation' => 'requestConfirmationCode',
                    'f4indexno' => $f4indexno
                ]),
                $this->equalTo('POST')
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->requestConfirmationCode($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Reject Admission - Request Format Validation
     */
    public function testRejectAdmissionRequestFormat(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Personal reasons';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success'
        ];
        
        // Verify the exact request format
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                $this->equalTo('/dashboard/reject'),
                $this->equalTo([
                    'operation' => 'rejectAdmission',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason
                ]),
                $this->equalTo('POST')
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reject($f4indexno, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
}