<?php

/**
 * TCU API Client - Applicant Resource Session 1 Unit Tests
 * 
 * Unit tests for Session 1 endpoints (3.1-3.3) of the ApplicantResource class.
 * Tests cover validation, request formatting, and response handling for
 * core applicant operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 1 ApplicantResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\ApplicantResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ApplicantResourceSession1Test extends TestCase
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
     * Test 3.1: Check Applicant Status - Valid F4 Index Number
     */
    public function testCheckStatusWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'f4indexno' => $f4indexno,
            'applicant_status' => 'eligible'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/checkStatus',
                [
                    'f4indexno' => $f4indexno,
                    'operation' => 'checkStatus'
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->checkStatus($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.1: Check Applicant Status - With F6 Index Number
     */
    public function testCheckStatusWithF6IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $f6indexno = 'S0123456790';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'f4indexno' => $f4indexno,
            'f6indexno' => $f6indexno,
            'applicant_status' => 'eligible'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/checkStatus',
                [
                    'f4indexno' => $f4indexno,
                    'f6indexno' => $f6indexno,
                    'operation' => 'checkStatus'
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->checkStatus($f4indexno, $f6indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.1: Check Applicant Status - With AVN
     */
    public function testCheckStatusWithAVN(): void
    {
        $f4indexno = 'S0123456789';
        $avn = 'AVN123456';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'f4indexno' => $f4indexno,
            'avn' => $avn,
            'applicant_status' => 'eligible'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/checkStatus',
                [
                    'f4indexno' => $f4indexno,
                    'avn' => $avn,
                    'operation' => 'checkStatus'
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->checkStatus($f4indexno, null, $avn);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.1: Check Applicant Status - Invalid F4 Index Number
     */
    public function testCheckStatusWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->checkStatus('invalid_f4');
    }
    
    /**
     * Test 3.1: Check Applicant Status - Invalid F6 Index Number
     */
    public function testCheckStatusWithInvalidF6IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F6 Index Number format');
        
        $this->resource->checkStatus('S0123456789', 'invalid_f6');
    }
    
    /**
     * Test 3.1: Check Applicant Status - Invalid AVN
     */
    public function testCheckStatusWithInvalidAVN(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid AVN format');
        
        $this->resource->checkStatus('S0123456789', null, 'invalid_avn');
    }
    
    /**
     * Test 3.2: Add Applicant - Valid Data
     */
    public function testAddApplicantWithValidData(): void
    {
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'f4indexno' => 'S0123456789',
            'f6indexno' => 'S0123456790',
            'nationality' => 'Tanzanian',
            'year' => 2000,
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Applicant added successfully',
            'f4indexno' => 'S0123456789'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/add',
                [
                    'operation' => 'addApplicant',
                    'applicant' => $applicantData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->add($applicantData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.2: Add Applicant - Missing Required Fields
     */
    public function testAddApplicantWithMissingRequiredFields(): void
    {
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael'
            // Missing surname, gender, f4indexno, etc.
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Required fields missing');
        
        $this->resource->add($applicantData);
    }
    
    /**
     * Test 3.2: Add Applicant - Invalid Email
     */
    public function testAddApplicantWithInvalidEmail(): void
    {
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'f4indexno' => 'S0123456789',
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'email' => 'invalid-email'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->add($applicantData);
    }
    
    /**
     * Test 3.2: Add Applicant - Invalid Phone Number
     */
    public function testAddApplicantWithInvalidPhoneNumber(): void
    {
        $applicantData = [
            'firstname' => 'John',
            'middlename' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'f4indexno' => 'S0123456789',
            'applicant_category' => 'Government',
            'institution_code' => 'INST001',
            'phone' => 'invalid-phone'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid phone number format');
        
        $this->resource->add($applicantData);
    }
    
    /**
     * Test 3.3: Submit Programme Choices - Valid Data
     */
    public function testSubmitProgrammeChoicesWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $programmeChoices = [
            [
                'programme_code' => 'PROG001',
                'priority' => 1
            ],
            [
                'programme_code' => 'PROG002',
                'priority' => 2
            ]
        ];
        $contactDetails = [
            'mobile' => '+255712345678',
            'email' => 'john.doe@example.com'
        ];
        $additionalData = [
            'admission_status' => 'selected',
            'programme_of_admission' => 'PROG001'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programme choices submitted successfully',
            'f4indexno' => $f4indexno
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/submitProgramme',
                [
                    'operation' => 'submitProgrammeChoices',
                    'f4indexno' => $f4indexno,
                    'programme_choices' => $programmeChoices,
                    'contact_details' => $contactDetails,
                    'additional_data' => $additionalData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitProgrammeChoices($f4indexno, $programmeChoices, $contactDetails, $additionalData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.3: Submit Programme Choices - Invalid F4 Index Number
     */
    public function testSubmitProgrammeChoicesWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->submitProgrammeChoices('invalid_f4', [], []);
    }
    
    /**
     * Test 3.3: Submit Programme Choices - Invalid Programme Data
     */
    public function testSubmitProgrammeChoicesWithInvalidProgrammeData(): void
    {
        $f4indexno = 'S0123456789';
        $programmeChoices = [
            [
                'programme_code' => 'PROG001'
                // Missing priority
            ]
        ];
        $contactDetails = [
            'mobile' => '+255712345678',
            'email' => 'john.doe@example.com'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Programme choice 1');
        
        $this->resource->submitProgrammeChoices($f4indexno, $programmeChoices, $contactDetails);
    }
    
    /**
     * Test 3.3: Submit Programme Choices - Invalid Email in Contact Details
     */
    public function testSubmitProgrammeChoicesWithInvalidEmailInContactDetails(): void
    {
        $f4indexno = 'S0123456789';
        $programmeChoices = [
            [
                'programme_code' => 'PROG001',
                'priority' => 1
            ]
        ];
        $contactDetails = [
            'mobile' => '+255712345678',
            'email' => 'invalid-email'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->submitProgrammeChoices($f4indexno, $programmeChoices, $contactDetails);
    }
    
    /**
     * Test 3.3: Submit Programme Choices - Invalid Mobile in Contact Details
     */
    public function testSubmitProgrammeChoicesWithInvalidMobileInContactDetails(): void
    {
        $f4indexno = 'S0123456789';
        $programmeChoices = [
            [
                'programme_code' => 'PROG001',
                'priority' => 1
            ]
        ];
        $contactDetails = [
            'mobile' => 'invalid-mobile',
            'email' => 'john.doe@example.com'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid mobile number format');
        
        $this->resource->submitProgrammeChoices($f4indexno, $programmeChoices, $contactDetails);
    }
}