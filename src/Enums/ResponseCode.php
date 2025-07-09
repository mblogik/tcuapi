<?php

/**
 * TCU API Client - Response Code Enumeration
 * 
 * Enumeration class for TCU API response codes and their meanings.
 * Based on TCU API Specification Documentation v4.5, Section 5: Message codes.
 * Provides constants, helper methods, and code classification for API responses.
 * 
 * @package    MBLogik\TCUAPIClient\Enums
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Enumeration of TCU API response codes with classification methods
 */

namespace MBLogik\TCUAPIClient\Enums;

/**
 * TCU API Response Codes
 * 
 * Based on TCU API Specification Documentation v4.5
 * Section 5: Message codes and their descriptions
 */
class ResponseCode
{
    // Success Codes
    public const SUCCESS = 200;
    public const PRIOR_ADMISSION = 201;
    public const CLEAR = 202;
    public const ALREADY_ADMITTED = 203;
    
    // Error Codes
    public const SESSION_TOKEN_DOES_NOT_EXIST = 204;
    public const MALFORMED_XML_REQUEST = 205;
    public const EMPTY_FORM_FOUR_INDEX_NUMBER = 206;
    public const OPERATION_FAIL = 207;
    public const DUPLICATE_RECORD = 208;
    public const RE_SUBMITTED_SUCCESSFUL = 209;
    public const NOT_FOUND = 210;
    public const MANDATORY_PARAMETERS = 211;
    public const CONFIRMED_SUCCESSFUL = 212;
    public const CONFIRM_TO_OTHER_HLI = 213;
    public const CONFIRM_TO_YOUR_HLI = 214;
    public const NO_MULTIPLE_ADMISSION = 215;
    public const PROGRAMME_CAPACITY_IS_FULL = 216;
    public const INVALID_CONFIRMATION_CODE = 217;
    public const UNCONFIRMED_SUCCESSFULLY = 218;
    public const OPERATION_FAILED = 219;
    public const NOT_CONFIRMED = 220;
    public const FAILED_TO_UN_CONFIRM = 221;
    public const CONFIRMATION_CODE_SENT_TO_EMAIL = 222;
    public const CONFIRMATION_CODE_SENT_TO_EMAIL_AND_SMS = 223;
    public const NO_ADMISSION_FOUND = 224;
    public const MULTIPLE_ADMISSION = 225;
    public const SINGLE_ADMISSION = 226;
    public const OPERATION_NOT_ALLOWED = 227;
    public const NOT_CANCELLED_ADMISSION_HERE = 228;
    public const NOT_CANCELLED_ADMISSION_ANYWHERE = 229;
    public const ADMISSION_RESTORED = 230;
    public const APPLICANT_CLEARED = 231;
    public const APPLICANT_NOT_CLEARED = 232;
    public const CONFIRMED_ADMISSION_IN_THIS_PROGRAMME = 233;
    public const CONFIRMED_ADMISSION_TO_OTHER_INSTITUTION = 234;
    
    /**
     * Get response code messages
     */
    public static function getMessages(): array
    {
        return [
            self::SUCCESS => 'Operation was performed successfully.',
            self::PRIOR_ADMISSION => 'Applicant record was found in prior admission list.',
            self::CLEAR => 'Applicant has no prior admission.',
            self::ALREADY_ADMITTED => 'Applicant is already admitted in current admission cycle.',
            self::SESSION_TOKEN_DOES_NOT_EXIST => 'The given session token does not exist in system, please contact system administrator.',
            self::MALFORMED_XML_REQUEST => 'Invalid xml request.',
            self::EMPTY_FORM_FOUR_INDEX_NUMBER => 'Form four index number cannot be null.',
            self::OPERATION_FAIL => 'Data was not successfully submitted to TCU.',
            self::DUPLICATE_RECORD => 'The applicant has already been submitted previously.',
            self::RE_SUBMITTED_SUCCESSFUL => 'The applicant has already been re-submitted previously.',
            self::NOT_FOUND => 'No record found',
            self::MANDATORY_PARAMETERS => 'Empty mandatory parameters',
            self::CONFIRMED_SUCCESSFUL => 'Applicant successfully confirmed',
            self::CONFIRM_TO_OTHER_HLI => 'Applicant has already confirmed to other institution',
            self::CONFIRM_TO_YOUR_HLI => 'Applicant has already confirmed to this institution',
            self::NO_MULTIPLE_ADMISSION => 'The applicant has no multiple admission',
            self::PROGRAMME_CAPACITY_IS_FULL => 'The programme capacity is full. No more confirmations are allowed',
            self::INVALID_CONFIRMATION_CODE => 'Invalid confirmation code',
            self::UNCONFIRMED_SUCCESSFULLY => 'Un-confirmed successfully',
            self::OPERATION_FAILED => 'Failed to un-confirm the admission',
            self::NOT_CONFIRMED => 'The applicant has not confirmed admission to any institution',
            self::FAILED_TO_UN_CONFIRM => 'Unable to un-confirm since the applicant has not confirmed to this institution',
            self::CONFIRMATION_CODE_SENT_TO_EMAIL => 'Confirmation code has been sent to your email address.',
            self::CONFIRMATION_CODE_SENT_TO_EMAIL_AND_SMS => 'Confirmation code has been sent to your email address and mobile number.',
            self::NO_ADMISSION_FOUND => 'You have no admission to this institution',
            self::MULTIPLE_ADMISSION => 'The applicant has multiple admissions',
            self::SINGLE_ADMISSION => 'The applicant has single admission',
            self::OPERATION_NOT_ALLOWED => 'Operation not allowed at the moment',
            self::NOT_CANCELLED_ADMISSION_HERE => 'Applicant have not cancelled admission in this programme',
            self::NOT_CANCELLED_ADMISSION_ANYWHERE => 'Applicant have not cancelled admission in any institution',
            self::ADMISSION_RESTORED => 'Applicant admission restored successfully',
            self::APPLICANT_CLEARED => 'Applicant cleared by the Commission',
            self::APPLICANT_NOT_CLEARED => 'Applicant NOT cleared by the Commission',
            self::CONFIRMED_ADMISSION_IN_THIS_PROGRAMME => 'Applicant confirmed in this programme',
            self::CONFIRMED_ADMISSION_TO_OTHER_INSTITUTION => 'Applicant Confirmed to other HLI',
        ];
    }
    
