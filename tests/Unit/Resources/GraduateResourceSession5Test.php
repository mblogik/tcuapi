<?php

/**
 * TCU API Client - Graduate Resource Session 5 Unit Tests
 * 
 * Unit tests for Session 5 endpoints (3.21-3.23) of the GraduateResource class.
 * Tests cover validation, request formatting, and response handling for
 * graduate registration and management operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 5 GraduateResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\GraduateResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GraduateResourceSession5Test extends TestCase
{
    private GraduateResource $resource;
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
        $this->resource = new GraduateResource($this->mockClient);
    }
    
    /**
     * Test 3.21: Register Graduate - Valid Data
     */
    public function testRegisterGraduateWithValidData(): void
    {
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'upper_second',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate registered successfully',
            'f4indexno' => 'S0123456789',
            'graduate_id' => 'GRAD_2025_001',
            'registered_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/register',
                [
                    'operation' => 'registerGraduate',
                    'graduate_data' => $graduateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->registerGraduate($graduateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.21: Register Graduate - Missing Required Fields
     */
    public function testRegisterGraduateWithMissingRequiredFields(): void
    {
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John'
            // Missing surname, programme_code, institution_code, graduation_year
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Surname is required');
        
        $this->resource->registerGraduate($graduateData);
    }
    
    /**
     * Test 3.21: Register Graduate - Invalid F4 Index Number
     */
    public function testRegisterGraduateWithInvalidF4IndexNumber(): void
    {
        $graduateData = [
            'f4indexno' => 'invalid_f4',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->registerGraduate($graduateData);
    }
    
    /**
     * Test 3.21: Register Graduate - Invalid Email
     */
    public function testRegisterGraduateWithInvalidEmail(): void
    {
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'email' => 'invalid_email'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->registerGraduate($graduateData);
    }
    
    /**
     * Test 3.21: Register Graduate - Invalid Graduation Year
     */
    public function testRegisterGraduateWithInvalidGraduationYear(): void
    {
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 1980 // Too old
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid graduation year');
        
        $this->resource->registerGraduate($graduateData);
    }
    
    /**
     * Test 3.21: Register Graduate - Invalid Degree Classification
     */
    public function testRegisterGraduateWithInvalidDegreeClassification(): void
    {
        $graduateData = [
            'f4indexno' => 'S0123456789',
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'invalid_classification'
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid degree classification');
        
        $this->resource->registerGraduate($graduateData);
    }
    
    /**
     * Test 3.22: Get Graduate Details - Valid F4 Index Number
     */
    public function testGetGraduateDetailsWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate details retrieved successfully',
            'f4indexno' => $f4indexno,
            'firstname' => 'John',
            'surname' => 'Doe',
            'programme_code' => 'PROG001',
            'institution_code' => 'INST001',
            'graduation_year' => 2024,
            'degree_classification' => 'upper_second',
            'email' => 'john.doe@example.com',
            'phone' => '+255712345678'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/getDetails',
                [
                    'operation' => 'getGraduateDetails',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getGraduateDetails($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.22: Get Graduate Details - Invalid F4 Index Number
     */
    public function testGetGraduateDetailsWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getGraduateDetails('invalid_f4');
    }
    
    /**
     * Test 3.23: Update Graduate Information - Valid Data
     */
    public function testUpdateGraduateInformationWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $updateData = [
            'email' => 'newemail@example.com',
            'phone' => '+255787654321',
            'degree_classification' => 'first_class'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate information updated successfully',
            'f4indexno' => $f4indexno,
            'updated_fields' => ['email', 'phone', 'degree_classification'],
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/updateInformation',
                [
                    'operation' => 'updateGraduateInformation',
                    'f4indexno' => $f4indexno,
                    'update_data' => $updateData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateGraduateInformation($f4indexno, $updateData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.23: Update Graduate Information - Invalid F4 Index Number
     */
    public function testUpdateGraduateInformationWithInvalidF4IndexNumber(): void
    {
        $updateData = ['email' => 'newemail@example.com'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->updateGraduateInformation('invalid_f4', $updateData);
    }
    
    /**
     * Test 3.23: Update Graduate Information - Empty Update Data
     */
    public function testUpdateGraduateInformationWithEmptyUpdateData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Update data cannot be empty');
        
        $this->resource->updateGraduateInformation('S0123456789', []);
    }
    
    /**
     * Test 3.23: Update Graduate Information - Invalid Email
     */
    public function testUpdateGraduateInformationWithInvalidEmail(): void
    {
        $updateData = ['email' => 'invalid_email'];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->resource->updateGraduateInformation('S0123456789', $updateData);
    }
    
    /**
     * Test Get Graduates by Programme - Valid Programme Code
     */
    public function testGetGraduatesByProgrammeWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $filters = ['graduation_year' => 2024];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduates retrieved successfully',
            'programme_code' => $programmeCode,
            'graduates' => [
                [
                    'f4indexno' => 'S0123456789',
                    'firstname' => 'John',
                    'surname' => 'Doe',
                    'graduation_year' => 2024
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/getByProgramme',
                [
                    'operation' => 'getGraduatesByProgramme',
                    'programme_code' => $programmeCode,
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getGraduatesByProgramme($programmeCode, $filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Graduates by Institution - Valid Institution Code
     */
    public function testGetGraduatesByInstitutionWithValidInstitutionCode(): void
    {
        $institutionCode = 'INST001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduates retrieved successfully',
            'institution_code' => $institutionCode,
            'graduates' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/getByInstitution',
                [
                    'operation' => 'getGraduatesByInstitution',
                    'institution_code' => $institutionCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getGraduatesByInstitution($institutionCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Graduates - Valid Search Criteria
     */
    public function testSearchGraduatesWithValidSearchCriteria(): void
    {
        $searchCriteria = [
            'firstname' => 'John',
            'programme_code' => 'PROG001'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate search completed successfully',
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
                '/graduates/search',
                [
                    'operation' => 'searchGraduates',
                    'search_criteria' => $searchCriteria
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->searchGraduates($searchCriteria);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Search Graduates - Empty Search Criteria
     */
    public function testSearchGraduatesWithEmptySearchCriteria(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Search criteria cannot be empty');
        
        $this->resource->searchGraduates([]);
    }
    
    /**
     * Test Get Graduate Statistics - Valid Filters
     */
    public function testGetGraduateStatisticsWithValidFilters(): void
    {
        $filters = ['programme_code' => 'PROG001'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate statistics retrieved successfully',
            'statistics' => [
                'total_graduates' => 150,
                'by_classification' => [
                    'first_class' => 20,
                    'upper_second' => 80,
                    'lower_second' => 40,
                    'third_class' => 10
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/getStatistics',
                [
                    'operation' => 'getGraduateStatistics',
                    'filters' => $filters
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getGraduateStatistics($filters);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Verify Graduate Credentials - Valid Data
     */
    public function testVerifyGraduateCredentialsWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $credentialsData = [
            'certificate_number' => 'CERT123456',
            'verification_type' => 'academic'
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate credentials verified successfully',
            'f4indexno' => $f4indexno,
            'verification_status' => 'verified',
            'verified_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/verifyCredentials',
                [
                    'operation' => 'verifyGraduateCredentials',
                    'f4indexno' => $f4indexno,
                    'credentials_data' => $credentialsData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->verifyGraduateCredentials($f4indexno, $credentialsData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Graduate Certificate - Valid Data
     */
    public function testGenerateGraduateCertificateWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $certificateType = 'completion';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Graduate certificate generated successfully',
            'f4indexno' => $f4indexno,
            'certificate_type' => $certificateType,
            'certificate_id' => 'CERT_2025_001',
            'generated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/generateCertificate',
                [
                    'operation' => 'generateGraduateCertificate',
                    'f4indexno' => $f4indexno,
                    'certificate_type' => $certificateType
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->generateGraduateCertificate($f4indexno, $certificateType);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Generate Graduate Certificate - Invalid Certificate Type
     */
    public function testGenerateGraduateCertificateWithInvalidCertificateType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid certificate type');
        
        $this->resource->generateGraduateCertificate('S0123456789', 'invalid_type');
    }
    
    /**
     * Test Bulk Register Graduates - Valid Data
     */
    public function testBulkRegisterGraduatesWithValidData(): void
    {
        $graduatesData = [
            [
                'f4indexno' => 'S0123456789',
                'firstname' => 'John',
                'surname' => 'Doe',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001',
                'graduation_year' => 2024
            ],
            [
                'f4indexno' => 'S0123456790',
                'firstname' => 'Jane',
                'surname' => 'Smith',
                'programme_code' => 'PROG002',
                'institution_code' => 'INST001',
                'graduation_year' => 2024
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Bulk graduate registration completed successfully',
            'total_graduates' => 2,
            'successful_registrations' => 2,
            'failed_registrations' => 0
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/graduates/bulkRegister',
                [
                    'operation' => 'bulkRegisterGraduates',
                    'graduates_data' => $graduatesData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->bulkRegisterGraduates($graduatesData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Bulk Register Graduates - Empty Data
     */
    public function testBulkRegisterGraduatesWithEmptyData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Graduates data array cannot be empty');
        
        $this->resource->bulkRegisterGraduates([]);
    }
    
    /**
     * Test Bulk Register Graduates - Invalid F4 Index Number
     */
    public function testBulkRegisterGraduatesWithInvalidF4IndexNumber(): void
    {
        $graduatesData = [
            [
                'f4indexno' => 'invalid_f4',
                'firstname' => 'John',
                'surname' => 'Doe',
                'programme_code' => 'PROG001',
                'institution_code' => 'INST001',
                'graduation_year' => 2024
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number for graduate at index 0');
        
        $this->resource->bulkRegisterGraduates($graduatesData);
    }
}