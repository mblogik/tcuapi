<?php

/**
 * TCU API Client - Postgraduate Resource Session 6 Unit Tests
 * 
 * Unit tests for Session 6 endpoints (3.29-3.30) of the PostgraduateResource class.
 * Tests cover validation, request formatting, and response handling for
 * postgraduate student operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 6 PostgraduateResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\PostgraduateResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PostgraduateResourceSession6Test extends TestCase
{
    private PostgraduateResource $resource;
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
        $this->resource = new PostgraduateResource($this->mockClient);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Valid Data
     */
    public function testRegisterPostgraduateStudentWithValidData(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'study_mode' => 'full_time',
            'funding_source' => 'self_sponsored',
            'research_area' => 'Computer Science',
            'supervisor_staff_id' => 'STAFF001',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate student registered successfully',
            'f4indexno' => 'S0123456789',
            'student_id' => 'POSTGRAD_2025_001',
            'registered_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/register',
                [
                    'operation' => 'registerPostgraduateStudent',
                    'student_data' => $studentData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerPostgraduateStudent($studentData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Missing Required Fields
     */
    public function testRegisterPostgraduateStudentWithMissingRequiredFields(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John'
            // Missing surname, programme_code, institution_code, study_level
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Surname is required');
        
        $this->resource->registerPostgraduateStudent($studentData);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Invalid Study Level
     */
    public function testRegisterPostgraduateStudentWithInvalidStudyLevel(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'invalid_level'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid study level');
        
        $this->resource->registerPostgraduateStudent($studentData);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Missing Research Area for Doctorate
     */
    public function testRegisterPostgraduateStudentDoctorateMissingResearchArea(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PHD001',
            'institution_code' => 'INST001',
            'study_level' => 'doctorate'
            // Missing research_area which is required for doctorate
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Research area is required for doctorate/PhD programmes');
        
        $this->resource->registerPostgraduateStudent($studentData);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Invalid Supervisor Staff ID
     */
    public function testRegisterPostgraduateStudentWithInvalidSupervisorStaffId(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'supervisor_staff_id' => 'invalid'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid supervisor staff ID format');
        
        $this->resource->registerPostgraduateStudent($studentData);
    }
    
    /**
     * Test 3.29: Register Postgraduate Student - Invalid Funding Source
     */
    public function testRegisterPostgraduateStudentWithInvalidFundingSource(): void
    {
        $studentData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'funding_source' => 'invalid_source'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid funding source');
        
        $this->resource->registerPostgraduateStudent($studentData);
    }
    
    /**
     * Test 3.30: Get Postgraduate Student Details - Valid F4 Index Number
     */
    public function testGetPostgraduateStudentDetailsWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate student details retrieved successfully',
            'f4indexno' => $f4indexno,
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'MSC001',
            'institution_code' => 'INST001',
            'study_level' => 'masters',
            'study_mode' => 'full_time',
            'completion_status' => 'in_progress',
            'research_area' => 'Computer Science',
            'supervisor_staff_id' => 'STAFF001',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/getDetails',
                [
                    'operation' => 'getPostgraduateStudentDetails',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getPostgraduateStudentDetails($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.30: Get Postgraduate Student Details - Invalid F4 Index Number
     */
    public function testGetPostgraduateStudentDetailsWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getPostgraduateStudentDetails('invalid_f4');
    }
    
    /**
     * Test Update Postgraduate Student Information - Valid Data
     */
    public function testUpdatePostgraduateStudentInformationWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $updateData = [
            'email' => 'john.doe.updated@example.com',
            'phone' => '+255787654321',
            'study_mode' => 'part_time',
            'completion_status' => 'thesis_submitted',
            'supervisor_staff_id' => 'STAFF002'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate student information updated successfully',
            'f4indexno' => $f4indexno,
            'updated_fields' => ['email', 'phone', 'study_mode', 'completion_status', 'supervisor_staff_id'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/updateInformation',
                [
                    'operation' => 'updatePostgraduateStudentInformation',
                    'f4indexno' => $f4indexno,
                    'update_data' => $updateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updatePostgraduateStudentInformation($f4indexno, $updateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Postgraduate Student Information - Invalid Completion Status
     */
    public function testUpdatePostgraduateStudentInformationWithInvalidCompletionStatus(): void
    {
        $updateData = ['completion_status' => 'invalid_status'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid completion status');
        
        $this->resource->updatePostgraduateStudentInformation('S0123456789', $updateData);
    }
    
    /**
     * Test Get Postgraduate Students by Programme - Valid Programme Code
     */
    public function testGetPostgraduateStudentsByProgrammeWithValidProgrammeCode(): void
    {
        $programmeCode = 'MSC001';
        $filters = ['study_level' => 'masters'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate students retrieved successfully',
            'programme_code' => $programmeCode,
            'students' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'study_level' => 'masters'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/getByProgramme',
                [
                    'operation' => 'getPostgraduateStudentsByProgramme',
                    'programme_code' => $programmeCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getPostgraduateStudentsByProgramme($programmeCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Postgraduate Students by Institution - Valid Institution Code
     */
    public function testGetPostgraduateStudentsByInstitutionWithValidInstitutionCode(): void
    {
        $institutionCode = 'INST001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate students retrieved successfully',
            'institution_code' => $institutionCode,
            'students' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/getByInstitution',
                [
                    'operation' => 'getPostgraduateStudentsByInstitution',
                    'institution_code' => $institutionCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getPostgraduateStudentsByInstitution($institutionCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Postgraduate Students - Valid Search Criteria
     */
    public function testSearchPostgraduateStudentsWithValidSearchCriteria(): void
    {
        $searchCriteria = [
            'firstname' => 'John',
            'study_level' => 'masters',
            'supervisor_staff_id' => 'STAFF001'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate student search completed successfully',
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
                '/postgraduate/search',
                [
                    'operation' => 'searchPostgraduateStudents',
                    'search_criteria' => $searchCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->searchPostgraduateStudents($searchCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Postgraduate Students - Invalid Supervisor Staff ID
     */
    public function testSearchPostgraduateStudentsWithInvalidSupervisorStaffId(): void
    {
        $searchCriteria = ['supervisor_staff_id' => 'invalid'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid supervisor staff ID in search criteria');
        
        $this->resource->searchPostgraduateStudents($searchCriteria);
    }
    
    /**
     * Test Get Postgraduate Programme Statistics - Valid Filters
     */
    public function testGetPostgraduateProgrammeStatisticsWithValidFilters(): void
    {
        $filters = ['study_level' => 'masters'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate programme statistics retrieved successfully',
            'statistics' => [
                'total_students' => 100,
                'by_study_level' => [
                    'masters' => 70,
                    'doctorate' => 30
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/getStatistics',
                [
                    'operation' => 'getPostgraduateProgrammeStatistics',
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getPostgraduateProgrammeStatistics($filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Register Thesis Submission - Valid Data
     */
    public function testRegisterThesisSubmissionWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $thesisData = [
            'thesis_title' => 'Machine Learning in Healthcare',
            'submission_date' => '2025-01-09',
            'supervisor_staff_id' => 'STAFF001',
            'thesis_type' => 'dissertation'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Thesis submission registered successfully',
            'f4indexno' => $f4indexno,
            'thesis_id' => 'THESIS_2025_001',
            'submitted_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/registerThesisSubmission',
                [
                    'operation' => 'registerThesisSubmission',
                    'f4indexno' => $f4indexno,
                    'thesis_data' => $thesisData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerThesisSubmission($f4indexno, $thesisData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Register Thesis Submission - Missing Required Fields
     */
    public function testRegisterThesisSubmissionWithMissingRequiredFields(): void
    {
        $thesisData = [
            'thesis_title' => 'Machine Learning in Healthcare'
            // Missing submission_date and supervisor_staff_id
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Submission date is required');
        
        $this->resource->registerThesisSubmission('S0123456789', $thesisData);
    }
    
    /**
     * Test Update Research Progress - Valid Data
     */
    public function testUpdateResearchProgressWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $progressData = [
            'progress_percentage' => 75,
            'milestone_status' => 'data_collection',
            'notes' => 'Good progress on data collection phase'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Research progress updated successfully',
            'f4indexno' => $f4indexno,
            'progress_percentage' => 75,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/updateResearchProgress',
                [
                    'operation' => 'updateResearchProgress',
                    'f4indexno' => $f4indexno,
                    'progress_data' => $progressData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateResearchProgress($f4indexno, $progressData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Research Progress - Invalid Progress Percentage
     */
    public function testUpdateResearchProgressWithInvalidProgressPercentage(): void
    {
        $progressData = ['progress_percentage' => 150]; // Invalid percentage
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid progress percentage');
        
        $this->resource->updateResearchProgress('S0123456789', $progressData);
    }
    
    /**
     * Test Assign Supervisor - Valid Data
     */
    public function testAssignSupervisorWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $supervisorStaffId = 'STAFF001';
        $supervisorRole = 'primary';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Supervisor assigned successfully',
            'f4indexno' => $f4indexno,
            'supervisor_staff_id' => $supervisorStaffId,
            'supervisor_role' => $supervisorRole,
            'assigned_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/assignSupervisor',
                [
                    'operation' => 'assignSupervisor',
                    'f4indexno' => $f4indexno,
                    'supervisor_staff_id' => $supervisorStaffId,
                    'supervisor_role' => $supervisorRole
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->assignSupervisor($f4indexno, $supervisorStaffId, $supervisorRole);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Assign Supervisor - Invalid Supervisor Role
     */
    public function testAssignSupervisorWithInvalidSupervisorRole(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid supervisor role');
        
        $this->resource->assignSupervisor('S0123456789', 'STAFF001', 'invalid_role');
    }
    
    /**
     * Test Get Supervisor Assignments - Valid Staff ID
     */
    public function testGetSupervisorAssignmentsWithValidStaffId(): void
    {
        $supervisorStaffId = 'STAFF001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Supervisor assignments retrieved successfully',
            'supervisor_staff_id' => $supervisorStaffId,
            'assignments' => [
                [
                    'f4indexno' => 'S0123456789',
                    'student_name' => 'John Doe',
                    'supervisor_role' => 'primary'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/getSupervisorAssignments',
                [
                    'operation' => 'getSupervisorAssignments',
                    'supervisor_staff_id' => $supervisorStaffId
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getSupervisorAssignments($supervisorStaffId);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Postgraduate Students - Valid Data
     */
    public function testBulkRegisterPostgraduateStudentsWithValidData(): void
    {
        $studentsData = [
            [
                'f4indexno' => 'S0123456789',
                'firstname' => 'John',
                'surname' => 'Doe',
                'programme_code' => 'MSC001',
                'institution_code' => 'INST001',
                'study_level' => 'masters'
            ],
            [
                'f4indexno' => 'S0123456790',
                'firstname' => 'Jane',
                'surname' => 'Smith',
                'programme_code' => 'PHD001',
                'institution_code' => 'INST001',
                'study_level' => 'doctorate'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk postgraduate student registration completed successfully',
            'total_students' => 2,
            'successful_registrations' => 2,
            'failed_registrations' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/bulkRegister',
                [
                    'operation' => 'bulkRegisterPostgraduateStudents',
                    'students_data' => $studentsData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkRegisterPostgraduateStudents($studentsData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Postgraduate Programme Report - Valid Report Criteria
     */
    public function testGeneratePostgraduateProgrammeReportWithValidReportCriteria(): void
    {
        $reportCriteria = [
            'institution_code' => 'INST001',
            'report_type' => 'thesis_submissions'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Postgraduate programme report generated successfully',
            'report_id' => 'RPT_2025_001',
            'report_type' => 'thesis_submissions',
            'generated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/postgraduate/generateReport',
                [
                    'operation' => 'generatePostgraduateProgrammeReport',
                    'report_criteria' => $reportCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->generatePostgraduateProgrammeReport($reportCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
}