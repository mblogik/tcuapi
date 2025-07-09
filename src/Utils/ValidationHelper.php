<?php

/**
 * TCU API Client - Validation Helper Utilities
 * 
 * This file contains utility functions for data validation, format checking,
 * and input sanitization. It provides helper methods for common validation
 * operations used throughout the TCU API client.
 * 
 * @package    MBLogik\TCUAPIClient\Utils
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Validation utility functions for data integrity and format checking
 */

namespace MBLogik\TCUAPIClient\Utils;

class ValidationHelper
{
    /**
     * Validate Form 4 Index Number format
     * Format: S1001/0012/2018 (School code/candidate number/year)
     */
    public static function validateF4IndexNo(string $f4indexno): bool
    {
        return preg_match('/^[A-Z][0-9]{4}\/[0-9]{4}\/[0-9]{4}$/', $f4indexno) === 1;
    }
    
    /**
     * Validate Form 6 Index Number format
     * Format: S1001/0562/2018 (Similar to F4 format)
     */
    public static function validateF6IndexNo(string $f6indexno): bool
    {
        return preg_match('/^[A-Z][0-9]{4}\/[0-9]{4}\/[0-9]{4}$/', $f6indexno) === 1;
    }
    
    /**
     * Validate AVN (Academic Verification Number) format
     */
    public static function validateAVN(string $avn): bool
    {
        return preg_match('/^AVN[0-9]{6,10}$/', $avn) === 1;
    }
    
    /**
     * Validate gender
     */
    public static function validateGender(string $gender): bool
    {
        return in_array(strtoupper($gender), ['M', 'F']);
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number format (Tanzanian)
     */
    public static function validatePhoneNumber(string $phone): bool
    {
        // Accept formats: +255xxxxxxxxx, 0xxxxxxxxx, or xxxxxxxxx
        return preg_match('/^(\+255|0)?[67][0-9]{8}$/', $phone) === 1;
    }
    
    /**
     * Validate year format
     */
    public static function validateYear(int $year): bool
    {
        $currentYear = (int)date('Y');
        return $year >= 1900 && $year <= ($currentYear + 10);
    }
    
    /**
     * Validate programme code format
     */
    public static function validateProgrammeCode(string $programmeCode): bool
    {
        return preg_match('/^[A-Z0-9]{3,10}$/', $programmeCode) === 1;
    }
    
    /**
     * Validate institution code format
     */
    public static function validateInstitutionCode(string $institutionCode): bool
    {
        return preg_match('/^[A-Z0-9]{3,10}$/', $institutionCode) === 1;
    }
    
    /**
     * Validate priority number
     */
    public static function validatePriority(int $priority): bool
    {
        return $priority >= 1 && $priority <= 10;
    }
    
    /**
     * Validate nationality
     */
    public static function validateNationality(string $nationality): bool
    {
        $validNationalities = [
            'Tanzanian', 'Kenyan', 'Ugandan', 'Rwandan', 'Burundian',
            'Congolese', 'Sudanese', 'Ethiopian', 'Somalian', 'Other'
        ];
        
        return in_array($nationality, $validNationalities);
    }
    
    /**
     * Validate applicant category
     */
    public static function validateApplicantCategory(string $category): bool
    {
        $validCategories = [
            'Government', 'Private', 'Foreign', 'Special'
        ];
        
        return in_array($category, $validCategories);
    }
    
    /**
     * Validate names (first, middle, surname)
     */
    public static function validateName(string $name): bool
    {
        return preg_match('/^[a-zA-Z\s\'-]{2,50}$/', $name) === 1;
    }
    
    /**
     * Validate confirmation code
     */
    public static function validateConfirmationCode(string $code): bool
    {
        return preg_match('/^[A-Z0-9]{6,12}$/', $code) === 1;
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    public static function validateDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1 && 
               strtotime($date) !== false;
    }
    
    /**
     * Validate boolean value
     */
    public static function validateBoolean($value): bool
    {
        return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);
    }
    
    /**
     * Validate numeric value
     */
    public static function validateNumeric($value): bool
    {
        return is_numeric($value);
    }
    
