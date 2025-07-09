<?php

/**
 * TCU API Client - Dashboard Populate Response Model
 * 
 * Response model for dashboard populate operations from the TCU API.
 * Provides structured access to dashboard population results including
 * status information and operation success details.
 * 
 * @package    MBLogik\TCUAPIClient\Models\Response
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 */

namespace MBLogik\TCUAPIClient\Models\Response;

class DashboardPopulateTcuResponse
{
    private string $programmeCode;
    private int $statusCode;
    private string $statusDescription;
    private bool $isPopulated;
    private int $maleCount;
    private int $femaleCount;
    private int $totalCount;
    private array $rawData;

    /**
     * Constructor
     * 
     * @param array $data Response data from TCU API
     * @param int $males Original male count sent
     * @param int $females Original female count sent
     */
    public function __construct(array $data, int $males = 0, int $females = 0)
    {
        $this->rawData = $data;
        $this->programmeCode = trim($data['ProgrammeCode'] ?? '');
        $this->statusCode = (int)($data['StatusCode'] ?? 0);
        $this->statusDescription = trim($data['StatusDescription'] ?? '');
        
        // Set counts from original request (since response may not include them)
        $this->maleCount = $males;
        $this->femaleCount = $females;
        $this->totalCount = $males + $females;
        
        // Parse population results
        $this->parsePopulationResults($data);
    }

    /**
     * Parse population results from response data
     * 
     * @param array $data
     */
    private function parsePopulationResults(array $data): void
    {
        $description = strtolower($this->statusDescription);
        
        // Determine if successfully populated
        $this->isPopulated = $this->statusCode === 200 && (
            stripos($description, 'successful') !== false ||
            stripos($description, 'operation was performed successfully') !== false ||
            stripos($description, 'updated') !== false
        );
    }

    /**
     * Get programme code
     * 
     * @return string
     */
    public function getProgrammeCode(): string
    {
        return $this->programmeCode;
    }

    /**
     * Get status code
     * 
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get status description
     * 
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->statusDescription;
    }

    /**
     * Check if the request was successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode === 200;
    }

    /**
     * Check if dashboard was successfully populated
     * 
     * @return bool
     */
    public function isPopulated(): bool
    {
        return $this->isPopulated;
    }

    /**
     * Get male count
     * 
     * @return int
     */
    public function getMaleCount(): int
    {
        return $this->maleCount;
    }

    /**
     * Get female count
     * 
     * @return int
     */
    public function getFemaleCount(): int
    {
        return $this->femaleCount;
    }

    /**
     * Get total count
     * 
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Get male percentage
     * 
     * @return float
     */
    public function getMalePercentage(): float
    {
        if ($this->totalCount === 0) {
            return 0.0;
        }
        return round(($this->maleCount / $this->totalCount) * 100, 2);
    }

    /**
     * Get female percentage
     * 
     * @return float
     */
    public function getFemalePercentage(): float
    {
        if ($this->totalCount === 0) {
            return 0.0;
        }
        return round(($this->femaleCount / $this->totalCount) * 100, 2);
    }

    /**
     * Get gender distribution summary
     * 
     * @return array
     */
    public function getGenderDistribution(): array
    {
        return [
            'males' => $this->maleCount,
            'females' => $this->femaleCount,
            'total' => $this->totalCount,
            'malePercentage' => $this->getMalePercentage(),
            'femalePercentage' => $this->getFemalePercentage()
        ];
    }

    /**
     * Get population summary
     * 
     * @return array
     */
    public function getPopulationSummary(): array
    {
        return [
            'programmeCode' => $this->programmeCode,
            'isSuccess' => $this->isSuccess(),
            'isPopulated' => $this->isPopulated(),
            'genderDistribution' => $this->getGenderDistribution(),
            'statusDescription' => $this->statusDescription,
            'statusCode' => $this->statusCode
        ];
    }

    /**
     * Get the operation result description
     * 
     * @return string
     */
    public function getOperationResult(): string
    {
        if (!$this->isSuccess()) {
            return 'Failed: ' . $this->statusDescription;
        }
        
        if ($this->isPopulated()) {
            return sprintf(
                'Successfully populated dashboard for %s (M:%d, F:%d, Total:%d)',
                $this->programmeCode,
                $this->maleCount,
                $this->femaleCount,
                $this->totalCount
            );
        }
        
        return 'Population status unclear: ' . $this->statusDescription;
    }

    /**
     * Check if the operation was completed successfully
     * 
     * @return bool
     */
    public function isOperationComplete(): bool
    {
        return $this->isSuccess() && $this->isPopulated();
    }

    /**
     * Check if there are gender imbalances (>70% of either gender)
     * 
     * @return bool
     */
    public function hasGenderImbalance(): bool
    {
        if ($this->totalCount === 0) {
            return false;
        }
        
        $malePercentage = $this->getMalePercentage();
        $femalePercentage = $this->getFemalePercentage();
        
        return $malePercentage > 70 || $femalePercentage > 70;
    }

    /**
     * Get gender balance status
     * 
     * @return string
     */
    public function getGenderBalanceStatus(): string
    {
        if ($this->totalCount === 0) {
            return 'No data';
        }
        
        $malePercentage = $this->getMalePercentage();
        $femalePercentage = $this->getFemalePercentage();
        
        if (abs($malePercentage - $femalePercentage) <= 10) {
            return 'Balanced';
        } elseif ($malePercentage > $femalePercentage) {
            return 'Male-dominated';
        } else {
            return 'Female-dominated';
        }
    }

    /**
     * Get recommended next steps
     * 
     * @return array
     */
    public function getRecommendedNextSteps(): array
    {
        $steps = [];
        
        if ($this->isOperationComplete()) {
            $steps[] = 'Dashboard successfully updated';
            $steps[] = 'Statistics are now available in TCU dashboard';
            
            if ($this->hasGenderImbalance()) {
                $steps[] = 'Consider reviewing gender balance strategies';
            }
        } else {
            $steps[] = 'Review population status';
            if (!$this->isSuccess()) {
                $steps[] = 'Check programme code validity';
                $steps[] = 'Verify data format and retry';
            }
        }
        
        return $steps;
    }

    /**
     * Get raw response data
     * 
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return $this->getPopulationSummary();
    }

    /**
     * Convert to JSON string
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * String representation
     * 
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            "%s: %s",
            $this->programmeCode,
            $this->getOperationResult()
        );
    }
}