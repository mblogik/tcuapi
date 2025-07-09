<?php

/**
 * TCU API Client - Staff Resource Session 5 Unit Tests
 * 
 * Unit tests for Session 5 endpoints (3.24-3.25) of the StaffResource class.
 * Tests cover validation, request formatting, and response handling for
 * staff registration and management operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 5 StaffResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\StaffResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class StaffResourceSession5Test extends TestCase
{
    private StaffResource $resource;
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
        $this->resource = new StaffResource($this->mockClient);
    }
    
    /**
     * Test 3.24: Register Staff Member - Valid Data
     */
    public function testRegisterStaffMemberWithValidData(): void
    {
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science',
            'employment_status' => 'permanent',
            'qualification_level' => 'masters',
            'email' => 'john.doe@university.ac.tz',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff member registered successfully',
            'staff_id' => 'STAFF001',
            'registration_id' => 'REG_2025_001',
            'registered_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/register',
                [
                    'operation' => 'registerStaffMember',
                    'staff_data' => $staffData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerStaffMember($staffData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.24: Register Staff Member - Missing Required Fields
     */
    public function testRegisterStaffMemberWithMissingRequiredFields(): void
    {
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'John'
            // Missing surname, position, institution_code, department
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Surname is required');
        
        $this->resource->registerStaffMember($staffData);
    }
    
    /**
     * Test 3.24: Register Staff Member - Invalid Staff ID
     */
    public function testRegisterStaffMemberWithInvalidStaffId(): void
    {
        $staffData = [
            'staff_id' => 'invalid',
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid staff ID format');
        
        $this->resource->registerStaffMember($staffData);
    }
    
    /**
     * Test 3.24: Register Staff Member - Invalid Position
     */
    public function testRegisterStaffMemberWithInvalidPosition(): void
    {
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'invalid_position',
            'institution_code' => 'INST001',
            'department' => 'Computer Science'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid staff position');
        
        $this->resource->registerStaffMember($staffData);
    }
    
    /**
     * Test 3.24: Register Staff Member - Invalid Email
     */
    public function testRegisterStaffMemberWithInvalidEmail(): void
    {
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science',
            'email' => 'invalid_email'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->registerStaffMember($staffData);
    }
    
    /**
     * Test 3.24: Register Staff Member - Invalid Employment Status
     */
    public function testRegisterStaffMemberWithInvalidEmploymentStatus(): void
    {
        $staffData = [
            'staff_id' => 'STAFF001',
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science',
            'employment_status' => 'invalid_status'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid employment status');
        
        $this->resource->registerStaffMember($staffData);
    }
    
    /**
     * Test 3.25: Get Staff Details - Valid Staff ID
     */
    public function testGetStaffDetailsWithValidStaffId(): void
    {
        $staffId = 'STAFF001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff details retrieved successfully',
            'staff_id' => $staffId,
            'firstname' => 'John',
            'surname' => 'Doe',
            'position' => 'lecturer',
            'institution_code' => 'INST001',
            'department' => 'Computer Science',
            'employment_status' => 'permanent',
            'qualification_level' => 'masters',
            'email' => 'john.doe@university.ac.tz',
            'phone' => '+255712345678'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/getDetails',
                [
                    'operation' => 'getStaffDetails',
                    'staff_id' => $staffId
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStaffDetails($staffId);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.25: Get Staff Details - Invalid Staff ID
     */
    public function testGetStaffDetailsWithInvalidStaffId(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid staff ID format');
        
        $this->resource->getStaffDetails('invalid');
    }
    
    /**
     * Test Update Staff Information - Valid Data
     */
    public function testUpdateStaffInformationWithValidData(): void
    {
        $staffId = 'STAFF001';
        $updateData = [
            'email' => 'newemail@university.ac.tz',
            'phone' => '+255787654321',
            'position' => 'senior_lecturer'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff information updated successfully',
            'staff_id' => $staffId,
            'updated_fields' => ['email', 'phone', 'position'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/updateInformation',
                [
                    'operation' => 'updateStaffInformation',
                    'staff_id' => $staffId,
                    'update_data' => $updateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateStaffInformation($staffId, $updateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Staff Information - Invalid Staff ID
     */
    public function testUpdateStaffInformationWithInvalidStaffId(): void
    {
        $updateData = ['email' => 'newemail@university.ac.tz'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid staff ID format');
        
        $this->resource->updateStaffInformation('invalid', $updateData);
    }
    
    /**
     * Test Update Staff Information - Empty Update Data
     */
    public function testUpdateStaffInformationWithEmptyUpdateData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Update data cannot be empty');
        
        $this->resource->updateStaffInformation('STAFF001', []);
    }
    
    /**
     * Test Get Staff by Institution - Valid Institution Code
     */
    public function testGetStaffByInstitutionWithValidInstitutionCode(): void
    {
        $institutionCode = 'INST001';
        $filters = ['position' => 'lecturer'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff retrieved successfully',
            'institution_code' => $institutionCode,
            'staff_members' => [
                [
                    'staff_id' => 'STAFF001',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'position' => 'lecturer'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/getByInstitution',
                [
                    'operation' => 'getStaffByInstitution',
                    'institution_code' => $institutionCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStaffByInstitution($institutionCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Staff by Department - Valid Data
     */
    public function testGetStaffByDepartmentWithValidData(): void
    {
        $institutionCode = 'INST001';
        $department = 'Computer Science';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff retrieved successfully',
            'institution_code' => $institutionCode,
            'department' => $department,
            'staff_members' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/getByDepartment',
                [
                    'operation' => 'getStaffByDepartment',
                    'institution_code' => $institutionCode,
                    'department' => $department
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStaffByDepartment($institutionCode, $department);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Staff by Department - Empty Department
     */
    public function testGetStaffByDepartmentWithEmptyDepartment(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Department is required');
        
        $this->resource->getStaffByDepartment('INST001', '');
    }
    
    /**
     * Test Search Staff - Valid Search Criteria
     */
    public function testSearchStaffWithValidSearchCriteria(): void
    {
        $searchCriteria = [
            'firstname' => 'John',
            'position' => 'lecturer'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff search completed successfully',
            'search_results' => [
                [
                    'staff_id' => 'STAFF001',
                    'firstname' => 'John',
                    'surname' => 'Doe'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/search',
                [
                    'operation' => 'searchStaff',
                    'search_criteria' => $searchCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->searchStaff($searchCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Staff - Empty Search Criteria
     */
    public function testSearchStaffWithEmptySearchCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Search criteria cannot be empty');
        
        $this->resource->searchStaff([]);
    }
    
    /**
     * Test Get Staff Statistics - Valid Filters
     */
    public function testGetStaffStatisticsWithValidFilters(): void
    {
        $filters = ['institution_code' => 'INST001'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff statistics retrieved successfully',
            'statistics' => [
                'total_staff' => 50,
                'by_position' => [
                    'professor' => 5,
                    'associate_professor' => 8,
                    'senior_lecturer' => 12,
                    'lecturer' => 25
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/getStatistics',
                [
                    'operation' => 'getStaffStatistics',
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStaffStatistics($filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Verify Staff Credentials - Valid Data
     */
    public function testVerifyStaffCredentialsWithValidData(): void
    {
        $staffId = 'STAFF001';
        $credentialsData = [
            'certificate_number' => 'CERT123456',
            'verification_type' => 'academic'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff credentials verified successfully',
            'staff_id' => $staffId,
            'verification_status' => 'verified',
            'verified_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/verifyCredentials',
                [
                    'operation' => 'verifyStaffCredentials',
                    'staff_id' => $staffId,
                    'credentials_data' => $credentialsData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->verifyStaffCredentials($staffId, $credentialsData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Staff Employment Status - Valid Data
     */
    public function testUpdateStaffEmploymentStatusWithValidData(): void
    {
        $staffId = 'STAFF001';
        $employmentStatus = 'retired';
        $reason = 'Reached retirement age';
        $additionalData = ['retirement_date' => '2025-01-01'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff employment status updated successfully',
            'staff_id' => $staffId,
            'employment_status' => $employmentStatus,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/updateEmploymentStatus',
                [
                    'operation' => 'updateStaffEmploymentStatus',
                    'staff_id' => $staffId,
                    'employment_status' => $employmentStatus,
                    'reason' => $reason,
                    'additional_data' => $additionalData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateStaffEmploymentStatus($staffId, $employmentStatus, $reason, $additionalData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Staff Employment Status - Invalid Employment Status
     */
    public function testUpdateStaffEmploymentStatusWithInvalidEmploymentStatus(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid employment status');
        
        $this->resource->updateStaffEmploymentStatus('STAFF001', 'invalid_status', 'Some reason');
    }
    
    /**
     * Test Get Staff Employment History - Valid Staff ID
     */
    public function testGetStaffEmploymentHistoryWithValidStaffId(): void
    {
        $staffId = 'STAFF001';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff employment history retrieved successfully',
            'staff_id' => $staffId,
            'employment_history' => [
                [
                    'employment_status' => 'contract',
                    'start_date' => '2020-01-01',
                    'end_date' => '2022-12-31'
                ],
                [
                    'employment_status' => 'permanent',
                    'start_date' => '2023-01-01',
                    'end_date' => null
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/getEmploymentHistory',
                [
                    'operation' => 'getStaffEmploymentHistory',
                    'staff_id' => $staffId
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getStaffEmploymentHistory($staffId);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Staff - Valid Data
     */
    public function testBulkRegisterStaffWithValidData(): void
    {
        $staffData = [
            [
                'staff_id' => 'STAFF001',
                'firstname' => 'John',
                'surname' => 'Doe',
                'position' => 'lecturer',
                'institution_code' => 'INST001',
                'department' => 'Computer Science'
            ],
            [
                'staff_id' => 'STAFF002',
                'firstname' => 'Jane',
                'surname' => 'Smith',
                'position' => 'senior_lecturer',
                'institution_code' => 'INST001',
                'department' => 'Mathematics'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk staff registration completed successfully',
            'total_staff' => 2,
            'successful_registrations' => 2,
            'failed_registrations' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/bulkRegister',
                [
                    'operation' => 'bulkRegisterStaff',
                    'staff_data' => $staffData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkRegisterStaff($staffData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Staff - Empty Data
     */
    public function testBulkRegisterStaffWithEmptyData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Staff data array cannot be empty');
        
        $this->resource->bulkRegisterStaff([]);
    }
    
    /**
     * Test Generate Staff Report - Valid Report Criteria
     */
    public function testGenerateStaffReportWithValidReportCriteria(): void
    {
        $reportCriteria = [
            'institution_code' => 'INST001',
            'report_type' => 'summary'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Staff report generated successfully',
            'report_id' => 'RPT_2025_001',
            'report_type' => 'summary',
            'generated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/staff/generateReport',
                [
                    'operation' => 'generateStaffReport',
                    'report_criteria' => $reportCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->generateStaffReport($reportCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Staff Report - Empty Report Criteria
     */
    public function testGenerateStaffReportWithEmptyReportCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Report criteria cannot be empty');
        
        $this->resource->generateStaffReport([]);
    }
}