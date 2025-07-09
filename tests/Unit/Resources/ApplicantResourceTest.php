<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Models\Data\Applicant;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;
use MBLogik\TCUAPIClient\Resources\ApplicantResource;
use MBLogik\TCUAPIClient\Tests\TestCase;

class ApplicantResourceTest extends TestCase
{
    private ApplicantResource $resource;
    private TCUAPIClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $config = $this->getTestConfiguration();
        $this->client = new TCUAPIClient($config);
        $this->resource = new ApplicantResource($this->client);
    }

    public function testCanCreateResourceWithClient()
    {
        $this->assertInstanceOf(ApplicantResource::class, $this->resource);
        $this->assertSame($this->client, $this->resource->getClient());
    }

    public function testCheckStatusAcceptsStringParameter()
    {
        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->checkStatus('S0123/0001/2023');
    }

    public function testCheckStatusAcceptsRequestObject()
    {
        $request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023');

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->checkStatus($request);
    }

    public function testCheckStatusThrowsValidationExceptionForInvalidRequest()
    {
        $request = CheckStatusRequest::forSingleApplicant('INVALID_FORMAT');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->checkStatus($request);
    }

    public function testCheckStatusThrowsValidationExceptionForInvalidString()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->checkStatus('INVALID_FORMAT');
    }

    public function testCheckStatusMultipleAcceptsArray()
    {
        $indexNumbers = ['S0123/0001/2023', 'S0124/0002/2023'];

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->checkStatusMultiple($indexNumbers);
    }

    public function testCheckStatusMultipleAcceptsRequestObject()
    {
        $request = CheckStatusRequest::forMultipleApplicants(['S0123/0001/2023', 'S0124/0002/2023']);

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->checkStatusMultiple($request);
    }

    public function testCheckStatusMultipleThrowsValidationExceptionForInvalidRequest()
    {
        $request = CheckStatusRequest::forMultipleApplicants(['INVALID_FORMAT']);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->checkStatusMultiple($request);
    }

    public function testCheckStatusMultipleThrowsValidationExceptionForInvalidArray()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->checkStatusMultiple(['INVALID_FORMAT']);
    }

    public function testAddAcceptsApplicantModelWithInstitutionCode()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->add($applicant, 'INST001');
    }

    public function testAddAcceptsRequestObject()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            'John',
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->add($request);
    }

    public function testAddThrowsExceptionWhenInstitutionCodeMissingForApplicantModel()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Institution code is required when passing Applicant model');

        $this->resource->add($applicant);
    }

    public function testAddThrowsValidationExceptionForInvalidApplicantModel()
    {
        $applicant = new Applicant([
            'first_name' => '',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government'
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Applicant validation failed');

        $this->resource->add($applicant, 'INST001');
    }

    public function testAddThrowsValidationExceptionForInvalidRequest()
    {
        $request = AddApplicantRequest::forLocalApplicant(
            'INST001',
            '', // Invalid empty first name
            'Doe',
            'M',
            'S0123/0001/2023',
            2000,
            'Government'
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->add($request);
    }

    public function testUpdateAcceptsApplicantModelWithInstitutionCode()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->update($applicant, 'INST001');
    }

    public function testUpdateThrowsExceptionWhenInstitutionCodeMissingForApplicantModel()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Institution code is required when passing Applicant model');

        $this->resource->update($applicant);
    }

    public function testUpdateThrowsValidationExceptionForInvalidApplicantModel()
    {
        $applicant = new Applicant([
            'first_name' => '',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government'
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Applicant validation failed');

        $this->resource->update($applicant, 'INST001');
    }

    public function testDeleteAcceptsString()
    {
        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->delete('S0123/0001/2023');
    }

    public function testDeleteAcceptsApplicantModel()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->delete($applicant);
    }

    public function testDeleteThrowsValidationExceptionForInvalidString()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->delete('INVALID_FORMAT');
    }

    public function testDeleteThrowsValidationExceptionForInvalidApplicantModel()
    {
        $applicant = new Applicant([
            'first_name' => 'John',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government',
            'form_four_index_number' => 'INVALID_FORMAT'
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Applicant validation failed');

        $this->resource->delete($applicant);
    }

    public function testGetAcceptsString()
    {
        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->get('S0123/0001/2023');
    }

    public function testGetAcceptsApplicantModel()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->get($applicant);
    }

    public function testGetThrowsValidationExceptionForInvalidString()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->get('INVALID_FORMAT');
    }

    public function testGetThrowsValidationExceptionForInvalidApplicantModel()
    {
        $applicant = new Applicant([
            'first_name' => 'John',
            'surname' => 'Doe',
            'gender' => 'M',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government',
            'form_four_index_number' => 'INVALID_FORMAT'
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Applicant validation failed');

        $this->resource->get($applicant);
    }

    public function testGetAllAcceptsOptionalParameters()
    {
        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->getAll('INST001', 1, 10);
    }

    public function testGetAllAcceptsNullParameters()
    {
        $this->expectException(\MBLogik\TCUAPIClient\Exceptions\TCUAPIException::class);

        $this->resource->getAll();
    }

    public function testGetAllThrowsValidationExceptionForInvalidInstitutionCode()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->getAll('INVALID@CODE');
    }

    public function testGetAllThrowsValidationExceptionForInvalidPage()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->getAll('INST001', 0);
    }

    public function testGetAllThrowsValidationExceptionForInvalidLimit()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->resource->getAll('INST001', 1, 0);
    }

    public function testResourceUsesCorrectClient()
    {
        $this->assertSame($this->client, $this->resource->getClient());
    }

    public function testResourceHasCorrectName()
    {
        $this->assertEquals('applicants', $this->resource->getName());
    }

    public function testResourceHasCorrectBaseEndpoint()
    {
        $this->assertEquals('/applicants', $this->resource->getBaseEndpoint());
    }
}
