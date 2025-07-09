<?php

/**
 * TCU API Client - Transfer Resource
 * 
 * Resource class for handling transfer-related API operations in the TCU system.
 * Provides methods for internal transfers, inter-institutional transfers,
 * and transfer status management operations.
 * 
 * @package    MBLogik\TCUAPIClient\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Resource class for transfer operations and status management
 */

namespace MBLogik\TCUAPIClient\Resources;

use MBLogik\TCUAPIClient\Utils\ValidationHelper;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;

class TransferResource extends BaseResource
{
    /**
     * Submit internal transfers (3.14)
     * 
     * Usage Context: Undergraduate admission
     * Description: An applicant who has been admitted but wishes to transfer 
     * to another programme within the same institution
     * 
     * @param array $transferData Array of transfer data
     * @return array
     */
    public function submitInternalTransfers(array $transferData): array
    {
        // Validate transfer data
        $errors = $this->validateTransferData($transferData, 'internal');
        if (!empty($errors)) {
            throw new ValidationException("Transfer validation failed", $errors);
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'submitInternalTransfers',
            'transfer_type' => 'internal',
            'transfers' => $transferData
        ];
        
        return $this->post('/admission/submitInternalTransfers', $requestData);
    }
    
    /**
     * Submit inter-institutional transfers (3.15)
     * 
     * Usage Context: Undergraduate admission
     * Description: An applicant who has been admitted and wishes to transfer 
     * to another institution
     * 
     * @param array $transferData Array of transfer data
     * @return array
     */
    public function submitInterInstitutionalTransfers(array $transferData): array
    {
        // Validate transfer data
        $errors = $this->validateTransferData($transferData, 'inter_institutional');
        if (!empty($errors)) {
            throw new ValidationException("Transfer validation failed", $errors);
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'submitInterInstitutionalTransfers',
            'transfer_type' => 'inter_institutional',
            'transfers' => $transferData
        ];
        
        return $this->post('/admission/submitInterInstitutionalTransfers', $requestData);
    }
    
    /**
     * Get internal transfer status
     * 
     * @param string $programmeCode Programme code to filter by
     * @return array
     */
    public function getInternalTransferStatus(string $programmeCode): array
    {
        // Validate programme code
        if (!ValidationHelper::validateProgrammeCode($programmeCode)) {
            throw new ValidationException("Invalid programme code format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getInternalTransferStatus',
            'programme_code' => $programmeCode
        ];
        
        return $this->post('/transfers/getInternalStatus', $requestData);
    }
    
    /**
     * Get inter-institutional transfer status
     * 
     * @param string $programmeCode Programme code to filter by
     * @return array
     */
    public function getInterInstitutionalTransferStatus(string $programmeCode): array
    {
        // Validate programme code
        if (!ValidationHelper::validateProgrammeCode($programmeCode)) {
            throw new ValidationException("Invalid programme code format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getInterInstitutionalTransferStatus',
            'programme_code' => $programmeCode
        ];
        
        return $this->post('/transfers/getInterInstitutionalStatus', $requestData);
    }
    
    /**
     * Submit single internal transfer
     * 
     * @param string $f4indexno Form four index number
     * @param string $fromProgrammeCode Source programme code
     * @param string $toProgrammeCode Target programme code
     * @param string $reason Transfer reason
     * @param array $additionalData Additional transfer data
     * @return array
     */
    public function submitSingleInternalTransfer(
        string $f4indexno,
        string $fromProgrammeCode,
        string $toProgrammeCode,
        string $reason,
        array $additionalData = []
    ): array {
        // Validate inputs
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            throw new ValidationException("Invalid F4 Index Number format");
        }
        
        if (!ValidationHelper::validateProgrammeCode($fromProgrammeCode)) {
            throw new ValidationException("Invalid source programme code format");
        }
        
        if (!ValidationHelper::validateProgrammeCode($toProgrammeCode)) {
            throw new ValidationException("Invalid target programme code format");
        }
        
        if (empty($reason)) {
            throw new ValidationException("Transfer reason is required");
        }
        
        if ($fromProgrammeCode === $toProgrammeCode) {
            throw new ValidationException("Source and target programmes cannot be the same");
        }
        
        // Prepare transfer data
        $transferData = [[
            'f4indexno' => $f4indexno,
            'from_programme_code' => $fromProgrammeCode,
            'to_programme_code' => $toProgrammeCode,
            'reason' => $reason,
            'additional_data' => $additionalData
        ]];
        
        return $this->submitInternalTransfers($transferData);
    }
    
    /**
     * Submit single inter-institutional transfer
     * 
     * @param string $f4indexno Form four index number
     * @param string $fromInstitutionCode Source institution code
     * @param string $toInstitutionCode Target institution code
     * @param string $fromProgrammeCode Source programme code
     * @param string $toProgrammeCode Target programme code
     * @param string $reason Transfer reason
     * @param array $additionalData Additional transfer data
     * @return array
     */
    public function submitSingleInterInstitutionalTransfer(
        string $f4indexno,
        string $fromInstitutionCode,
        string $toInstitutionCode,
        string $fromProgrammeCode,
        string $toProgrammeCode,
        string $reason,
        array $additionalData = []
    ): array {
        // Validate inputs
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            throw new ValidationException("Invalid F4 Index Number format");
        }
        
        if (!ValidationHelper::validateInstitutionCode($fromInstitutionCode)) {
            throw new ValidationException("Invalid source institution code format");
        }
        
        if (!ValidationHelper::validateInstitutionCode($toInstitutionCode)) {
            throw new ValidationException("Invalid target institution code format");
        }
        
        if (!ValidationHelper::validateProgrammeCode($fromProgrammeCode)) {
            throw new ValidationException("Invalid source programme code format");
        }
        
        if (!ValidationHelper::validateProgrammeCode($toProgrammeCode)) {
            throw new ValidationException("Invalid target programme code format");
        }
        
        if (empty($reason)) {
            throw new ValidationException("Transfer reason is required");
        }
        
        if ($fromInstitutionCode === $toInstitutionCode) {
            throw new ValidationException("Source and target institutions cannot be the same");
        }
        
        // Prepare transfer data
        $transferData = [[
            'f4indexno' => $f4indexno,
            'from_institution_code' => $fromInstitutionCode,
            'to_institution_code' => $toInstitutionCode,
            'from_programme_code' => $fromProgrammeCode,
            'to_programme_code' => $toProgrammeCode,
            'reason' => $reason,
            'additional_data' => $additionalData
        ]];
        
        return $this->submitInterInstitutionalTransfers($transferData);
    }
    
