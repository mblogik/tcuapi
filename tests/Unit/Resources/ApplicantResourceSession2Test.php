<?php

/**
 * TCU API Client - Applicant Resource Session 2 Unit Tests
 * 
 * Unit tests for Session 2 endpoints (3.6, 3.8, 3.10) of the ApplicantResource class.
 * Tests cover validation, request formatting, and response handling for
 * administrative applicant operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 2 ApplicantResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\ApplicantResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ApplicantResourceSession2Test extends TestCase
{
    private ApplicantResource $resource;
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
        $this->resource = new ApplicantResource($this->mockClient);
    }
    
    /**
     * Test 3.6: Resubmit Applicant Details - Valid Data
     */
    public function testResubmitApplicantWithValidData(): void
    {
        $applicantData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678',
            'nationality' => 'Tanzanian',
            'applicant_category' => 'Government'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Applicant details resubmitted successfully',
            'f4indexno' => 'S0123456789'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/resubmit',
                [
                    'operation' => 'resubmitApplicant',
                    'applicant' => $applicantData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->resubmit($applicantData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.6: Resubmit Applicant Details - Missing F4 Index Number
     */
    public function testResubmitApplicantWithMissingF4IndexNumber(): void
    {
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('F4 index number is required for resubmission');
        
        $this->resource->resubmit($applicantData);
    }
    
    /**
     * Test 3.6: Resubmit Applicant Details - Invalid Email
     */
    public function testResubmitApplicantWithInvalidEmail(): void
    {
        $applicantData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'email' => 'invalid-email'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->resubmit($applicantData);
    }
    
    /**
     * Test 3.6: Resubmit Applicant Details - Invalid Phone Number
     */
    public function testResubmitApplicantWithInvalidPhoneNumber(): void
    {
        $applicantData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'phone' => 'invalid-phone'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid phone number format');
        
        $this->resource->resubmit($applicantData);
    }
    
    /**
     * Test 3.8: Get Admitted Applicants - Valid Programme Code
     */
    public function testGetAdmittedApplicantsWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Admitted applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'admission_status' => 'admitted'
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'firstname' => 'Jane',
                    'surname' => 'Smith',
                    'admission_status' => 'admitted'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/getAdmitted',
                [
                    'operation' => 'getAdmittedApplicants',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getAdmitted($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.8: Get Admitted Applicants - Invalid Programme Code
     */
    public function testGetAdmittedApplicantsWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getAdmitted('invalid_programme');
    }
    
    /**
     * Test 3.8: Get Admitted Applicants - Empty Programme Code
     */
    public function testGetAdmittedApplicantsWithEmptyProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getAdmitted('');
    }
    
    /**
     * Test 3.10: Get Applicant Status - Valid Programme Code
     */
    public function testGetApplicantStatusWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Applicant status retrieved successfully',
            'programme_code' => $programmeCode,
            'applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'admission_status' => 'selected',
                    'confirmation_status' => 'confirmed'
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'firstname' => 'Jane',
                    'surname' => 'Smith',
                    'admission_status' => 'selected',
                    'confirmation_status' => 'pending'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/getStatus',
                [
                    'operation' => 'getApplicantStatus',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStatus($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.10: Get Applicant Status - Invalid Programme Code
     */
    public function testGetApplicantStatusWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getStatus('invalid_programme');
    }
    
    /**
     * Test 3.10: Get Applicant Status - Empty Programme Code
     */
    public function testGetApplicantStatusWithEmptyProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getStatus('');
    }
    
    /**
     * Test Resubmit Applicant - Response Structure
     */
    public function testResubmitApplicantResponseStructure(): void
    {
        $applicantData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'email' => 'john.doe@updated.com',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Applicant details resubmitted successfully',
            'f4indexno' => 'S0123456789',
            'updated_fields' => ['email'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->resubmit($applicantData);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Applicant details resubmitted successfully', $result['status_description']);
        $this->assertEquals('S0123456789', $result['f4indexno']);
    }
    
    /**
     * Test Get Admitted Applicants - Empty Results
     */
    public function testGetAdmittedApplicantsWithEmptyResults(): void
    {
        $programmeCode = 'PROG999';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'No admitted applicants found',
            'programme_code' => $programmeCode,
            'applicants' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getAdmitted($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertIsArray($result['applicants']);
        $this->assertEmpty($result['applicants']);
    }
    
    /**
     * Test Get Applicant Status - Multiple Statuses
     */
    public function testGetApplicantStatusWithMultipleStatuses(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Applicant status retrieved successfully',
            'programme_code' => $programmeCode,
            'applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'admission_status' => 'selected',
                    'confirmation_status' => 'confirmed'
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'admission_status' => 'selected',
                    'confirmation_status' => 'pending'
                ],
                [
                    'f4indexno' => 'S0123456791',
                    'admission_status' => 'not_selected',
                    'confirmation_status' => 'not_applicable'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStatus($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(3, $result['applicants']);
    }
}