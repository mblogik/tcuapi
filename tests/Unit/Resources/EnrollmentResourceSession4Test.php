<?php

/**
 * TCU API Client - Enrollment Resource Session 4 Unit Tests
 * 
 * Unit tests for Session 4 enrollment endpoints of the EnrollmentResource class.
 * Tests cover validation, request formatting, and response handling for
 * student enrollment operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 4 EnrollmentResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\EnrollmentResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EnrollmentResourceSession4Test extends TestCase
{
    private EnrollmentResource $resource;
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
        $this->resource = new EnrollmentResource($this->mockClient);
    }
    
    /**
     * Test Enroll Student - Valid Data
     */
    public function testEnrollStudentWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $programmeCode = 'PROG001';
        $institutionCode = 'INST001';
        $enrollmentData = [
            'academic_year' => '2025/2026',
            'semester' => '1',
            'study_mode' => 'full_time'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Student enrolled successfully',
            'f4indexno' => $f4indexno,
            'programme_code' => $programmeCode,
            'institution_code' => $institutionCode,
            'enrollment_id' => 'ENR_2025_001',
            'enrolled_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/enrollStudent',
                [
                    'operation' => 'enrollStudent',
                    'f4indexno' => $f4indexno,
                    'programme_code' => $programmeCode,
                    'institution_code' => $institutionCode,
                    'enrollment_data' => $enrollmentData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->enrollStudent($f4indexno, $programmeCode, $institutionCode, $enrollmentData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Enroll Student - Invalid F4 Index Number
     */
    public function testEnrollStudentWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->enrollStudent('invalid_f4', 'PROG001', 'INST001');
    }
    
    /**
     * Test Enroll Student - Invalid Programme Code
     */
    public function testEnrollStudentWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->enrollStudent('S0123456789', 'invalid_programme', 'INST001');
    }
    
    /**
     * Test Enroll Student - Invalid Institution Code
     */
    public function testEnrollStudentWithInvalidInstitutionCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid institution code format');
        
        $this->resource->enrollStudent('S0123456789', 'PROG001', 'invalid_institution');
    }
    
    /**
     * Test Get Enrollment Status - Valid F4 Index Number
     */
    public function testGetEnrollmentStatusWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment status retrieved successfully',
            'f4indexno' => $f4indexno,
            'enrollment_status' => 'enrolled',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'enrolled_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/getEnrollmentStatus',
                [
                    'operation' => 'getEnrollmentStatus',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getEnrollmentStatus($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Enrollment Status - Invalid F4 Index Number
     */
    public function testGetEnrollmentStatusWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getEnrollmentStatus('invalid_f4');
    }
    
    /**
     * Test Update Enrollment Status - Valid Data
     */
    public function testUpdateEnrollmentStatusWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $status = 'deferred';
        $reason = 'Student requested deferment for personal reasons';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment status updated successfully',
            'f4indexno' => $f4indexno,
            'status' => $status,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/updateEnrollmentStatus',
                [
                    'operation' => 'updateEnrollmentStatus',
                    'f4indexno' => $f4indexno,
                    'status' => $status,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateEnrollmentStatus($f4indexno, $status, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Enrollment Status - Invalid Status
     */
    public function testUpdateEnrollmentStatusWithInvalidStatus(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid enrollment status');
        
        $this->resource->updateEnrollmentStatus('S0123456789', 'invalid_status', 'Some reason');
    }
    
    /**
     * Test Get Enrollment Statistics - Valid Programme Code
     */
    public function testGetEnrollmentStatisticsWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $academicYear = '2025/2026';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment statistics retrieved successfully',
            'programme_code' => $programmeCode,
            'academic_year' => $academicYear,
            'statistics' => [
                'total_enrolled' => 150,
                'enrolled_students' => 120,
                'deferred_students' => 20,
                'withdrawn_students' => 10
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/getEnrollmentStatistics',
                [
                    'operation' => 'getEnrollmentStatistics',
                    'programme_code' => $programmeCode,
                    'academic_year' => $academicYear
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getEnrollmentStatistics($programmeCode, $academicYear);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Enrolled Students - Valid Programme Code
     */
    public function testGetEnrolledStudentsWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $filters = [
            'academic_year' => '2025/2026',
            'semester' => '1',
            'study_mode' => 'full_time'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrolled students retrieved successfully',
            'programme_code' => $programmeCode,
            'enrolled_students' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'enrollment_status' => 'enrolled',
                    'enrolled_at' => '2025-01-09 14:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/getEnrolledStudents',
                [
                    'operation' => 'getEnrolledStudents',
                    'programme_code' => $programmeCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getEnrolledStudents($programmeCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Defer Enrollment - Valid Data
     */
    public function testDeferEnrollmentWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Personal circumstances require deferment';
        $deferredUntil = '2025-09-01';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment deferred successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'deferred_until' => $deferredUntil,
            'deferred_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/deferEnrollment',
                [
                    'operation' => 'deferEnrollment',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason,
                    'deferred_until' => $deferredUntil
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->deferEnrollment($f4indexno, $reason, $deferredUntil);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Defer Enrollment - Empty Reason
     */
    public function testDeferEnrollmentWithEmptyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Deferment reason is required');
        
        $this->resource->deferEnrollment('S0123456789', '', '2025-09-01');
    }
    
    /**
     * Test Withdraw Enrollment - Valid Data
     */
    public function testWithdrawEnrollmentWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Financial difficulties';
        $withdrawalType = 'financial';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment withdrawn successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'withdrawal_type' => $withdrawalType,
            'withdrawn_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/withdrawEnrollment',
                [
                    'operation' => 'withdrawEnrollment',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason,
                    'withdrawal_type' => $withdrawalType
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->withdrawEnrollment($f4indexno, $reason, $withdrawalType);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Withdraw Enrollment - Invalid Withdrawal Type
     */
    public function testWithdrawEnrollmentWithInvalidWithdrawalType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid withdrawal type');
        
        $this->resource->withdrawEnrollment('S0123456789', 'Some reason', 'invalid_type');
    }
    
    /**
     * Test Reinstate Enrollment - Valid Data
     */
    public function testReinstateEnrollmentWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $reason = 'Issues resolved, student ready to continue';
        $reinstatementData = [
            'effective_date' => '2025-02-01',
            'conditions' => ['probation', 'academic_support']
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment reinstated successfully',
            'f4indexno' => $f4indexno,
            'reason' => $reason,
            'reinstated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/reinstateEnrollment',
                [
                    'operation' => 'reinstateEnrollment',
                    'f4indexno' => $f4indexno,
                    'reason' => $reason,
                    'reinstatement_data' => $reinstatementData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reinstateEnrollment($f4indexno, $reason, $reinstatementData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Enrollment History - Valid F4 Index Number
     */
    public function testGetEnrollmentHistoryWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Enrollment history retrieved successfully',
            'f4indexno' => $f4indexno,
            'enrollment_history' => [
                [
                    'action' => 'enrolled',
                    'timestamp' => '2025-01-09 14:30:00',
                    'programme_code' => 'PROG001',
                    'institution_code' => 'INST001'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/getEnrollmentHistory',
                [
                    'operation' => 'getEnrollmentHistory',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getEnrollmentHistory($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Enroll Students - Valid Data
     */
    public function testBulkEnrollStudentsWithValidData(): void
    {
        $enrollmentData = [
            [
                'f4indexno' => 'S0123456789',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001'
            ],
            [
                'f4indexno' => 'S0123456790',
                'programme_code' => 'PROG002',
                'institution_code' => 'INST002'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk enrollment completed successfully',
            'total_enrollments' => 2,
            'successful_enrollments' => 2,
            'failed_enrollments' => 0,
            'enrollments' => [
                [
                    'f4indexno' => 'S0123456789',
                    'status' => 'enrolled',
                    'enrollment_id' => 'ENR_2025_001'
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'status' => 'enrolled',
                    'enrollment_id' => 'ENR_2025_002'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/enrollment/bulkEnrollStudents',
                [
                    'operation' => 'bulkEnrollStudents',
                    'enrollment_data' => $enrollmentData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkEnrollStudents($enrollmentData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Enroll Students - Empty Data
     */
    public function testBulkEnrollStudentsWithEmptyData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Enrollment data array cannot be empty');
        
        $this->resource->bulkEnrollStudents([]);
    }
    
    /**
     * Test Bulk Enroll Students - Invalid F4 Index Number
     */
    public function testBulkEnrollStudentsWithInvalidF4IndexNumber(): void
    {
        $enrollmentData = [
            [
                'f4indexno' => 'invalid_f4',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number for enrollment at index 0');
        
        $this->resource->bulkEnrollStudents($enrollmentData);
    }
}