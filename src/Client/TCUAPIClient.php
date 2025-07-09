<?php

/**
 * TCU API Client - Main Client Class
 *
 * This file contains the main client class for interacting with the Tanzania Commission
 * for Universities (TCU) API. It provides a comprehensive interface for all API operations
 * including applicant management, admission processing, and dashboard operations.
 *
 * @package    MBLogik\TCUAPIClient
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 *
 * @purpose    Main entry point for TCU API operations with enterprise-level features
 *             including retry logic, comprehensive error handling, and database logging.
 */

namespace MBLogik\TCUAPIClient\Client;

use Httpful\Exception\ConnectionErrorException;
use Httpful\Request;
use Httpful\Response;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\AuthenticationException;
use MBLogik\TCUAPIClient\Exceptions\NetworkException;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use MBLogik\TCUAPIClient\Http\Logger\DatabaseLogger;
use MBLogik\TCUAPIClient\Resources\AdmissionResource;
use MBLogik\TCUAPIClient\Resources\ApplicantResource;
use MBLogik\TCUAPIClient\Resources\DashboardResource;
use MBLogik\TCUAPIClient\Resources\TransferResource;
use MBLogik\TCUAPIClient\Resources\VerificationResource;
use MBLogik\TCUAPIClient\Resources\EnrollmentResource;
use MBLogik\TCUAPIClient\Resources\GraduateResource;
use MBLogik\TCUAPIClient\Resources\StaffResource;
use MBLogik\TCUAPIClient\Resources\NonDegreeResource;
use MBLogik\TCUAPIClient\Resources\PostgraduateResource;
use MBLogik\TCUAPIClient\Resources\ForeignApplicantResource;



class TCUAPIClient
{
    private Configuration $config;
    private ?DatabaseLogger $logger = null;
    private ApplicantResource $applicants;
    private AdmissionResource $admissions;
    private DashboardResource $dashboard;
    private TransferResource $transfers;
    private VerificationResource $verification;
    private EnrollmentResource $enrollment;
    private GraduateResource $graduates;
    private StaffResource $staff;
    private NonDegreeResource $nonDegree;
    private PostgraduateResource $postgraduate;
    private ForeignApplicantResource $foreignApplicants;

    public function __construct(Configuration $config)
    {
        $this->config = $config;

        // Validate configuration
        $errors = $this->config->validate();
        if (!empty($errors)) {
            throw new ValidationException("Configuration validation failed", $errors);
        }

        // Initialize database logger if enabled
        if ($this->config->isDatabaseLoggingEnabled() && $this->config->getDatabaseConfig()) {
            $this->logger = new DatabaseLogger($this->config->getDatabaseConfig());
        }

        // Configure HTTP client defaults for XML
        Request::init()
            ->sendsType('application/xml')
            ->expectsType('application/xml')
            ->timeout($this->config->getTimeout());

        // Initialize resource classes
        $this->applicants = new ApplicantResource($this);
        $this->admissions = new AdmissionResource($this);
        $this->dashboard = new DashboardResource($this);
        $this->transfers = new TransferResource($this);
        $this->verification = new VerificationResource($this);
        $this->enrollment = new EnrollmentResource($this);
        $this->graduates = new GraduateResource($this);
        $this->staff = new StaffResource($this);
        $this->nonDegree = new NonDegreeResource($this);
        $this->postgraduate = new PostgraduateResource($this);
        $this->foreignApplicants = new ForeignApplicantResource($this);
    }

    public function applicants(): ApplicantResource
    {
        return $this->applicants;
    }

    public function admissions(): AdmissionResource
    {
        return $this->admissions;
    }

    public function dashboard(): DashboardResource
    {
        return $this->dashboard;
    }

    public function transfers(): TransferResource
    {
        return $this->transfers;
    }

    public function verification(): VerificationResource
    {
        return $this->verification;
    }

    public function enrollment(): EnrollmentResource
    {
        return $this->enrollment;
    }

    public function graduates(): GraduateResource
    {
        return $this->graduates;
    }

    public function staff(): StaffResource
    {
        return $this->staff;
    }

    public function nonDegree(): NonDegreeResource
    {
        return $this->nonDegree;
    }

    public function postgraduate(): PostgraduateResource
    {
        return $this->postgraduate;
    }

    public function foreignApplicants(): ForeignApplicantResource
    {
        return $this->foreignApplicants;
    }

