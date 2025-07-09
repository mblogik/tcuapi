<?php

/**
 * TCU API Client - XML Request Builder
 * 
 * This file contains the RequestBuilder class for constructing XML requests
 * to the TCU API. It handles proper XML formatting, authentication token
 * embedding, and parameter validation according to TCU API specifications.
 * 
 * @package    MBLogik\TCUAPIClient\Xml
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    XML request construction for TCU API with proper formatting,
 *             authentication embedding, and parameter validation.
 */

namespace MBLogik\TCUAPIClient\Xml;

use MBLogik\TCUAPIClient\Authentication\UsernameToken;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

class RequestBuilder
{
    private const XML_DECLARATION = '<?xml version="1.0" encoding="UTF-8"?>';
    private const SOAP_NAMESPACE = 'http://schemas.xmlsoap.org/soap/envelope/';
    private const TCU_NAMESPACE = 'http://www.tcu.go.tz/';
    
    /**
     * Build XML request with authentication and parameters
     */
    public function buildRequest(UsernameToken $token, array $parameters = []): string
    {
        try {
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            
            // Create SOAP envelope
            $envelope = $xml->createElementNS(self::SOAP_NAMESPACE, 'soap:Envelope');
            $envelope->setAttribute('xmlns:tcu', self::TCU_NAMESPACE);
            $xml->appendChild($envelope);
            
            // Create SOAP header with authentication
            $header = $xml->createElement('soap:Header');
            $envelope->appendChild($header);
            
            $this->addAuthenticationHeader($xml, $header, $token);
            
            // Create SOAP body with parameters
            $body = $xml->createElement('soap:Body');
            $envelope->appendChild($body);
            
            $this->addRequestParameters($xml, $body, $parameters);
            
            return $xml->saveXML();
            
        } catch (\Exception $e) {
            throw new TCUAPIException('Failed to build XML request: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Add authentication header to request
     */
    private function addAuthenticationHeader(\DOMDocument $xml, \DOMElement $header, UsernameToken $token): void
    {
        $security = $xml->createElement('Security');
        $header->appendChild($security);
        
        $usernameToken = $xml->createElement('UsernameToken');
        $security->appendChild($usernameToken);
        
        $username = $xml->createElement('Username', htmlspecialchars($token->getUsername()));
        $usernameToken->appendChild($username);
        
        $sessionToken = $xml->createElement('SessionToken', htmlspecialchars($token->getSessionToken()));
        $usernameToken->appendChild($sessionToken);
        
        $timestamp = $xml->createElement('Timestamp', date('Y-m-d\TH:i:s\Z'));
        $usernameToken->appendChild($timestamp);
    }
    
    /**
     * Add request parameters to body
     */
    private function addRequestParameters(\DOMDocument $xml, \DOMElement $body, array $parameters): void
    {
        if (empty($parameters)) {
            return;
        }
        
        $requestParams = $xml->createElement('tcu:RequestParameters');
        $body->appendChild($requestParams);
        
        $this->addParametersRecursive($xml, $requestParams, $parameters);
    }
    
    /**
     * Add parameters recursively to handle nested arrays
     */
    private function addParametersRecursive(\DOMDocument $xml, \DOMElement $parent, array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $key = $this->sanitizeElementName($key);
            
            if (is_array($value)) {
                $element = $xml->createElement($key);
                $parent->appendChild($element);
                $this->addParametersRecursive($xml, $element, $value);
            } else {
                $element = $xml->createElement($key, htmlspecialchars((string)$value));
                $parent->appendChild($element);
            }
        }
    }
    
    /**
     * Sanitize element name for XML compatibility
     */
    private function sanitizeElementName(string $name): string
    {
        // Remove invalid characters and ensure it starts with letter or underscore
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        
        if (empty($name) || is_numeric($name[0])) {
            $name = 'param_' . $name;
        }
        
        return $name;
    }
    
    /**
     * Build simple request with minimal parameters
     */
    public function buildSimpleRequest(UsernameToken $token, string $operation, array $data = []): string
    {
        $parameters = [
            'Operation' => $operation,
            'Data' => $data
        ];
        
        return $this->buildRequest($token, $parameters);
    }
    
    /**
     * Build applicant search request
     */
    public function buildApplicantSearchRequest(UsernameToken $token, array $searchCriteria): string
    {
        $parameters = [
            'Operation' => 'SearchApplicant',
            'SearchCriteria' => $searchCriteria
        ];
        
        return $this->buildRequest($token, $parameters);
    }
    
    /**
     * Build applicant registration request
     */
    public function buildApplicantRegistrationRequest(UsernameToken $token, array $applicantData): string
    {
        $this->validateApplicantData($applicantData);
        
        $parameters = [
            'Operation' => 'RegisterApplicant',
            'ApplicantData' => $applicantData
        ];
        
        return $this->buildRequest($token, $parameters);
    }
    
    /**
     * Build status update request
     */
    public function buildStatusUpdateRequest(UsernameToken $token, string $f4indexno, string $status): string
    {
        $parameters = [
            'Operation' => 'UpdateStatus',
            'f4indexno' => $f4indexno,
            'Status' => $status
        ];
        
        return $this->buildRequest($token, $parameters);
    }
    
    /**
     * Validate applicant data before building request
     */
    private function validateApplicantData(array $applicantData): void
    {
        $requiredFields = ['firstname', 'middlename', 'surname'];
        
        foreach ($requiredFields as $field) {
            if (empty($applicantData[$field])) {
                throw new TCUAPIException("Required field '{$field}' is missing from applicant data");
            }
        }
        
        // Validate f4indexno format if provided
        if (isset($applicantData['f4indexno']) && !preg_match('/^[A-Z0-9]{10,15}$/', $applicantData['f4indexno'])) {
            throw new TCUAPIException('Invalid f4indexno format');
        }
    }
    
    /**
     * Validate XML structure
     */
    public function validateRequest(string $xml): bool
    {
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get XML template for debugging
     */
    public function getXmlTemplate(): string
    {
        return self::XML_DECLARATION . "\n" .
               '<soap:Envelope xmlns:soap="' . self::SOAP_NAMESPACE . '" xmlns:tcu="' . self::TCU_NAMESPACE . '">' . "\n" .
               '  <soap:Header>' . "\n" .
               '    <Security>' . "\n" .
               '      <UsernameToken>' . "\n" .
               '        <Username>USERNAME_HERE</Username>' . "\n" .
               '        <SessionToken>SESSION_TOKEN_HERE</SessionToken>' . "\n" .
               '        <Timestamp>TIMESTAMP_HERE</Timestamp>' . "\n" .
               '      </UsernameToken>' . "\n" .
               '    </Security>' . "\n" .
               '  </soap:Header>' . "\n" .
               '  <soap:Body>' . "\n" .
               '    <tcu:RequestParameters>' . "\n" .
               '      <!-- Request parameters go here -->' . "\n" .
               '    </tcu:RequestParameters>' . "\n" .
               '  </soap:Body>' . "\n" .
               '</soap:Envelope>';
    }
}