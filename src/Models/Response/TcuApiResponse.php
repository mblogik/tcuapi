<?php

/**
 * TCU API Client - TCU API Response Model
 * 
 * This file contains the base response model for TCU API responses.
 * It provides structured object representation of XML responses with
 * proper data handling and validation.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Base response model for all TCU API responses with XML to object mapping
 */

namespace MBLogik\TCUAPIClient\Models\Response;

use MBLogik\TCUAPIClient\Enums\ResponseCode;
use MBLogik\TCUAPIClient\Models\BaseModel;

class TcuApiResponse extends BaseModel
{
    protected array $fillable = [
        'f4indexno',
        'status_code',
        'status_description',
        'data',
        'timestamp'
    ];
    
    protected array $casts = [
        'status_code' => 'int',
        'timestamp' => 'string'
    ];
    
    /**
     * Create response from XML data
     */
    public static function fromXml(string $xml): self
    {
        $data = self::parseXmlResponse($xml);
        return new self($data);
    }
    
    /**
     * Parse XML response to array
     */
    private static function parseXmlResponse(string $xml): array
    {
        try {
            $xmlObject = simplexml_load_string($xml);
            if ($xmlObject === false) {
                throw new \InvalidArgumentException('Invalid XML response');
            }
            
            $responseParams = $xmlObject->ResponseParameters ?? $xmlObject;
            
            return [
                'f4indexno' => (string)($responseParams->f4indexno ?? ''),
                'status_code' => (int)($responseParams->StatusCode ?? 0),
                'status_description' => (string)($responseParams->StatusDescription ?? ''),
                'data' => self::extractAdditionalData($responseParams),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Failed to parse XML response: ' . $e->getMessage());
        }
    }
    
    /**
     * Extract additional data from XML response
     */
    private static function extractAdditionalData(\SimpleXMLElement $responseParams): array
    {
        $data = [];
        
        foreach ($responseParams->children() as $child) {
            $name = $child->getName();
            
            // Skip standard fields
            if (in_array($name, ['f4indexno', 'StatusCode', 'StatusDescription'])) {
                continue;
            }
            
            $data[$name] = (string)$child;
        }
        
        return $data;
    }
    
    /**
     * Get form four index number
     */
    public function getFormFourIndexNumber(): ?string
    {
        return $this->get('f4indexno');
    }
    
    /**
     * Get status code
     */
    public function getStatusCode(): int
    {
        return $this->get('status_code', 0);
    }
    
    /**
     * Get status description
     */
    public function getStatusDescription(): string
    {
        return $this->get('status_description', '');
    }
    
    /**
     * Get additional data
     */
    public function getData(): array
    {
        return $this->get('data', []);
    }
    
    /**
     * Get timestamp
     */
    public function getTimestamp(): string
    {
        return $this->get('timestamp', '');
    }
    
    /**
     * Check if response is successful
     */
    public function isSuccess(): bool
    {
        return ResponseCode::isSuccess($this->getStatusCode());
    }
    
    /**
     * Check if response is error
     */
    public function isError(): bool
    {
        return ResponseCode::isError($this->getStatusCode());
    }
    
    /**
     * Get response message based on status code
     */
    public function getResponseMessage(): string
    {
        return ResponseCode::getMessage($this->getStatusCode());
    }
    
    /**
     * Check if response indicates duplicate
     */
    public function isDuplicate(): bool
    {
        return ResponseCode::isDuplicate($this->getStatusCode());
    }
    
    /**
     * Check if response indicates not found
     */
    public function isNotFound(): bool
    {
        return ResponseCode::isNotFound($this->getStatusCode());
    }
    
    /**
     * Check if response indicates validation error
     */
    public function isValidationError(): bool
    {
        return ResponseCode::isValidationError($this->getStatusCode());
    }
    
    /**
     * Get data field by key
     */
    public function getDataField(string $key): mixed
    {
        $data = $this->getData();
        return $data[$key] ?? null;
    }
    
    /**
     * Check if data field exists
     */
    public function hasDataField(string $key): bool
    {
        $data = $this->getData();
        return isset($data[$key]);
    }
    
    /**
     * Get response summary
     */
    public function getSummary(): array
    {
        return [
            'f4indexno' => $this->getFormFourIndexNumber(),
            'status_code' => $this->getStatusCode(),
            'status_description' => $this->getStatusDescription(),
            'response_message' => $this->getResponseMessage(),
            'is_success' => $this->isSuccess(),
            'is_error' => $this->isError(),
            'is_duplicate' => $this->isDuplicate(),
            'is_not_found' => $this->isNotFound(),
            'is_validation_error' => $this->isValidationError(),
            'timestamp' => $this->getTimestamp(),
            'data_fields' => array_keys($this->getData())
        ];
    }
}