<?php

/**
 * TCU API Client - XML Helper Utilities
 * 
 * This file contains utility functions for XML processing, manipulation,
 * and formatting. It provides helper methods for common XML operations
 * used throughout the TCU API client.
 * 
 * @package    MBLogik\TCUAPIClient\Utils
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    XML utility functions for formatting, conversion, and manipulation
 */

namespace MBLogik\TCUAPIClient\Utils;

use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;

class XmlHelper
{
    /**
     * Convert array to XML string
     */
    public static function arrayToXml(array $data, string $rootElement = 'root'): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $root = $xml->createElement($rootElement);
        $xml->appendChild($root);
        
        self::addArrayToXml($xml, $root, $data);
        
        return $xml->saveXML();
    }
    
    /**
     * Add array data to XML element recursively
     */
    private static function addArrayToXml(\DOMDocument $xml, \DOMElement $parent, array $data): void
    {
        foreach ($data as $key => $value) {
            $key = self::sanitizeElementName($key);
            
            if (is_array($value)) {
                $element = $xml->createElement($key);
                $parent->appendChild($element);
                self::addArrayToXml($xml, $element, $value);
            } else {
                $element = $xml->createElement($key, htmlspecialchars((string)$value));
                $parent->appendChild($element);
            }
        }
    }
    
    /**
     * Convert XML string to array
     */
    public static function xmlToArray(string $xml): array
    {
        try {
            $xmlObject = simplexml_load_string($xml);
            if ($xmlObject === false) {
                throw new TCUAPIException('Invalid XML format');
            }
            
            return json_decode(json_encode($xmlObject), true);
            
        } catch (\Exception $e) {
            throw new TCUAPIException('Failed to convert XML to array: ' . $e->getMessage());
        }
    }
    
    /**
     * Sanitize element name for XML compatibility
     */
    public static function sanitizeElementName(string $name): string
    {
        // Remove invalid characters and ensure it starts with letter or underscore
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        
        if (empty($name) || is_numeric($name[0])) {
            $name = 'element_' . $name;
        }
        
        return $name;
    }
    
    /**
     * Format XML string with proper indentation
     */
    public static function formatXml(string $xml): string
    {
        try {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = true;
            $doc->loadXML($xml);
            
            return $doc->saveXML();
            
        } catch (\Exception $e) {
            return $xml; // Return original if formatting fails
        }
    }
    
    /**
     * Escape XML special characters
     */
    public static function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1, 'UTF-8');
    }
    
    /**
     * Unescape XML special characters
     */
    public static function unescapeXml(string $text): string
    {
        return htmlspecialchars_decode($text, ENT_XML1);
    }
    
    /**
     * Extract text content from XML element
     */
    public static function getElementText(string $xml, string $elementName): ?string
    {
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            $elements = $doc->getElementsByTagName($elementName);
            if ($elements->length > 0) {
                return $elements->item(0)->textContent;
            }
            
            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Extract attribute value from XML element
     */
    public static function getElementAttribute(string $xml, string $elementName, string $attributeName): ?string
    {
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            $elements = $doc->getElementsByTagName($elementName);
            if ($elements->length > 0) {
                return $elements->item(0)->getAttribute($attributeName);
            }
            
            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Check if XML string is valid
     */
    public static function isValidXml(string $xml): bool
    {
        try {
            $doc = new \DOMDocument();
            return $doc->loadXML($xml) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Remove XML declaration from string
     */
    public static function removeXmlDeclaration(string $xml): string
    {
        return preg_replace('/<\?xml[^>]*\?>\s*/', '', $xml);
    }
    
    /**
     * Add XML declaration to string
     */
    public static function addXmlDeclaration(string $xml): string
    {
        if (!str_starts_with(trim($xml), '<?xml')) {
            return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
        }
        
        return $xml;
    }
    
    /**
     * Minify XML by removing unnecessary whitespace
     */
    public static function minifyXml(string $xml): string
    {
        try {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xml);
            
            return $doc->saveXML();
            
        } catch (\Exception $e) {
            return $xml;
        }
    }
    
    /**
     * Extract namespace from XML
     */
    public static function extractNamespace(string $xml): array
    {
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            $namespaces = [];
            $xpath = new \DOMXPath($doc);
            
            foreach ($xpath->query('//namespace::*') as $namespace) {
                $namespaces[$namespace->prefix] = $namespace->nodeValue;
            }
            
            return $namespaces;
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Convert SimpleXMLElement to array
     */
    public static function simpleXmlToArray(\SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }
    
    /**
     * Create CDATA section
     */
    public static function createCData(string $content): string
    {
        return '<![CDATA[' . $content . ']]>';
    }
    
    /**
     * Extract CDATA content
     */
    public static function extractCData(string $xml): string
    {
        if (preg_match('/<!\[CDATA\[(.*?)\]\]>/s', $xml, $matches)) {
            return $matches[1];
        }
        
        return $xml;
    }
    
    /**
     * Generate XML from template with placeholders
     */
    public static function generateFromTemplate(string $template, array $variables): string
    {
        $xml = $template;
        
        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $xml = str_replace($placeholder, self::escapeXml((string)$value), $xml);
        }
        
        return $xml;
    }
    
    /**
     * Validate XML against DTD
     */
    public static function validateWithDtd(string $xml, string $dtdPath): bool
    {
        if (!file_exists($dtdPath)) {
            return false;
        }
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            return $doc->validate();
            
        } catch (\Exception $e) {
            return false;
        }
    }
}