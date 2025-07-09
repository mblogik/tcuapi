<?php

/**
 * TCU API Client - XML Validator
 * 
 * This file contains the XmlValidator class for validating XML requests
 * and responses according to TCU API specifications. It provides validation
 * for XML structure, schema compliance, and data integrity.
 * 
 * @package    MBLogik\TCUAPIClient\Xml
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    XML validation for TCU API requests and responses with schema
 *             validation and data integrity checks.
 */

namespace MBLogik\TCUAPIClient\Xml;

use MBLogik\TCUAPIClient\Exceptions\ValidationException;

class XmlValidator
{
    private array $validationErrors = [];
    
    /**
     * Validate XML structure
     */
    public function validateXmlStructure(string $xml): bool
    {
        $this->clearErrors();
        
        try {
            // Basic XML parsing validation
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            // Check for SOAP envelope structure
            if (!$this->validateSoapEnvelope($doc)) {
                $this->addError('Invalid SOAP envelope structure');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->addError('Invalid XML format: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate SOAP envelope structure
     */
    private function validateSoapEnvelope(\DOMDocument $doc): bool
    {
        $envelope = $doc->getElementsByTagName('Envelope');
        
        if ($envelope->length === 0) {
            $this->addError('Missing SOAP Envelope element');
            return false;
        }
        
        $header = $doc->getElementsByTagName('Header');
        if ($header->length === 0) {
            $this->addError('Missing SOAP Header element');
            return false;
        }
        
        $body = $doc->getElementsByTagName('Body');
        if ($body->length === 0) {
            $this->addError('Missing SOAP Body element');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate authentication structure
     */
    public function validateAuthenticationStructure(string $xml): bool
    {
        $this->clearErrors();
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            // Check for Security element
            $security = $doc->getElementsByTagName('Security');
            if ($security->length === 0) {
                $this->addError('Missing Security element in header');
                return false;
            }
            
            // Check for UsernameToken
            $usernameToken = $doc->getElementsByTagName('UsernameToken');
            if ($usernameToken->length === 0) {
                $this->addError('Missing UsernameToken element');
                return false;
            }
            
            // Check for Username
            $username = $doc->getElementsByTagName('Username');
            if ($username->length === 0) {
                $this->addError('Missing Username element');
                return false;
            }
            
            // Check for SessionToken
            $sessionToken = $doc->getElementsByTagName('SessionToken');
            if ($sessionToken->length === 0) {
                $this->addError('Missing SessionToken element');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->addError('Authentication validation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate request parameters
     */
    public function validateRequestParameters(string $xml): bool
    {
        $this->clearErrors();
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            // Check for RequestParameters element
            $requestParams = $doc->getElementsByTagName('RequestParameters');
            if ($requestParams->length === 0) {
                $this->addError('Missing RequestParameters element');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->addError('Request parameter validation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate response structure
     */
    public function validateResponseStructure(string $xml): bool
    {
        $this->clearErrors();
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            // Check for ResponseParameters or similar response structure
            $responseParams = $doc->getElementsByTagName('ResponseParameters');
            if ($responseParams->length === 0) {
                // Try alternative response structure
                $statusCode = $doc->getElementsByTagName('StatusCode');
                if ($statusCode->length === 0) {
                    $this->addError('Missing response structure elements');
                    return false;
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->addError('Response validation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate applicant data structure
     */
    public function validateApplicantData(array $data): bool
    {
        $this->clearErrors();
        
        $requiredFields = ['firstname', 'middlename', 'surname'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->addError("Required field '{$field}' is missing");
            }
        }
        
        // Validate f4indexno format if provided
        if (isset($data['f4indexno']) && !empty($data['f4indexno'])) {
            if (!preg_match('/^[A-Z0-9]{10,15}$/', $data['f4indexno'])) {
                $this->addError('Invalid f4indexno format');
            }
        }
        
        // Validate gender if provided
        if (isset($data['gender']) && !empty($data['gender'])) {
            if (!in_array($data['gender'], ['M', 'F'])) {
                $this->addError('Gender must be M or F');
            }
        }
        
        // Validate email format if provided
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->addError('Invalid email format');
            }
        }
        
        return empty($this->validationErrors);
    }
    
    /**
     * Validate programme data structure
     */
    public function validateProgrammeData(array $data): bool
    {
        $this->clearErrors();
        
        $requiredFields = ['programme_code', 'priority'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->addError("Required field '{$field}' is missing");
            }
        }
        
        // Validate priority is numeric
        if (isset($data['priority']) && !is_numeric($data['priority'])) {
            $this->addError('Priority must be numeric');
        }
        
        return empty($this->validationErrors);
    }
    
    /**
     * Validate XML against XSD schema (if available)
     */
    public function validateWithSchema(string $xml, string $xsdPath): bool
    {
        $this->clearErrors();
        
        if (!file_exists($xsdPath)) {
            $this->addError('XSD schema file not found');
            return false;
        }
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            if (!$doc->schemaValidate($xsdPath)) {
                $this->addError('XML does not validate against schema');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->addError('Schema validation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->validationErrors;
    }
    
    /**
     * Get last validation error
     */
    public function getLastError(): ?string
    {
        return end($this->validationErrors) ?: null;
    }
    
    /**
     * Check if there are validation errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->validationErrors);
    }
    
    /**
     * Clear validation errors
     */
    public function clearErrors(): void
    {
        $this->validationErrors = [];
    }
    
    /**
     * Add validation error
     */
    private function addError(string $error): void
    {
        $this->validationErrors[] = $error;
    }
    
    /**
     * Get validation errors as string
     */
    public function getErrorsAsString(): string
    {
        return implode('; ', $this->validationErrors);
    }
    
    /**
     * Throw validation exception if errors exist
     */
    public function throwIfErrors(): void
    {
        if ($this->hasErrors()) {
            throw new ValidationException('Validation failed', $this->getErrors());
        }
    }
}