<?php

/**
 * TCU API Client - XML Response Parser
 * 
 * This file contains the ResponseParser class for parsing XML responses
 * from the TCU API. It handles XML structure validation, data extraction,
 * and object mapping according to TCU API response specifications.
 * 
 * @package    MBLogik\TCUAPIClient\Xml
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    XML response parsing for TCU API with validation, data extraction,
 *             and structured object mapping capabilities.
 */

namespace MBLogik\TCUAPIClient\Xml;

use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use MBLogik\TCUAPIClient\Models\Response\TcuApiResponse;

class ResponseParser
{
    /**
     * Parse XML response to structured array
     */
    public function parseResponse(string $xmlResponse): array
    {
        try {
            if (empty($xmlResponse)) {
                throw new TCUAPIException('Empty XML response received');
            }
            
            $xml = $this->loadXml($xmlResponse);
            $responseData = $this->extractResponseData($xml);
            
            return $this->formatResponseData($responseData);
            
        } catch (\Exception $e) {
            throw new TCUAPIException('Failed to parse XML response: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Parse response to TcuApiResponse object
     */
    public function parseToObject(string $xmlResponse): TcuApiResponse
    {
        return TcuApiResponse::fromXml($xmlResponse);
    }
    
    /**
     * Load and validate XML
     */
    private function loadXml(string $xmlResponse): \SimpleXMLElement
    {
        // Disable libxml errors and use internal error handling
        libxml_use_internal_errors(true);
        
        $xml = simplexml_load_string($xmlResponse);
        
        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMessage = 'Invalid XML format';
            
            if (!empty($errors)) {
                $errorMessage .= ': ' . $errors[0]->message;
            }
            
            libxml_clear_errors();
            throw new TCUAPIException($errorMessage);
        }
        
        return $xml;
    }
    
    /**
     * Extract response data from XML
     */
    private function extractResponseData(\SimpleXMLElement $xml): array
    {
        $data = [];
        
        // Check for SOAP envelope structure
        if ($xml->getName() === 'Envelope') {
            $body = $xml->Body ?? $xml->children()->Body;
            if ($body) {
                $responseParams = $body->children();
                if ($responseParams->count() > 0) {
                    $xml = $responseParams[0];
                }
            }
        }
        
        // Extract all elements
        foreach ($xml->children() as $child) {
            $name = $child->getName();
            $value = $this->extractElementValue($child);
            $data[$name] = $value;
        }
        
        // Extract attributes if any
        foreach ($xml->attributes() as $attrName => $attrValue) {
            $data['@' . $attrName] = (string)$attrValue;
        }
        
        return $data;
    }
    
    /**
     * Extract value from XML element (handles nested structures)
     */
    private function extractElementValue(\SimpleXMLElement $element): mixed
    {
        // If element has children, extract them recursively
        if ($element->children()->count() > 0) {
            $childData = [];
            
            foreach ($element->children() as $child) {
                $name = $child->getName();
                $value = $this->extractElementValue($child);
                
                // Handle multiple elements with same name
                if (isset($childData[$name])) {
                    if (!is_array($childData[$name]) || !isset($childData[$name][0])) {
                        $childData[$name] = [$childData[$name]];
                    }
                    $childData[$name][] = $value;
                } else {
                    $childData[$name] = $value;
                }
            }
            
            return $childData;
        }
        
        // Return simple string value
        return (string)$element;
    }
    
    /**
     * Format response data with standard fields
     */
    private function formatResponseData(array $data): array
    {
        $formatted = [
            'f4indexno' => $data['f4indexno'] ?? $data['F4IndexNo'] ?? '',
            'status_code' => (int)($data['StatusCode'] ?? $data['status_code'] ?? 0),
            'status_description' => $data['StatusDescription'] ?? $data['status_description'] ?? '',
            'raw_data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Add additional data fields
        $formatted['data'] = $this->extractAdditionalData($data);
        
        return $formatted;
    }
    
    /**
     * Extract additional data fields (excluding standard ones)
     */
    private function extractAdditionalData(array $data): array
    {
        $standardFields = [
            'f4indexno', 'F4IndexNo', 'StatusCode', 'status_code',
            'StatusDescription', 'status_description'
        ];
        
        $additionalData = [];
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $standardFields)) {
                $additionalData[$key] = $value;
            }
        }
        
        return $additionalData;
    }
    
    /**
     * Extract status code from response
     */
    public function extractStatusCode(string $xml): int
    {
        try {
            $response = $this->parseResponse($xml);
            return $response['status_code'] ?? 0;
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    /**
     * Extract status description from response
     */
    public function extractStatusDescription(string $xml): string
    {
        try {
            $response = $this->parseResponse($xml);
            return $response['status_description'] ?? 'Unknown response';
        } catch (\Exception $e) {
            return 'Failed to parse response: ' . $e->getMessage();
        }
    }
    
    /**
     * Parse error response
     */
    public function parseErrorResponse(string $xmlResponse): array
    {
        try {
            $data = $this->parseResponse($xmlResponse);
            
            return [
                'error' => true,
                'error_code' => $data['status_code'],
                'error_message' => $data['status_description'],
                'f4indexno' => $data['f4indexno'],
                'raw_response' => $xmlResponse,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (\Exception $e) {
            return [
                'error' => true,
                'error_code' => -1,
                'error_message' => 'Failed to parse error response: ' . $e->getMessage(),
                'raw_response' => $xmlResponse,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Validate response structure
     */
    public function validateResponseStructure(array $response): bool
    {
        $requiredFields = ['status_code', 'timestamp'];
        
        foreach ($requiredFields as $field) {
            if (!isset($response[$field])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Extract applicant data from response
     */
    public function extractApplicantData(string $xmlResponse): array
    {
        $response = $this->parseResponse($xmlResponse);
        
        if (!isset($response['data'])) {
            return [];
        }
        
        $applicantFields = [
            'firstname', 'middlename', 'surname', 'gender', 'phone',
            'email', 'nationality', 'f4indexno', 'f6indexno',
            'programme', 'institution', 'year'
        ];
        
        $applicantData = [];
        
        foreach ($applicantFields as $field) {
            if (isset($response['data'][$field])) {
                $applicantData[$field] = $response['data'][$field];
            }
        }
        
        return $applicantData;
    }
    
    /**
     * Extract search results from response
     */
    public function extractSearchResults(string $xmlResponse): array
    {
        $response = $this->parseResponse($xmlResponse);
        
        if (!isset($response['data']['Results'])) {
            return [];
        }
        
        $results = $response['data']['Results'];
        
        // Handle single result vs multiple results
        if (!is_array($results) || !isset($results[0])) {
            return [$results];
        }
        
        return $results;
    }
    
    /**
     * Check if response indicates success
     */
    public function isSuccessResponse(string $xmlResponse): bool
    {
        try {
            $response = $this->parseResponse($xmlResponse);
            return isset($response['status_code']) && $response['status_code'] === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Convert XML to pretty formatted string
     */
    public function formatXml(string $xml): string
    {
        try {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = true;
            $doc->loadXML($xml);
            
            return $doc->saveXML();
        } catch (\Exception $e) {
            return $xml;
        }
    }
}