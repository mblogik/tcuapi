<?php

/**
 * TCU API Client - Foreign Applicant Resource Session 7 Unit Tests
 * 
 * Unit tests for Session 7 endpoints (3.31-3.35) of the ForeignApplicantResource class.
 * Tests cover validation, request formatting, and response handling for
 * foreign applicant operations including visa processing and document verification.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 7 ForeignApplicantResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\ForeignApplicantResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ForeignApplicantResourceSession7Test extends TestCase
{
    private ForeignApplicantResource $resource;
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
        $this->resource = new ForeignApplicantResource($this->mockClient);
    }
    
    /**
     * Test 3.31: Register Foreign Applicant - Valid Data
     */
    public function testRegisterForeignApplicantWithValidData(): void
    {
        $applicantData = [
            'passport_number' => 'A1234567',
            'firstname' => 'John',
            'surname' => 'Doe',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001',
            'study_level' => 'undergraduate',
            'funding_source' => 'self_sponsored',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'visa_status' => 'pending',
            'passport_expiry_date' => '2030-12-31',
            'date_of_birth' => '1995-05-15'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicant registered successfully',
            'passport_number' => 'A1234567',
            'applicant_id' => 'FOREIGN_2025_001',
            'registered_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/register',
                [
                    'operation' => 'registerForeignApplicant',
                    'applicant_data' => $applicantData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerForeignApplicant($applicantData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.31: Register Foreign Applicant - Missing Required Fields
     */
    public function testRegisterForeignApplicantWithMissingRequiredFields(): void
    {
        $applicantData = [
            'passport_number' => 'A1234567',
            'firstname' => 'John'
            // Missing surname, nationality, country_of_origin, programme_code, institution_code
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Surname is required');
        
        $this->resource->registerForeignApplicant($applicantData);
    }
    
    /**
     * Test 3.31: Register Foreign Applicant - Invalid Passport Number Format
     */
    public function testRegisterForeignApplicantWithInvalidPassportNumber(): void
    {
        $applicantData = [
            'passport_number' => 'invalid',
            'firstname' => 'John',
            'surname' => 'Doe',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid passport number format');
        
        $this->resource->registerForeignApplicant($applicantData);
    }
    
    /**
     * Test 3.31: Register Foreign Applicant - Invalid Nationality Format
     */
    public function testRegisterForeignApplicantWithInvalidNationality(): void
    {
        $applicantData = [
            'passport_number' => 'A1234567',
            'firstname' => 'John',
            'surname' => 'Doe',
            'nationality' => 'invalid',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid nationality format (use ISO country code)');
        
        $this->resource->registerForeignApplicant($applicantData);
    }
    
    /**
     * Test 3.31: Register Foreign Applicant - Invalid Visa Status
     */
    public function testRegisterForeignApplicantWithInvalidVisaStatus(): void
    {
        $applicantData = [
            'passport_number' => 'A1234567',
            'firstname' => 'John',
            'surname' => 'Doe',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001',
            'visa_status' => 'invalid_status'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid visa status');
        
        $this->resource->registerForeignApplicant($applicantData);
    }
    
    /**
     * Test 3.32: Get Foreign Applicant Details - Valid Passport Number
     */
    public function testGetForeignApplicantDetailsWithValidPassportNumber(): void
    {
        $passportNumber = 'A1234567';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicant details retrieved successfully',
            'passport_number' => $passportNumber,
            'firstname' => 'John',
            'surname' => 'Doe',
            'nationality' => 'US',
            'country_of_origin' => 'US',
            'programme_code' => 'BSC001',
            'institution_code' => 'INST001',
            'visa_status' => 'pending',
            'application_status' => 'under_review',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/getDetails',
                [
                    'operation' => 'getForeignApplicantDetails',
                    'passport_number' => $passportNumber
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getForeignApplicantDetails($passportNumber);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.32: Get Foreign Applicant Details - Invalid Passport Number
     */
    public function testGetForeignApplicantDetailsWithInvalidPassportNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid passport number format');
        
        $this->resource->getForeignApplicantDetails('invalid');
    }
    
    /**
     * Test 3.33: Update Foreign Applicant Information - Valid Data
     */
    public function testUpdateForeignApplicantInformationWithValidData(): void
    {
        $passportNumber = 'A1234567';
        $updateData = [
            'email' => 'john.doe.updated@example.com',
            'phone' => '+1987654321',
            'visa_status' => 'approved',
            'application_status' => 'approved'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicant information updated successfully',
            'passport_number' => $passportNumber,
            'updated_fields' => ['email', 'phone', 'visa_status', 'application_status'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/updateInformation',
                [
                    'operation' => 'updateForeignApplicantInformation',
                    'passport_number' => $passportNumber,
                    'update_data' => $updateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateForeignApplicantInformation($passportNumber, $updateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.33: Update Foreign Applicant Information - Empty Update Data
     */
    public function testUpdateForeignApplicantInformationWithEmptyUpdateData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Update data cannot be empty');
        
        $this->resource->updateForeignApplicantInformation('A1234567', []);
    }
    
    /**
     * Test 3.34: Process Visa Application - Valid Data
     */
    public function testProcessVisaApplicationWithValidData(): void
    {
        $passportNumber = 'A1234567';
        $visaData = [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-01',
            'intended_departure_date' => '2027-07-31',
            'duration_of_stay_days' => 730
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Visa application processed successfully',
            'passport_number' => $passportNumber,
            'visa_application_id' => 'VISA_2025_001',
            'visa_status' => 'processing',
            'processed_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/processVisaApplication',
                [
                    'operation' => 'processVisaApplication',
                    'passport_number' => $passportNumber,
                    'visa_data' => $visaData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->processVisaApplication($passportNumber, $visaData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.34: Process Visa Application - Missing Required Fields
     */
    public function testProcessVisaApplicationWithMissingRequiredFields(): void
    {
        $visaData = [
            'visa_type' => 'student'
            // Missing intended_arrival_date and intended_departure_date
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Intended arrival date is required');
        
        $this->resource->processVisaApplication('A1234567', $visaData);
    }
    
    /**
     * Test 3.34: Process Visa Application - Invalid Date Order
     */
    public function testProcessVisaApplicationWithInvalidDateOrder(): void
    {
        $visaData = [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-01',
            'intended_departure_date' => '2025-07-31' // Before arrival date
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Intended departure date must be after intended arrival date');
        
        $this->resource->processVisaApplication('A1234567', $visaData);
    }
    
    /**
     * Test 3.34: Process Visa Application - Invalid Duration
     */
    public function testProcessVisaApplicationWithInvalidDuration(): void
    {
        $visaData = [
            'visa_type' => 'student',
            'intended_arrival_date' => '2025-08-01',
            'intended_departure_date' => '2025-08-02',
            'duration_of_stay_days' => 2000 // Too long
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid duration of stay (must be 1-1825 days)');
        
        $this->resource->processVisaApplication('A1234567', $visaData);
    }
    
    /**
     * Test 3.35: Get Visa Status - Valid Passport Number
     */
    public function testGetVisaStatusWithValidPassportNumber(): void
    {
        $passportNumber = 'A1234567';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Visa status retrieved successfully',
            'passport_number' => $passportNumber,
            'visa_status' => 'approved',
            'visa_type' => 'student',
            'visa_expiry_date' => '2027-07-31',
            'visa_application_date' => '2025-01-09',
            'visa_approval_date' => '2025-01-15'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/getVisaStatus',
                [
                    'operation' => 'getVisaStatus',
                    'passport_number' => $passportNumber
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getVisaStatus($passportNumber);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.35: Get Visa Status - Invalid Passport Number
     */
    public function testGetVisaStatusWithInvalidPassportNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid passport number format');
        
        $this->resource->getVisaStatus('invalid');
    }
    
    /**
     * Test Get Foreign Applicants by Programme - Valid Programme Code
     */
    public function testGetForeignApplicantsByProgrammeWithValidProgrammeCode(): void
    {
        $programmeCode = 'BSC001';
        $filters = ['nationality' => 'US'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicants retrieved successfully',
            'programme_code' => $programmeCode,
            'applicants' => [
                [
                    'passport_number' => 'A1234567',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'nationality' => 'US'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/getByProgramme',
                [
                    'operation' => 'getForeignApplicantsByProgramme',
                    'programme_code' => $programmeCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getForeignApplicantsByProgramme($programmeCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Foreign Applicants - Valid Search Criteria
     */
    public function testSearchForeignApplicantsWithValidSearchCriteria(): void
    {
        $searchCriteria = [
            'nationality' => 'US',
            'programme_code' => 'BSC001',
            'visa_status' => 'approved'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicant search completed successfully',
            'search_results' => [
                [
                    'passport_number' => 'A1234567',
                    'firstname' => 'John',
                    'surname' => 'Doe'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/search',
                [
                    'operation' => 'searchForeignApplicants',
                    'search_criteria' => $searchCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->searchForeignApplicants($searchCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Foreign Applicants - Empty Search Criteria
     */
    public function testSearchForeignApplicantsWithEmptySearchCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Search criteria cannot be empty');
        
        $this->resource->searchForeignApplicants([]);
    }
    
    /**
     * Test Submit Document Verification - Valid Data
     */
    public function testSubmitDocumentVerificationWithValidData(): void
    {
        $passportNumber = 'A1234567';
        $documents = [
            [
                'document_type' => 'passport',
                'document_number' => 'A1234567'
            ],
            [
                'document_type' => 'academic_transcript',
                'document_number' => 'TRANS001'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Document verification submitted successfully',
            'passport_number' => $passportNumber,
            'verification_id' => 'VERIFY_2025_001',
            'documents_submitted' => 2,
            'submitted_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/submitDocumentVerification',
                [
                    'operation' => 'submitDocumentVerification',
                    'passport_number' => $passportNumber,
                    'documents' => $documents
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitDocumentVerification($passportNumber, $documents);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Submit Document Verification - Empty Documents
     */
    public function testSubmitDocumentVerificationWithEmptyDocuments(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Documents array cannot be empty');
        
        $this->resource->submitDocumentVerification('A1234567', []);
    }
    
    /**
     * Test Update Application Status - Valid Data
     */
    public function testUpdateApplicationStatusWithValidData(): void
    {
        $passportNumber = 'A1234567';
        $applicationStatus = 'approved';
        $reason = 'Application meets all requirements';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Application status updated successfully',
            'passport_number' => $passportNumber,
            'application_status' => $applicationStatus,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/updateApplicationStatus',
                [
                    'operation' => 'updateApplicationStatus',
                    'passport_number' => $passportNumber,
                    'application_status' => $applicationStatus,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateApplicationStatus($passportNumber, $applicationStatus, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Application Status - Invalid Status
     */
    public function testUpdateApplicationStatusWithInvalidStatus(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid application status');
        
        $this->resource->updateApplicationStatus('A1234567', 'invalid_status', 'Some reason');
    }
    
    /**
     * Test Get Visa Requirements - Valid Data
     */
    public function testGetVisaRequirementsWithValidData(): void
    {
        $nationality = 'US';
        $studyLevel = 'undergraduate';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Visa requirements retrieved successfully',
            'nationality' => $nationality,
            'study_level' => $studyLevel,
            'visa_required' => true,
            'requirements' => [
                'passport_validity' => '6 months minimum',
                'financial_proof' => 'Bank statement required',
                'health_certificate' => 'Medical examination required'
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/getVisaRequirements',
                [
                    'operation' => 'getVisaRequirements',
                    'nationality' => $nationality,
                    'study_level' => $studyLevel
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getVisaRequirements($nationality, $studyLevel);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Visa Requirements - Invalid Study Level
     */
    public function testGetVisaRequirementsWithInvalidStudyLevel(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid study level');
        
        $this->resource->getVisaRequirements('US', 'invalid_level');
    }
    
    /**
     * Test Bulk Register Foreign Applicants - Valid Data
     */
    public function testBulkRegisterForeignApplicantsWithValidData(): void
    {
        $applicantsData = [
            [
                'passport_number' => 'A1234567',
                'firstname' => 'John',
                'surname' => 'Doe',
                'nationality' => 'US',
                'country_of_origin' => 'US',
                'programme_code' => 'BSC001',
                'institution_code' => 'INST001'
            ],
            [
                'passport_number' => 'B7654321',
                'firstname' => 'Jane',
                'surname' => 'Smith',
                'nationality' => 'CA',
                'country_of_origin' => 'CA',
                'programme_code' => 'BSC002',
                'institution_code' => 'INST001'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk foreign applicant registration completed successfully',
            'total_applicants' => 2,
            'successful_registrations' => 2,
            'failed_registrations' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/bulkRegister',
                [
                    'operation' => 'bulkRegisterForeignApplicants',
                    'applicants_data' => $applicantsData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkRegisterForeignApplicants($applicantsData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Foreign Applicant Report - Valid Report Criteria
     */
    public function testGenerateForeignApplicantReportWithValidReportCriteria(): void
    {
        $reportCriteria = [
            'institution_code' => 'INST001',
            'report_type' => 'visa_status'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Foreign applicant report generated successfully',
            'report_id' => 'RPT_2025_001',
            'report_type' => 'visa_status',
            'generated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreign/generateReport',
                [
                    'operation' => 'generateForeignApplicantReport',
                    'report_criteria' => $reportCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->generateForeignApplicantReport($reportCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
}