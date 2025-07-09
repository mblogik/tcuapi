<?php

/**
 * TCU API Client - Admission Resource Session 2 Unit Tests
 * 
 * Unit tests for Session 2 endpoint (3.9) of the AdmissionResource class.
 * Tests cover validation, request formatting, and response handling for
 * admission programme operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 2 AdmissionResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\AdmissionResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AdmissionResourceSession2Test extends TestCase
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
     * Test 3.9: Get Programmes with Admitted Candidates
     */
    public function testGetProgrammesWithAdmittedCandidates(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programmes with admitted candidates retrieved successfully',
            'programmes' => [
                [
                    'programme_code' => 'PROG001',
                    'programme_name' => 'Computer Science',
                    'admitted_count' => 50,
                    'male_count' => 30,
                    'female_count' => 20
                ],
                [
                    'programme_code' => 'PROG002',
                    'programme_name' => 'Engineering',
                    'admitted_count' => 40,
                    'male_count' => 35,
                    'female_count' => 5
                ],
                [
                    'programme_code' => 'PROG003',
                    'programme_name' => 'Medicine',
                    'admitted_count' => 25,
                    'male_count' => 12,
                    'female_count' => 13
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/getProgrammes',
                [
                    'operation' => 'getProgrammesWithAdmitted'
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Empty Results
     */
    public function testGetProgrammesWithAdmittedCandidatesEmptyResults(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'No programmes with admitted candidates found',
            'programmes' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/getProgrammes',
                [
                    'operation' => 'getProgrammesWithAdmitted'
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertIsArray($result['programmes']);
        $this->assertEmpty($result['programmes']);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Response Structure
     */
    public function testGetProgrammesWithAdmittedCandidatesResponseStructure(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programmes with admitted candidates retrieved successfully',
            'programmes' => [
                [
                    'programme_code' => 'PROG001',
                    'programme_name' => 'Computer Science',
                    'faculty' => 'Engineering',
                    'admitted_count' => 50,
                    'male_count' => 30,
                    'female_count' => 20,
                    'confirmed_count' => 45,
                    'pending_count' => 5,
                    'last_updated' => '2025-01-09 16:30:00'
                ]
            ],
            'total_programmes' => 1,
            'total_admitted' => 50,
            'generated_at' => '2025-01-09 16:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Programmes with admitted candidates retrieved successfully', $result['status_description']);
        $this->assertIsArray($result['programmes']);
        $this->assertCount(1, $result['programmes']);
        
        $programme = $result['programmes'][0];
        $this->assertEquals('PROG001', $programme['programme_code']);
        $this->assertEquals('Computer Science', $programme['programme_name']);
        $this->assertEquals(50, $programme['admitted_count']);
        $this->assertEquals(30, $programme['male_count']);
        $this->assertEquals(20, $programme['female_count']);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Single Programme
     */
    public function testGetProgrammesWithAdmittedCandidatesSingleProgramme(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programmes with admitted candidates retrieved successfully',
            'programmes' => [
                [
                    'programme_code' => 'PROG001',
                    'programme_name' => 'Bachelor of Science in Computer Science',
                    'admitted_count' => 100,
                    'male_count' => 60,
                    'female_count' => 40
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(1, $result['programmes']);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Multiple Programmes
     */
    public function testGetProgrammesWithAdmittedCandidatesMultipleProgrammes(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programmes with admitted candidates retrieved successfully',
            'programmes' => [
                [
                    'programme_code' => 'PROG001',
                    'programme_name' => 'Computer Science',
                    'admitted_count' => 50,
                    'male_count' => 30,
                    'female_count' => 20
                ],
                [
                    'programme_code' => 'PROG002',
                    'programme_name' => 'Engineering',
                    'admitted_count' => 40,
                    'male_count' => 35,
                    'female_count' => 5
                ],
                [
                    'programme_code' => 'PROG003',
                    'programme_name' => 'Medicine',
                    'admitted_count' => 25,
                    'male_count' => 12,
                    'female_count' => 13
                ],
                [
                    'programme_code' => 'PROG004',
                    'programme_name' => 'Law',
                    'admitted_count' => 30,
                    'male_count' => 15,
                    'female_count' => 15
                ],
                [
                    'programme_code' => 'PROG005',
                    'programme_name' => 'Business Administration',
                    'admitted_count' => 60,
                    'male_count' => 25,
                    'female_count' => 35
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(5, $result['programmes']);
        
        // Verify each programme has required fields
        foreach ($result['programmes'] as $programme) {
            $this->assertArrayHasKey('programme_code', $programme);
            $this->assertArrayHasKey('programme_name', $programme);
            $this->assertArrayHasKey('admitted_count', $programme);
            $this->assertArrayHasKey('male_count', $programme);
            $this->assertArrayHasKey('female_count', $programme);
        }
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Large Dataset
     */
    public function testGetProgrammesWithAdmittedCandidatesLargeDataset(): void
    {
        $programmes = [];
        for ($i = 1; $i <= 50; $i++) {
            $programmes[] = [
                'programme_code' => sprintf('PROG%03d', $i),
                'programme_name' => "Programme $i",
                'admitted_count' => rand(10, 100),
                'male_count' => rand(5, 60),
                'female_count' => rand(5, 40)
            ];
        }
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Programmes with admitted candidates retrieved successfully',
            'programmes' => $programmes
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(50, $result['programmes']);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Error Response
     */
    public function testGetProgrammesWithAdmittedCandidatesErrorResponse(): void
    {
        $expectedResponse = [
            'status_code' => 500,
            'status_description' => 'Internal server error while retrieving programmes',
            'error_details' => 'Database connection failed'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(500, $result['status_code']);
    }
    
    /**
     * Test 3.9: Get Programmes with Admitted Candidates - Request Format
     */
    public function testGetProgrammesWithAdmittedCandidatesRequestFormat(): void
    {
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Success',
            'programmes' => []
        ];
        
        // Verify the exact request format
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                $this->equalTo('/admission/getProgrammes'),
                $this->equalTo([
                    'operation' => 'getProgrammesWithAdmitted'
                ]),
                $this->equalTo('POST')
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getProgrammes();
        
        $this->assertEquals($expectedResponse, $result);
    }
}