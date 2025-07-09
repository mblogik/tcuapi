<?php

/**
 * TCU API Client - Non-Degree Resource Session 6 Unit Tests
 * 
 * Unit tests for Session 6 endpoints (3.26-3.28) of the NonDegreeResource class.
 * Tests cover validation, request formatting, and response handling for
 * non-degree student operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 6 NonDegreeResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\NonDegreeResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class NonDegreeResourceSession6Test extends TestCase
{
    private NonDegreeResource $resource;
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
        $this->resource = new NonDegreeResource($this->mockClient);
    }
    
    /**
     * Test 3.26: Register Non-Degree Student - Valid Data
     */
    public function testRegisterNonDegreeStudentWithValidData(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'certificate',
            'study_mode' => 'part_time',
            'duration_months' => 12,
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree student registered successfully',
            'f4indexno' => 'S0123456789',
            'student_id' => 'NONDEG_2025_001',
            'registered_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/register',
                [
                    'operation' => 'registerNonDegreeStudent',
                    'student_data' => $studentData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerNonDegreeStudent($studentData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.26: Register Non-Degree Student - Missing Required Fields
     */
    public function testRegisterNonDegreeStudentWithMissingRequiredFields(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John'
            // Missing surname, programme_code, institution_code, programme_type
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Surname is required');
        
        $this->resource->registerNonDegreeStudent($studentData);
    }
    
    /**
     * Test 3.26: Register Non-Degree Student - Invalid Programme Type
     */
    public function testRegisterNonDegreeStudentWithInvalidProgrammeType(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'invalid_type'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme type');
        
        $this->resource->registerNonDegreeStudent($studentData);
    }
    
    /**
     * Test 3.26: Register Non-Degree Student - Invalid Duration
     */
    public function testRegisterNonDegreeStudentWithInvalidDuration(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'certificate',
            'duration_months' => 70 // Invalid duration
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme duration');
        
        $this->resource->registerNonDegreeStudent($studentData);
    }
    
    /**
     * Test 3.26: Register Non-Degree Student - Invalid Study Mode
     */
    public function testRegisterNonDegreeStudentWithInvalidStudyMode(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'certificate',
            'study_mode' => 'invalid_mode'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid study mode');
        
        $this->resource->registerNonDegreeStudent($studentData);
    }
    
    /**
     * Test 3.27: Get Non-Degree Student Details - Valid F4 Index Number
     */
    public function testGetNonDegreeStudentDetailsWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree student details retrieved successfully',
            'f4indexno' => $f4indexno,
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'CERT001',
            'institution_code' => 'INST001',
            'programme_type' => 'certificate',
            'study_mode' => 'part_time',
            'completion_status' => 'in_progress',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/getDetails',
                [
                    'operation' => 'getNonDegreeStudentDetails',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getNonDegreeStudentDetails($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.27: Get Non-Degree Student Details - Invalid F4 Index Number
     */
    public function testGetNonDegreeStudentDetailsWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getNonDegreeStudentDetails('invalid_f4');
    }
    
    /**
     * Test 3.28: Update Non-Degree Student Information - Valid Data
     */
    public function testUpdateNonDegreeStudentInformationWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $updateData = [
            'email' => 'john.doe.updated@example.com',
            'phone' => '+255787654321',
            'study_mode' => 'full_time',
            'completion_status' => 'completed'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree student information updated successfully',
            'f4indexno' => $f4indexno,
            'updated_fields' => ['email', 'phone', 'study_mode', 'completion_status'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/updateInformation',
                [
                    'operation' => 'updateNonDegreeStudentInformation',
                    'f4indexno' => $f4indexno,
                    'update_data' => $updateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateNonDegreeStudentInformation($f4indexno, $updateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.28: Update Non-Degree Student Information - Invalid F4 Index Number
     */
    public function testUpdateNonDegreeStudentInformationWithInvalidF4IndexNumber(): void
    {
        $updateData = ['email' => 'newemail@example.com'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->updateNonDegreeStudentInformation('invalid_f4', $updateData);
    }
    
    /**
     * Test 3.28: Update Non-Degree Student Information - Empty Update Data
     */
    public function testUpdateNonDegreeStudentInformationWithEmptyUpdateData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Update data cannot be empty');
        
        $this->resource->updateNonDegreeStudentInformation('S0123456789', []);
    }
    
    /**
     * Test 3.28: Update Non-Degree Student Information - Invalid Completion Status
     */
    public function testUpdateNonDegreeStudentInformationWithInvalidCompletionStatus(): void
    {
        $updateData = ['completion_status' => 'invalid_status'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid completion status');
        
        $this->resource->updateNonDegreeStudentInformation('S0123456789', $updateData);
    }
    
    /**
     * Test Get Non-Degree Students by Programme - Valid Programme Code
     */
    public function testGetNonDegreeStudentsByProgrammeWithValidProgrammeCode(): void
    {
        $programmeCode = 'CERT001';
        $filters = ['programme_type' => 'certificate'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree students retrieved successfully',
            'programme_code' => $programmeCode,
            'students' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'programme_type' => 'certificate'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/getByProgramme',
                [
                    'operation' => 'getNonDegreeStudentsByProgramme',
                    'programme_code' => $programmeCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getNonDegreeStudentsByProgramme($programmeCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Non-Degree Students by Institution - Valid Institution Code
     */
    public function testGetNonDegreeStudentsByInstitutionWithValidInstitutionCode(): void
    {
        $institutionCode = 'INST001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree students retrieved successfully',
            'institution_code' => $institutionCode,
            'students' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/getByInstitution',
                [
                    'operation' => 'getNonDegreeStudentsByInstitution',
                    'institution_code' => $institutionCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getNonDegreeStudentsByInstitution($institutionCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Non-Degree Students - Valid Search Criteria
     */
    public function testSearchNonDegreeStudentsWithValidSearchCriteria(): void
    {
        $searchCriteria = [
            'firstname' => 'John',
            'programme_type' => 'certificate'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree student search completed successfully',
            'search_results' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/search',
                [
                    'operation' => 'searchNonDegreeStudents',
                    'search_criteria' => $searchCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->searchNonDegreeStudents($searchCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Non-Degree Students - Empty Search Criteria
     */
    public function testSearchNonDegreeStudentsWithEmptySearchCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Search criteria cannot be empty');
        
        $this->resource->searchNonDegreeStudents([]);
    }
    
    /**
     * Test Get Non-Degree Programme Statistics - Valid Filters
     */
    public function testGetNonDegreeProgrammeStatisticsWithValidFilters(): void
    {
        $filters = ['programme_type' => 'certificate'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree programme statistics retrieved successfully',
            'statistics' => [
                'total_students' => 150,
                'by_programme_type' => [
                    'certificate' => 80,
                    'diploma' => 50,
                    'short_course' => 20
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/getStatistics',
                [
                    'operation' => 'getNonDegreeProgrammeStatistics',
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getNonDegreeProgrammeStatistics($filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Issue Non-Degree Certificate - Valid Data
     */
    public function testIssueNonDegreeCertificateWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $certificateType = 'completion';
        $certificateData = ['grade' => 'A'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree certificate issued successfully',
            'f4indexno' => $f4indexno,
            'certificate_type' => $certificateType,
            'certificate_id' => 'CERT_2025_001',
            'issued_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/issueCertificate',
                [
                    'operation' => 'issueNonDegreeCertificate',
                    'f4indexno' => $f4indexno,
                    'certificate_type' => $certificateType,
                    'certificate_data' => $certificateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->issueNonDegreeCertificate($f4indexno, $certificateType, $certificateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Issue Non-Degree Certificate - Invalid Certificate Type
     */
    public function testIssueNonDegreeCertificateWithInvalidCertificateType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid certificate type');
        
        $this->resource->issueNonDegreeCertificate('S0123456789', 'invalid_type');
    }
    
    /**
     * Test Update Completion Status - Valid Data
     */
    public function testUpdateCompletionStatusWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $completionStatus = 'completed';
        $reason = 'Student successfully completed all requirements';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Completion status updated successfully',
            'f4indexno' => $f4indexno,
            'completion_status' => $completionStatus,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/updateCompletionStatus',
                [
                    'operation' => 'updateCompletionStatus',
                    'f4indexno' => $f4indexno,
                    'completion_status' => $completionStatus,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateCompletionStatus($f4indexno, $completionStatus, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Completion Status - Invalid Status
     */
    public function testUpdateCompletionStatusWithInvalidStatus(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid completion status');
        
        $this->resource->updateCompletionStatus('S0123456789', 'invalid_status', 'Some reason');
    }
    
    /**
     * Test Get Completion History - Valid F4 Index Number
     */
    public function testGetCompletionHistoryWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Completion history retrieved successfully',
            'f4indexno' => $f4indexno,
            'completion_history' => [
                [
                    'completion_status' => 'in_progress',
                    'updated_at' => '2025-01-01 10:00:00'
                ],
                [
                    'completion_status' => 'completed',
                    'updated_at' => '2025-01-09 14:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/getCompletionHistory',
                [
                    'operation' => 'getCompletionHistory',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getCompletionHistory($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Non-Degree Students - Valid Data
     */
    public function testBulkRegisterNonDegreeStudentsWithValidData(): void
    {
        $studentsData = [
            [
                'f4indexno' => 'S0123456789',
                'firstname' => 'John',
                'surname' => 'Doe',
                'programme_code' => 'CERT001',
                'institution_code' => 'INST001',
                'programme_type' => 'certificate'
            ],
            [
                'f4indexno' => 'S0123456790',
                'firstname' => 'Jane',
                'surname' => 'Smith',
                'programme_code' => 'DIPL001',
                'institution_code' => 'INST001',
                'programme_type' => 'diploma'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk non-degree student registration completed successfully',
            'total_students' => 2,
            'successful_registrations' => 2,
            'failed_registrations' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/bulkRegister',
                [
                    'operation' => 'bulkRegisterNonDegreeStudents',
                    'students_data' => $studentsData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkRegisterNonDegreeStudents($studentsData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Non-Degree Students - Empty Data
     */
    public function testBulkRegisterNonDegreeStudentsWithEmptyData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Students data array cannot be empty');
        
        $this->resource->bulkRegisterNonDegreeStudents([]);
    }
    
    /**
     * Test Generate Non-Degree Programme Report - Valid Report Criteria
     */
    public function testGenerateNonDegreeProgrammeReportWithValidReportCriteria(): void
    {
        $reportCriteria = [
            'institution_code' => 'INST001',
            'report_type' => 'summary'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Non-degree programme report generated successfully',
            'report_id' => 'RPT_2025_001',
            'report_type' => 'summary',
            'generated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/nondegree/generateReport',
                [
                    'operation' => 'generateNonDegreeProgrammeReport',
                    'report_criteria' => $reportCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->generateNonDegreeProgrammeReport($reportCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Non-Degree Programme Report - Empty Report Criteria
     */
    public function testGenerateNonDegreeProgrammeReportWithEmptyReportCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Report criteria cannot be empty');
        
        $this->resource->generateNonDegreeProgrammeReport([]);
    }
}