    /**
     * Validate array is not empty
     */
    public static function validateNonEmptyArray(array $array): bool
    {
        return !empty($array);
    }
    
    /**
     * Validate string is not empty
     */
    public static function validateNonEmptyString(string $string): bool
    {
        return !empty(trim($string));
    }
    
    /**
     * Validate string length
     */
    public static function validateStringLength(string $string, int $minLength, int $maxLength): bool
    {
        $length = strlen($string);
        return $length >= $minLength && $length <= $maxLength;
    }
    
    /**
     * Validate required fields are present
     */
    public static function validateRequiredFields(array $data, array $requiredFields): array
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Required field '{$field}' is missing or empty";
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize input string
     */
    public static function sanitizeString(string $input): string
    {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Sanitize phone number
     */
    public static function sanitizePhoneNumber(string $phone): string
    {
        // Remove non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Convert to standard format
        if (str_starts_with($phone, '0')) {
            $phone = '+255' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+255' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate and sanitize applicant data
     */
    public static function validateApplicantData(array $data): array
    {
        $errors = [];
        
        // Required fields validation
        $requiredFields = ['firstname', 'middlename', 'surname'];
        $errors = array_merge($errors, self::validateRequiredFields($data, $requiredFields));
        
        // Name validation
        foreach (['firstname', 'middlename', 'surname'] as $field) {
            if (isset($data[$field]) && !self::validateName($data[$field])) {
                $errors[] = "Invalid {$field} format";
            }
        }
        
        // F4 Index Number validation
        if (isset($data['f4indexno']) && !empty($data['f4indexno'])) {
            if (!self::validateF4IndexNo($data['f4indexno'])) {
                $errors[] = 'Invalid F4 Index Number format';
            }
        }
        
        // F6 Index Number validation
        if (isset($data['f6indexno']) && !empty($data['f6indexno'])) {
            if (!self::validateF6IndexNo($data['f6indexno'])) {
                $errors[] = 'Invalid F6 Index Number format';
            }
        }
        
        // Gender validation
        if (isset($data['gender']) && !self::validateGender($data['gender'])) {
            $errors[] = 'Invalid gender format';
        }
        
        // Email validation
        if (isset($data['email']) && !empty($data['email'])) {
            if (!self::validateEmail($data['email'])) {
                $errors[] = 'Invalid email format';
            }
        }
        
        // Phone validation
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!self::validatePhoneNumber($data['phone'])) {
                $errors[] = 'Invalid phone number format';
            }
        }
        
        // Year validation
        if (isset($data['year']) && !self::validateYear($data['year'])) {
            $errors[] = 'Invalid year';
        }
        
        // Nationality validation
        if (isset($data['nationality']) && !self::validateNationality($data['nationality'])) {
            $errors[] = 'Invalid nationality';
        }
        
        // Category validation
        if (isset($data['applicant_category']) && !self::validateApplicantCategory($data['applicant_category'])) {
            $errors[] = 'Invalid applicant category';
        }
        
        return $errors;
    }
    
    /**
     * Validate programme data
     */
    public static function validateProgrammeData(array $data): array
    {
        $errors = [];
        
        // Required fields validation
        $requiredFields = ['programme_code', 'priority'];
        $errors = array_merge($errors, self::validateRequiredFields($data, $requiredFields));
        
        // Programme code validation
        if (isset($data['programme_code']) && !self::validateProgrammeCode($data['programme_code'])) {
            $errors[] = 'Invalid programme code format';
        }
        
        // Priority validation
        if (isset($data['priority']) && !self::validatePriority($data['priority'])) {
            $errors[] = 'Invalid priority value';
        }
        
        return $errors;
    }
    
    /**
     * Convert value to boolean
     */
    public static function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        
        return (bool)$value;
    }
    
    /**
     * Convert value to integer
     */
    public static function toInteger($value): int
    {
        return (int)$value;
    }
    
    /**
     * Convert value to float
     */
    public static function toFloat($value): float
    {
        return (float)$value;
    }
}