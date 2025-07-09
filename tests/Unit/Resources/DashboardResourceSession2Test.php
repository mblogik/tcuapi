<?php

/**
 * TCU API Client - Dashboard Resource Session 2 Unit Tests
 * 
 * Unit tests for Session 2 endpoint (3.7) of the DashboardResource class.
 * Tests cover validation, request formatting, and response handling for
 * dashboard population operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 2 DashboardResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\DashboardResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DashboardResourceSession2Test extends TestCase
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
     * Test 3.7: Populate Dashboard - Valid Data
     */
    public function testPopulateDashboardWithValidData(): void
    {
        $programmeCode = 'PROG001';
        $males = 50;
        $females = 30;
        $additionalData = [
            'academic_year' => '2025',
            'application_round' => '1'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => $males + $females
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/dashboard/populate',
                [
                    'operation' => 'populateDashboard',
                    'programme_code' => $programmeCode,
                    'males' => $males,
                    'females' => $females,
                    'total' => $males + $females,
                    'additional_data' => $additionalData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females, $additionalData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Without Additional Data
     */
    public function testPopulateDashboardWithoutAdditionalData(): void
    {
        $programmeCode = 'PROG002';
        $males = 25;
        $females = 35;
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => $males + $females
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/dashboard/populate',
                [
                    'operation' => 'populateDashboard',
                    'programme_code' => $programmeCode,
                    'males' => $males,
                    'females' => $females,
                    'total' => $males + $females,
                    'additional_data' => []
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Invalid Programme Code
     */
    public function testPopulateDashboardWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->populate('invalid_programme', 10, 20);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Negative Male Count
     */
    public function testPopulateDashboardWithNegativeMaleCount(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Male and female counts must be non-negative');
        
        $this->resource->populate('PROG001', -5, 20);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Negative Female Count
     */
    public function testPopulateDashboardWithNegativeFemaleCount(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Male and female counts must be non-negative');
        
        $this->resource->populate('PROG001', 10, -15);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Both Negative Counts
     */
    public function testPopulateDashboardWithBothNegativeCounts(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Male and female counts must be non-negative');
        
        $this->resource->populate('PROG001', -5, -10);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Zero Counts
     */
    public function testPopulateDashboardWithZeroCounts(): void
    {
        $programmeCode = 'PROG003';
        $males = 0;
        $females = 0;
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Large Numbers
     */
    public function testPopulateDashboardWithLargeNumbers(): void
    {
        $programmeCode = 'PROG004';
        $males = 1000;
        $females = 1500;
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => 2500
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females);
        
        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(2500, $result['total']);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Response Structure
     */
    public function testPopulateDashboardResponseStructure(): void
    {
        $programmeCode = 'PROG005';
        $males = 40;
        $females = 60;
        $additionalData = [
            'academic_year' => '2025',
            'application_round' => '2',
            'institution_code' => 'INST001'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => 100,
            'updated_at' => '2025-01-09 15:45:00',
            'previous_total' => 85,
            'change_percentage' => 17.6
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females, $additionalData);
        
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('Dashboard populated successfully', $result['status_description']);
        $this->assertEquals($programmeCode, $result['programme_code']);
        $this->assertEquals($males, $result['males']);
        $this->assertEquals($females, $result['females']);
        $this->assertEquals(100, $result['total']);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Empty Programme Code
     */
    public function testPopulateDashboardWithEmptyProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->populate('', 10, 20);
    }
    
    /**
     * Test 3.7: Populate Dashboard - Complex Additional Data
     */
    public function testPopulateDashboardWithComplexAdditionalData(): void
    {
        $programmeCode = 'PROG006';
        $males = 30;
        $females = 25;
        $additionalData = [
            'academic_year' => '2025',
            'semester' => '1',
            'application_round' => '1',
            'institution_code' => 'INST002',
            'programme_name' => 'Computer Science',
            'faculty' => 'Engineering',
            'statistics' => [
                'total_applications' => 200,
                'selected_applications' => 55,
                'confirmed_applications' => 45
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Dashboard populated successfully',
            'programme_code' => $programmeCode,
            'males' => $males,
            'females' => $females,
            'total' => 55
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->willReturn($expectedResponse);
        
        $result = $this->resource->populate($programmeCode, $males, $females, $additionalData);
        
        $this->assertEquals($expectedResponse, $result);
    }
}