    /**
     * Cancel transfer request
     * 
     * @param string $transferId Transfer ID to cancel
     * @param string $reason Cancellation reason
     * @return array
     */
    public function cancelTransfer(string $transferId, string $reason): array
    {
        // Validate inputs
        if (empty($transferId)) {
            throw new ValidationException("Transfer ID is required");
        }
        
        if (empty($reason)) {
            throw new ValidationException("Cancellation reason is required");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'cancelTransfer',
            'transfer_id' => $transferId,
            'reason' => $reason
        ];
        
        return $this->post('/transfers/cancel', $requestData);
    }
    
    /**
     * Get transfer history for applicant
     * 
     * @param string $f4indexno Form four index number
     * @return array
     */
    public function getTransferHistory(string $f4indexno): array
    {
        // Validate F4 index number
        if (!ValidationHelper::validateF4IndexNo($f4indexno)) {
            throw new ValidationException("Invalid F4 Index Number format");
        }
        
        // Prepare request data
        $requestData = [
            'operation' => 'getTransferHistory',
            'f4indexno' => $f4indexno
        ];
        
        return $this->post('/transfers/history', $requestData);
    }
    
    /**
     * Validate transfer data
     * 
     * @param array $transferData Transfer data to validate
     * @param string $transferType Type of transfer (internal or inter_institutional)
     * @return array Validation errors
     */
    private function validateTransferData(array $transferData, string $transferType): array
    {
        $errors = [];
        
        if (empty($transferData)) {
            $errors[] = 'Transfer data cannot be empty';
            return $errors;
        }
        
        foreach ($transferData as $index => $transfer) {
            $transferIndex = $index + 1;
            
            // Validate F4 index number
            if (empty($transfer['f4indexno'])) {
                $errors[] = "Transfer $transferIndex: F4 index number is required";
            } elseif (!ValidationHelper::validateF4IndexNo($transfer['f4indexno'])) {
                $errors[] = "Transfer $transferIndex: Invalid F4 index number format";
            }
            
            // Validate programme codes
            if (empty($transfer['from_programme_code'])) {
                $errors[] = "Transfer $transferIndex: Source programme code is required";
            } elseif (!ValidationHelper::validateProgrammeCode($transfer['from_programme_code'])) {
                $errors[] = "Transfer $transferIndex: Invalid source programme code format";
            }
            
            if (empty($transfer['to_programme_code'])) {
                $errors[] = "Transfer $transferIndex: Target programme code is required";
            } elseif (!ValidationHelper::validateProgrammeCode($transfer['to_programme_code'])) {
                $errors[] = "Transfer $transferIndex: Invalid target programme code format";
            }
            
            // For inter-institutional transfers, validate institution codes
            if ($transferType === 'inter_institutional') {
                if (empty($transfer['from_institution_code'])) {
                    $errors[] = "Transfer $transferIndex: Source institution code is required";
                } elseif (!ValidationHelper::validateInstitutionCode($transfer['from_institution_code'])) {
                    $errors[] = "Transfer $transferIndex: Invalid source institution code format";
                }
                
                if (empty($transfer['to_institution_code'])) {
                    $errors[] = "Transfer $transferIndex: Target institution code is required";
                } elseif (!ValidationHelper::validateInstitutionCode($transfer['to_institution_code'])) {
                    $errors[] = "Transfer $transferIndex: Invalid target institution code format";
                }
                
                // Check that institutions are different
                if (!empty($transfer['from_institution_code']) && 
                    !empty($transfer['to_institution_code']) && 
                    $transfer['from_institution_code'] === $transfer['to_institution_code']) {
                    $errors[] = "Transfer $transferIndex: Source and target institutions cannot be the same";
                }
            }
            
            // Validate reason
            if (empty($transfer['reason'])) {
                $errors[] = "Transfer $transferIndex: Transfer reason is required";
            }
            
            // Check that programmes are different
            if (!empty($transfer['from_programme_code']) && 
                !empty($transfer['to_programme_code']) && 
                $transfer['from_programme_code'] === $transfer['to_programme_code']) {
                $errors[] = "Transfer $transferIndex: Source and target programmes cannot be the same";
            }
        }
        
        return $errors;
    }
}