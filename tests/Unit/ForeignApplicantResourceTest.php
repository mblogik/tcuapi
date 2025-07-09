<?php

/**
 * Unit Tests for ForeignApplicantResource
 * 
 * Comprehensive test suite for foreign applicant operations (3.31-3.35)
 * covering registration, details retrieval, visa processing, and status checks.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MBLogik\TCUAPIClient\Resources\ForeignApplicantResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;

class ForeignApplicantResourceTest extends TestCase
{
    private ForeignApplicantResource $foreignApplicantResource;
    private MockObject $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(TCUAPIClient::class);
        $this->foreignApplicantResource = new ForeignApplicantResource($this->mockClient);
    }

    /**
     * Test registerForeignApplicant with valid data
     */
    public function testRegisterForeignApplicantWithValidData(): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => 'AB123456',
            'nationality' => 'US',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'john.doe@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        $expectedResponse = [
            'status' => 'success',
            'message' => 'Foreign applicant registered successfully',
            'applicantId' => 'FA2025001'
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreignApplicants/register',
                $applicantData
            )
            ->willReturn($expectedResponse);

        $response = $this->foreignApplicantResource->registerForeignApplicant($applicantData);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Test registerForeignApplicant with invalid passport number format
     */
    public function testRegisterForeignApplicantWithInvalidPassportNumber(): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => 'invalid123',
            'nationality' => 'US',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'john.doe@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->registerForeignApplicant($applicantData);
    }

    /**
     * Test registerForeignApplicant with invalid nationality code
     */
    public function testRegisterForeignApplicantWithInvalidNationality(): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => 'AB123456',
            'nationality' => 'INVALID',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'john.doe@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->registerForeignApplicant($applicantData);
    }

    /**
     * Test registerForeignApplicant with invalid email
     */
    public function testRegisterForeignApplicantWithInvalidEmail(): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => 'AB123456',
            'nationality' => 'US',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'invalid-email',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->registerForeignApplicant($applicantData);
    }

    /**
     * Test getForeignApplicantDetails with valid passport number
     */
    public function testGetForeignApplicantDetailsWithValidPassportNumber(): void
    {
        $passportNumber = 'AB123456';

        $expectedResponse = [
            'status' => 'success',
            'applicant' => [
                'applicantId' => 'FA2025001',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'passportNumber' => 'AB123456',
                'nationality' => 'US',
                'status' => 'active'
            ]
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreignApplicants/details',
                ['passportNumber' => $passportNumber]
            )
            ->willReturn($expectedResponse);

        $response = $this->foreignApplicantResource->getForeignApplicantDetails($passportNumber);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Test getForeignApplicantDetails with invalid passport number
     */
    public function testGetForeignApplicantDetailsWithInvalidPassportNumber(): void
    {
        $passportNumber = 'invalid123';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->getForeignApplicantDetails($passportNumber);
    }

    /**
     * Test processVisaApplication with valid data
     */
    public function testProcessVisaApplicationWithValidData(): void
    {
        $applicationData = [
            'passportNumber' => 'AB123456',
            'visaType' => 'student',
            'purposeOfVisit' => 'education',
            'durationOfStay' => '4 years',
            'sponsorshipDetails' => 'Self-sponsored',
            'accommodationArrangements' => 'University hostel'
        ];

        $expectedResponse = [
            'status' => 'success',
            'message' => 'Visa application processed successfully',
            'applicationId' => 'VA2025001',
            'estimatedProcessingTime' => '14 days'
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreignApplicants/visa/process',
                $applicationData
            )
            ->willReturn($expectedResponse);

        $response = $this->foreignApplicantResource->processVisaApplication($applicationData);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Test processVisaApplication with invalid passport number
     */
    public function testProcessVisaApplicationWithInvalidPassportNumber(): void
    {
        $applicationData = [
            'passportNumber' => 'invalid123',
            'visaType' => 'student',
            'purposeOfVisit' => 'education',
            'durationOfStay' => '4 years',
            'sponsorshipDetails' => 'Self-sponsored',
            'accommodationArrangements' => 'University hostel'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->processVisaApplication($applicationData);
    }

    /**
     * Test getVisaStatus with valid application ID
     */
    public function testGetVisaStatusWithValidApplicationId(): void
    {
        $applicationId = 'VA2025001';

        $expectedResponse = [
            'status' => 'success',
            'visaApplication' => [
                'applicationId' => 'VA2025001',
                'status' => 'under_review',
                'submissionDate' => '2025-01-09',
                'estimatedCompletion' => '2025-01-23',
                'currentStage' => 'Document verification'
            ]
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreignApplicants/visa/status',
                ['applicationId' => $applicationId]
            )
            ->willReturn($expectedResponse);

        $response = $this->foreignApplicantResource->getVisaStatus($applicationId);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Test getVisaStatus with empty application ID
     */
    public function testGetVisaStatusWithEmptyApplicationId(): void
    {
        $applicationId = '';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->getVisaStatus($applicationId);
    }

    /**
     * Test updateForeignApplicantInformation with valid data
     */
    public function testUpdateForeignApplicantInformationWithValidData(): void
    {
        $passportNumber = 'AB123456';
        $updateData = [
            'mobileNumber' => '+255766654321',
            'emailAddress' => 'john.updated@example.com',
            'currentAddress' => '123 Updated Street, Dar es Salaam'
        ];

        $expectedResponse = [
            'status' => 'success',
            'message' => 'Foreign applicant information updated successfully'
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/foreignApplicants/update',
                array_merge(['passportNumber' => $passportNumber], $updateData)
            )
            ->willReturn($expectedResponse);

        $response = $this->foreignApplicantResource->updateForeignApplicantInformation($passportNumber, $updateData);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Test updateForeignApplicantInformation with invalid passport number
     */
    public function testUpdateForeignApplicantInformationWithInvalidPassportNumber(): void
    {
        $passportNumber = 'invalid123';
        $updateData = [
            'mobileNumber' => '+255766654321',
            'emailAddress' => 'john.updated@example.com'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->updateForeignApplicantInformation($passportNumber, $updateData);
    }

    /**
     * Test updateForeignApplicantInformation with invalid email in update data
     */
    public function testUpdateForeignApplicantInformationWithInvalidEmailInUpdateData(): void
    {
        $passportNumber = 'AB123456';
        $updateData = [
            'emailAddress' => 'invalid-email'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->foreignApplicantResource->updateForeignApplicantInformation($passportNumber, $updateData);
    }

    /**
     * Test passport number validation patterns
     * 
     * @dataProvider passportNumberProvider
     */
    public function testPassportNumberValidation(string $passportNumber, bool $isValid): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => $passportNumber,
            'nationality' => 'US',
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'john.doe@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        if (!$isValid) {
            $this->expectException(ValidationException::class);
        } else {
            $this->mockClient
                ->expects($this->once())
                ->method('makeRequest')
                ->willReturn(['status' => 'success']);
        }

        $this->foreignApplicantResource->registerForeignApplicant($applicantData);
    }

    /**
     * Data provider for passport number validation tests
     */
    public function passportNumberProvider(): array
    {
        return [
            // Valid passport numbers
            ['AB123456', true],
            ['123456', true],
            ['ABCDEF', true],
            ['A1B2C3', true],
            ['X9Y8Z7W6V5U4', true],
            ['123456789012345', true],
            
            // Invalid passport numbers
            ['AB12', false],                    // Too short
            ['ABCDEFGHIJK123456789012', false], // Too long
            ['ab123456', false],                // Lowercase letters
            ['AB 123456', false],               // Contains space
            ['AB-123456', false],               // Contains hyphen
            ['', false],                        // Empty
        ];
    }

    /**
     * Test nationality code validation
     * 
     * @dataProvider nationalityCodeProvider
     */
    public function testNationalityCodeValidation(string $nationality, bool $isValid): void
    {
        $applicantData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'passportNumber' => 'AB123456',
            'nationality' => $nationality,
            'dateOfBirth' => '1995-05-15',
            'emailAddress' => 'john.doe@example.com',
            'mobileNumber' => '+255766123456',
            'programmeCode' => 'UD023',
            'academicYear' => '2025/2026'
        ];

        if (!$isValid) {
            $this->expectException(ValidationException::class);
        } else {
            $this->mockClient
                ->expects($this->once())
                ->method('makeRequest')
                ->willReturn(['status' => 'success']);
        }

        $this->foreignApplicantResource->registerForeignApplicant($applicantData);
    }

    /**
     * Data provider for nationality code validation tests
     */
    public function nationalityCodeProvider(): array
    {
        return [
            // Valid ISO codes
            ['US', true],
            ['GB', true],
            ['KE', true],
            ['ZA', true],
            ['CN', true],
            ['IN', true],
            
            // Invalid codes
            ['USA', false],     // 3 letters
            ['U', false],       // 1 letter
            ['us', false],      // Lowercase
            ['1A', false],      // Contains number
            ['', false],        // Empty
            ['XX', false],      // Invalid ISO code
        ];
    }
}