    public function makeRequest(string $endpoint, array $requestParameters = [], string $method = 'POST'): array
    {
        $url = $this->config->getBaseUrl() . $endpoint;
        $logId = null;
        $startTime = microtime(true);

        // Generate XML request body
        $xmlBody = $this->generateXMLRequest($requestParameters);

        // Log request start
        if ($this->logger) {
            $logId = $this->logger->logRequest([
                'endpoint' => $endpoint,
                'method' => $method,
                'request_headers' => $this->getHeaders(),
                'request_body' => $xmlBody,
                'username' => $this->config->getUsername()
            ]);
        }

        try {
            $response = $this->executeRequest($url, $xmlBody, $method);
            $executionTime = microtime(true) - $startTime;

            // Log successful response
            if ($this->logger && $logId) {
                $this->logger->logResponse($logId, [
                    'response_code' => $response->code,
                    'response_headers' => $response->headers,
                    'response_body' => $response->body,
                    'execution_time' => $executionTime,
                    'status' => 'completed'
                ]);
            }

            return $this->processResponse($response);

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            // Log error response
            if ($this->logger && $logId) {
                $this->logger->logResponse($logId, [
                    'response_code' => $e->getCode(),
                    'execution_time' => $executionTime,
                    'status' => 'error',
                    'error_message' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    private function executeRequest(string $url, string $xmlBody, string $method): Response
    {
        $attempts = 0;
        $maxAttempts = $this->config->getRetryAttempts();

        while ($attempts < $maxAttempts) {
            try {
                $request = Request::$method($url)
                    ->body($xmlBody)
                    ->addHeaders($this->getHeaders());

                $response = $request->send();

                if ($response->hasErrors()) {
                    throw new TCUAPIException(
                        "API Error: " . ($response->body ?? 'Unknown error'),
                        $response->code
                    );
                }

                return $response;

            } catch (ConnectionErrorException $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw new NetworkException("Connection failed after {$maxAttempts} attempts: " . $e->getMessage(), 0, $e);
                }

                // Wait before retrying (exponential backoff)
                usleep(pow(2, $attempts) * 100000); // 0.1s, 0.2s, 0.4s, etc.
            }
        }

        throw new NetworkException("Max retry attempts exceeded");
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/xml',
            'Accept' => 'application/xml',
            'User-Agent' => 'TCU-API-Client/1.0'
        ];
    }

    private function processResponse(Response $response): array
    {
        if ($response->code === 401) {
            throw new AuthenticationException("Authentication failed. Please check your credentials.");
        }

        if ($response->code >= 400) {
            throw new TCUAPIException(
                "API request failed with status {$response->code}",
                $response->code
            );
        }

        return $this->parseXMLResponse($response->body);
    }

    /**
     * Generate XML request body with proper TCU API format
     */
    private function generateXMLRequest(array $requestParameters): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        // Create root Request element
        $request = $xml->createElement('Request');
        $xml->appendChild($request);

        // Create UsernameToken element
        $usernameToken = $xml->createElement('UsernameToken');
        $request->appendChild($usernameToken);

        // Add Username
        $username = $xml->createElement('Username', $this->config->getUsername());
        $usernameToken->appendChild($username);

        // Add SessionToken
        $sessionToken = $xml->createElement('SessionToken', $this->config->getSecurityToken());
        $usernameToken->appendChild($sessionToken);

        // Check if we have multiple RequestParameters blocks (for endpoints like add)
        if (is_array($requestParameters) && isset($requestParameters[0]) && is_array($requestParameters[0])) {
            // Multiple RequestParameters blocks
            foreach ($requestParameters as $parameterBlock) {
                $requestParams = $xml->createElement('RequestParameters');
                $request->appendChild($requestParams);

                foreach ($parameterBlock as $key => $value) {
                    if (is_array($value)) {
                        // Handle multiple values (like multiple f4indexno in checkStatus)
                        foreach ($value as $item) {
                            $param = $xml->createElement($key, htmlspecialchars($item));
                            $requestParams->appendChild($param);
                        }
                    } else {
                        $param = $xml->createElement($key, htmlspecialchars($value));
                        $requestParams->appendChild($param);
                    }
                }
            }
        } else {
            // Single RequestParameters block
            $requestParams = $xml->createElement('RequestParameters');
            $request->appendChild($requestParams);

            // Add request parameters
            foreach ($requestParameters as $key => $value) {
                if (is_array($value)) {
                    // Handle multiple values (like multiple f4indexno in checkStatus)
                    foreach ($value as $item) {
                        $param = $xml->createElement($key, htmlspecialchars($item));
                        $requestParams->appendChild($param);
                    }
                } else {
                    $param = $xml->createElement($key, htmlspecialchars($value));
                    $requestParams->appendChild($param);
                }
            }
        }

        return $xml->saveXML();
    }

    /**
     * Parse XML response and convert to array
     */
    private function parseXMLResponse(string $xmlString): array
    {
        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            throw new TCUAPIException("Invalid XML response received from server");
        }

        // Convert SimpleXML to array
        $result = json_decode(json_encode($xml), true);

        return $result;
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    public function getLogger(): ?DatabaseLogger
    {
        return $this->logger;
    }
}
