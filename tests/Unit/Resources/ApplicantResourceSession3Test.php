<?php

/**
 * TCU API Client - Applicant Resource Session 3 Unit Tests
 * 
 * Unit tests for Session 3 endpoint (3.11) of the ApplicantResource class.
 * Tests cover validation, request formatting, and response handling for
 * confirmation-related applicant operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 3 ApplicantResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\ApplicantResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ApplicantResourceSession3Test extends TestCase
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
     * Test 3.11: Get Confirmed Applicants - Valid Programme Code
     */
    public function testGetConfirmedApplicantsWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmed applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'confirmation_status' => 'confirmed',
                    'confirmed_at' => '2025-01-09 10:30:00',
                    'multiple_admissions' => true,
                    'other_programmes' => ['PROG002', 'PROG003']
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'firstname' => 'Jane',
                    'surname' => 'Smith',
                    'confirmation_status' => 'confirmed',
                    'confirmed_at' => '2025-01-09 11:15:00',
                    'multiple_admissions' => true,
                    'other_programmes' => ['PROG004']
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/applicants/getConfirmed',
                [
                    'operation' => 'getConfirmedApplicants',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Invalid Programme Code
     */
    public function testGetConfirmedApplicantsWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getConfirmed('invalid_programme');
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Empty Programme Code
     */
    public function testGetConfirmedApplicantsWithEmptyProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getConfirmed('');
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Empty Results
     */
    public function testGetConfirmedApplicantsWithEmptyResults(): void
    {
        $programmeCode = 'PROG999';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'No confirmed applicants found',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertIsArray($result['confirmed_applicants']);
        $this->assertEmpty($result['confirmed_applicants']);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Response Structure
     */
    public function testGetConfirmedApplicantsResponseStructure(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmed applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'middlename' => 'Michael',
                    'surname' => 'Doe',
                    'gender' => 'M',
                    'confirmation_status' => 'confirmed',
                    'confirmed_at' => '2025-01-09 10:30:00',
                    'confirmation_code' => 'CONF123ABC',
                    'multiple_admissions' => true,
                    'other_programmes' => ['PROG002', 'PROG003'],
                    'institution_code' => 'INST001',
                    'email' => 'john.doe@example.com',
                    'phone' => '+255712345678'
                ]
            ],
            'total_confirmed' => 1,
            'total_with_multiple_admissions' => 1,
            'generated_at' => '2025-01-09 16:45:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Confirmed applicants retrieved successfully', $result['status_description']);
        $this->assertEquals($programmeCode, $result['programme_code']);
        $this->assertIsArray($result['confirmed_applicants']);
        $this->assertCount(1, $result['confirmed_applicants']);
        
        $applicant = $result['confirmed_applicants'][0];
        $this->assertEquals('S0123456789', $applicant['f4indexno']);
        $this->assertEquals('John', $applicant['firstname']);
        $this->assertEquals('confirmed', $applicant['confirmation_status']);
        $this->assertTrue($applicant['multiple_admissions']);
        $this->assertIsArray($applicant['other_programmes']);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Single Applicant
     */
    public function testGetConfirmedApplicantsSingleApplicant(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmed applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'confirmation_status' => 'confirmed',
                    'multiple_admissions' => false,
                    'other_programmes' => []
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(1, $result['confirmed_applicants']);
        $this->assertFalse($result['confirmed_applicants'][0]['multiple_admissions']);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Multiple Applicants
     */
    public function testGetConfirmedApplicantsMultipleApplicants(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmed applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'confirmation_status' => 'confirmed',
                    'multiple_admissions' => true,
                    'other_programmes' => ['PROG002']
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'firstname' => 'Jane',
                    'surname' => 'Smith',
                    'confirmation_status' => 'confirmed',
                    'multiple_admissions' => true,
                    'other_programmes' => ['PROG003', 'PROG004']
                ],
                [
                    'f4indexno' => 'S0123456791',
                    'firstname' => 'Peter',
                    'surname' => 'Johnson',
                    'confirmation_status' => 'confirmed',
                    'multiple_admissions' => false,
                    'other_programmes' => []
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(3, $result['confirmed_applicants']);
        
        // Verify each applicant has required fields
        foreach ($result['confirmed_applicants'] as $applicant) {
            $this->assertArrayHasKey('f4indexno', $applicant);
            $this->assertArrayHasKey('firstname', $applicant);
            $this->assertArrayHasKey('surname', $applicant);
            $this->assertArrayHasKey('confirmation_status', $applicant);
            $this->assertArrayHasKey('multiple_admissions', $applicant);
            $this->assertArrayHasKey('other_programmes', $applicant);
        }
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Large Dataset
     */
    public function testGetConfirmedApplicantsLargeDataset(): void
    {
        $programmeCode = 'PROG001';
        $confirmedApplicants = [];
        
        for ($i = 1; $i <= 100; $i++) {
            $confirmedApplicants[] = [
                'f4indexno' => sprintf('S0123%06d', $i),
                'firstname' => "Student$i",
                'surname' => "Lastname$i",
                'confirmation_status' => 'confirmed',
                'multiple_admissions' => ($i % 3 === 0),
                'other_programmes' => ($i % 3 === 0) ? ['PROG002', 'PROG003'] : []
            ];
        }
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Confirmed applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'confirmed_applicants' => $confirmedApplicants
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(100, $result['confirmed_applicants']);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Error Response
     */
    public function testGetConfirmedApplicantsErrorResponse(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 404,
            'status_description' => 'Programme not found',
            'error_details' => 'The specified programme code does not exist'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(404, $result['status_code']);
        $this->assertEquals('Programme not found', $result['status_description']);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Request Format Validation
     */
    public function testGetConfirmedApplicantsRequestFormat(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'confirmed_applicants' => []
        ];
        
        // Verify the exact request format
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                $this->equalTo('/applicants/getConfirmed'),
                $this->equalTo([
                    'operation' => 'getConfirmedApplicants',
                    'programme_code' => $programmeCode
                ]),
                $this->equalTo('POST')
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getConfirmed($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.11: Get Confirmed Applicants - Different Programme Codes
     */
    public function testGetConfirmedApplicantsWithDifferentProgrammeCodes(): void
    {
        $programmes = ['PROG001', 'PROG002', 'COMP001', 'ENG001', 'MED001'];
        
        foreach ($programmes as $programmeCode) {
            $expectedResponse = [
                'status_code' => 200,
                'status_description' => 'Confirmed applicants retrieved successfully',
                'programme_code' => $programmeCode,
                'confirmed_applicants' => []
            ];
            
            $this->mockClient->expects($this->once())
                ->method('makeRequest')
                ->willReturn($expectedResponse);
            
            $result = $this->resource->getConfirmed($programmeCode);
            
            $this->assertEquals($expectedResponse, $result);
            $this->assertEquals($programmeCode, $result['programme_code']);
        }
    }
}