    /**
     * Get message for a specific code
     */
    public static function getMessage(int $code): string
    {
        $messages = self::getMessages();
        return $messages[$code] ?? 'Unknown response code';
    }
    
    /**
     * Check if code indicates success
     */
    public static function isSuccess(int $code): bool
    {
        return in_array($code, [
            self::SUCCESS,
            self::PRIOR_ADMISSION,
            self::CLEAR,
            self::RE_SUBMITTED_SUCCESSFUL,
            self::CONFIRMED_SUCCESSFUL,
            self::UNCONFIRMED_SUCCESSFULLY,
            self::CONFIRMATION_CODE_SENT_TO_EMAIL,
            self::CONFIRMATION_CODE_SENT_TO_EMAIL_AND_SMS,
            self::ADMISSION_RESTORED,
            self::APPLICANT_CLEARED,
            self::CONFIRMED_ADMISSION_IN_THIS_PROGRAMME,
        ]);
    }
    
    /**
     * Check if code indicates error
     */
    public static function isError(int $code): bool
    {
        return !self::isSuccess($code);
    }
    
    /**
     * Check if code indicates duplicate/already exists
     */
    public static function isDuplicate(int $code): bool
    {
        return in_array($code, [
            self::ALREADY_ADMITTED,
            self::DUPLICATE_RECORD,
            self::CONFIRM_TO_OTHER_HLI,
            self::CONFIRM_TO_YOUR_HLI,
        ]);
    }
    
    /**
     * Check if code indicates not found
     */
    public static function isNotFound(int $code): bool
    {
        return in_array($code, [
            self::NOT_FOUND,
            self::NO_ADMISSION_FOUND,
            self::NOT_CANCELLED_ADMISSION_HERE,
            self::NOT_CANCELLED_ADMISSION_ANYWHERE,
        ]);
    }
    
    /**
     * Check if code indicates validation error
     */
    public static function isValidationError(int $code): bool
    {
        return in_array($code, [
            self::MALFORMED_XML_REQUEST,
            self::EMPTY_FORM_FOUR_INDEX_NUMBER,
            self::MANDATORY_PARAMETERS,
            self::INVALID_CONFIRMATION_CODE,
        ]);
    }
    
    /**
     * Check if code indicates admission status
     */
    public static function isAdmissionStatus(int $code): bool
    {
        return in_array($code, [
            self::MULTIPLE_ADMISSION,
            self::SINGLE_ADMISSION,
            self::APPLICANT_CLEARED,
            self::APPLICANT_NOT_CLEARED,
        ]);
    }
    
    /**
     * Check if code indicates capacity/permission issue
     */
    public static function isCapacityIssue(int $code): bool
    {
        return in_array($code, [
            self::PROGRAMME_CAPACITY_IS_FULL,
            self::OPERATION_NOT_ALLOWED,
        ]);
    }
    
    /**
     * Get all success codes
     */
    public static function getSuccessCodes(): array
    {
        return [
            self::SUCCESS,
            self::PRIOR_ADMISSION,
            self::CLEAR,
            self::RE_SUBMITTED_SUCCESSFUL,
            self::CONFIRMED_SUCCESSFUL,
            self::UNCONFIRMED_SUCCESSFULLY,
            self::CONFIRMATION_CODE_SENT_TO_EMAIL,
            self::CONFIRMATION_CODE_SENT_TO_EMAIL_AND_SMS,
            self::ADMISSION_RESTORED,
            self::APPLICANT_CLEARED,
            self::CONFIRMED_ADMISSION_IN_THIS_PROGRAMME,
        ];
    }
    
    /**
     * Get all error codes
     */
    public static function getErrorCodes(): array
    {
        return [
            self::SESSION_TOKEN_DOES_NOT_EXIST,
            self::MALFORMED_XML_REQUEST,
            self::EMPTY_FORM_FOUR_INDEX_NUMBER,
            self::OPERATION_FAIL,
            self::DUPLICATE_RECORD,
            self::NOT_FOUND,
            self::MANDATORY_PARAMETERS,
            self::CONFIRM_TO_OTHER_HLI,
            self::CONFIRM_TO_YOUR_HLI,
            self::NO_MULTIPLE_ADMISSION,
            self::PROGRAMME_CAPACITY_IS_FULL,
            self::INVALID_CONFIRMATION_CODE,
            self::OPERATION_FAILED,
            self::NOT_CONFIRMED,
            self::FAILED_TO_UN_CONFIRM,
            self::NO_ADMISSION_FOUND,
            self::OPERATION_NOT_ALLOWED,
            self::NOT_CANCELLED_ADMISSION_HERE,
            self::NOT_CANCELLED_ADMISSION_ANYWHERE,
            self::APPLICANT_NOT_CLEARED,
            self::CONFIRMED_ADMISSION_TO_OTHER_INSTITUTION,
        ];
    }
}