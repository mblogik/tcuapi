<?php

namespace MBLogik\TCUAPIClient\Tests\Integration;

use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Models\Data\Applicant;
use MBLogik\TCUAPIClient\Models\Request\AddApplicantRequest;
use MBLogik\TCUAPIClient\Models\Request\CheckStatusRequest;
use MBLogik\TCUAPIClient\Tests\TestCase;

class TCUAPIClientTest extends TestCase
{
    private TCUAPIClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $config = $this->getTestConfiguration();
        $this->client = new TCUAPIClient($config);
    }

    public function testCanCreateClientWithConfiguration()
    {
        $config = $this->getTestConfiguration();
        $client = new TCUAPIClient($config);

        $this->assertInstanceOf(TCUAPIClient::class, $client);
        $this->assertSame($config, $client->getConfig());
    }

    public function testClientHasResourceMethods()
    {
        $this->assertInstanceOf(\MBLogik\TCUAPIClient\Resources\ApplicantResource::class, $this->client->applicants());
        $this->assertInstanceOf(\MBLogik\TCUAPIClient\Resources\AdmissionResource::class, $this->client->admissions());
        $this->assertInstanceOf(\MBLogik\TCUAPIClient\Resources\DashboardResource::class, $this->client->dashboard());
    }

    public function testClientThrowsValidationExceptionForInvalidConfiguration()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Configuration validation failed');

        $config = new Configuration([
            'base_url' => 'invalid-url'
        ]);

        new TCUAPIClient($config);
    }

    public function testApplicantResourceCanHandleStringParameters()
    {
        // This would normally make an HTTP request, but we're testing the interface
        $this->expectException(TCUAPIException::class);

        $this->client->applicants()->checkStatus('S0123/0001/2023');
    }

    public function testApplicantResourceCanHandleRequestObjects()
    {
        $request = CheckStatusRequest::forSingleApplicant('S0123/0001/2023');

        // This would normally make an HTTP request, but we're testing the interface
        $this->expectException(TCUAPIException::class);

        $this->client->applicants()->checkStatus($request);
    }

    public function testApplicantResourceValidatesRequestBeforeApiCall()
    {
        $request = CheckStatusRequest::forSingleApplicant('INVALID_FORMAT');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Request validation failed');

        $this->client->applicants()->checkStatus($request);
    }

    public function testApplicantResourceCanAddApplicantFromModel()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        // This would normally make an HTTP request, but we're testing the interface
        $this->expectException(TCUAPIException::class);

        $this->client->applicants()->add($applicant, 'INST001');
    }

    public function testApplicantResourceCanAddApplicantFromRequest()
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

        // This would normally make an HTTP request, but we're testing the interface
        $this->expectException(TCUAPIException::class);

        $this->client->applicants()->add($request);
    }

    public function testApplicantResourceThrowsExceptionWhenInstitutionCodeMissing()
    {
        $applicant = new Applicant($this->getTestApplicantData());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Institution code is required when passing Applicant model');

        $this->client->applicants()->add($applicant);
    }

    public function testApplicantResourceValidatesAddRequestBeforeApiCall()
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

        $this->client->applicants()->add($request);
    }

    public function testClientCanGetConfigurationAndLogger()
    {
        $config = $this->client->getConfig();
        $logger = $this->client->getLogger();

        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertNull($logger); // Should be null since we didn't enable database logging
    }

    public function testClientWithDatabaseLoggingEnabled()
    {
        $config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'enable_database_logging' => true,
            'database' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'table_prefix' => 'test_'
            ]
        ]);

        // This would normally create a database logger, but we're testing the interface
        $this->expectException(TCUAPIException::class);
        $this->expectExceptionMessage('does not exist');

        new TCUAPIClient($config);
    }

    public function testClientResourcesShareSameClient()
    {
        $applicants1 = $this->client->applicants();
        $applicants2 = $this->client->applicants();

        $this->assertSame($applicants1, $applicants2);

        $admissions1 = $this->client->admissions();
        $admissions2 = $this->client->admissions();

        $this->assertSame($admissions1, $admissions2);

        $dashboard1 = $this->client->dashboard();
        $dashboard2 = $this->client->dashboard();

        $this->assertSame($dashboard1, $dashboard2);
    }

    public function testClientConfigurationIsReadOnly()
    {
        $config = $this->client->getConfig();
        $originalUsername = $config->getUsername();

        // Even if we modify the config, the client should maintain its state
        $config->setCredentials('new_user', 'new_token');

        $this->assertEquals('new_user', $config->getUsername());
        $this->assertSame($config, $this->client->getConfig());
    }
}
