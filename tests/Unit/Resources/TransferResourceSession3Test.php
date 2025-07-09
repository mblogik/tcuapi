<?php

/**
 * TCU API Client - Transfer Resource Session 3 Unit Tests
 * 
 * Unit tests for Session 3 endpoints (3.14-3.15) of the TransferResource class.
 * Tests cover validation, request formatting, and response handling for
 * internal and inter-institutional transfer operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 3 TransferResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\TransferResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TransferResourceSession3Test extends TestCase
{
    private TransferResource $resource;
    private MockObject $mockClient;
    private Configuration $config;
    
    protected function setUp(): void
    {
        // Create mock configuration
        $this->config = new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token',
            'base_url' => 'https://api.tcu.go.tz',
            'timeout' => 30
        ]);
        
        // Create mock client
        $this->mockClient = $this->createMock(TCUAPIClient::class);
        $this->mockClient->method('getConfig')->willReturn($this->config);
        
        // Create resource instance
        $this->resource = new TransferResource($this->mockClient);
    }
    
    /**
     * Test 3.14: Submit Internal Transfers - Valid Data
     */
    public function testSubmitInternalTransfersWithValidData(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Better alignment with career goals'
            ],
            [
                'f4indexno' => 'S0123456790',
                'from_programme_code' => 'PROG003',
                'to_programme_code' => 'PROG004',
                'reason' => 'Academic performance improvement'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Internal transfers submitted successfully',
            'transfer_type' => 'internal',
            'submitted_transfers' => 2,
            'transfers' => [
                [
                    'f4indexno' => 'S0123456789',
                    'transfer_id' => 'INT_2025_001',
                    'status' => 'pending',
                    'submitted_at' => '2025-01-09 15:30:00'
                ],
                [
                    'f4indexno' => 'S0123456790',
                    'transfer_id' => 'INT_2025_002',
                    'status' => 'pending',
                    'submitted_at' => '2025-01-09 15:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/submitInternalTransfers',
                [
                    'operation' => 'submitInternalTransfers',
                    'transfer_type' => 'internal',
                    'transfers' => $transferData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitInternalTransfers($transferData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.14: Submit Internal Transfers - Missing Required Fields
     */
    public function testSubmitInternalTransfersWithMissingRequiredFields(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_programme_code' => 'PROG001'
                // Missing to_programme_code and reason
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Target programme code is required');
        
        $this->resource->submitInternalTransfers($transferData);
    }
    
    /**
     * Test 3.14: Submit Internal Transfers - Invalid F4 Index Number
     */
    public function testSubmitInternalTransfersWithInvalidF4IndexNumber(): void
    {
        $transferData = [
            [
                'f4indexno' => 'invalid_f4',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Transfer reason'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 index number format');
        
        $this->resource->submitInternalTransfers($transferData);
    }
    
    /**
     * Test 3.14: Submit Internal Transfers - Same Source and Target Programme
     */
    public function testSubmitInternalTransfersWithSameSourceAndTargetProgramme(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG001',
                'reason' => 'Transfer reason'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Source and target programmes cannot be the same');
        
        $this->resource->submitInternalTransfers($transferData);
    }
    
    /**
     * Test 3.15: Submit Inter-Institutional Transfers - Valid Data
     */
    public function testSubmitInterInstitutionalTransfersWithValidData(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_institution_code' => 'INST001',
                'to_institution_code' => 'INST002',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Better facilities and resources'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Inter-institutional transfers submitted successfully',
            'transfer_type' => 'inter_institutional',
            'submitted_transfers' => 1,
            'transfers' => [
                [
                    'f4indexno' => 'S0123456789',
                    'transfer_id' => 'INTER_2025_001',
                    'status' => 'pending',
                    'submitted_at' => '2025-01-09 15:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/submitInterInstitutionalTransfers',
                [
                    'operation' => 'submitInterInstitutionalTransfers',
                    'transfer_type' => 'inter_institutional',
                    'transfers' => $transferData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitInterInstitutionalTransfers($transferData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.15: Submit Inter-Institutional Transfers - Missing Institution Codes
     */
    public function testSubmitInterInstitutionalTransfersWithMissingInstitutionCodes(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Transfer reason'
                // Missing institution codes
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Source institution code is required');
        
        $this->resource->submitInterInstitutionalTransfers($transferData);
    }
    
    /**
     * Test 3.15: Submit Inter-Institutional Transfers - Same Source and Target Institution
     */
    public function testSubmitInterInstitutionalTransfersWithSameSourceAndTargetInstitution(): void
    {
        $transferData = [
            [
                'f4indexno' => 'S0123456789',
                'from_institution_code' => 'INST001',
                'to_institution_code' => 'INST001',
                'from_programme_code' => 'PROG001',
                'to_programme_code' => 'PROG002',
                'reason' => 'Transfer reason'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Source and target institutions cannot be the same');
        
        $this->resource->submitInterInstitutionalTransfers($transferData);
    }
    
    /**
     * Test Submit Single Internal Transfer - Valid Data
     */
    public function testSubmitSingleInternalTransferWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $fromProgrammeCode = 'PROG001';
        $toProgrammeCode = 'PROG002';
        $reason = 'Better alignment with career goals';
        $additionalData = ['academic_year' => '2025'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Internal transfers submitted successfully',
            'transfer_type' => 'internal',
            'submitted_transfers' => 1
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/submitInternalTransfers',
                [
                    'operation' => 'submitInternalTransfers',
                    'transfer_type' => 'internal',
                    'transfers' => [
                        [
                            'f4indexno' => $f4indexno,
                            'from_programme_code' => $fromProgrammeCode,
                            'to_programme_code' => $toProgrammeCode,
                            'reason' => $reason,
                            'additional_data' => $additionalData
                        ]
                    ]
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitSingleInternalTransfer(
            $f4indexno,
            $fromProgrammeCode,
            $toProgrammeCode,
            $reason,
            $additionalData
        );
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Submit Single Internal Transfer - Invalid F4 Index Number
     */
    public function testSubmitSingleInternalTransferWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->submitSingleInternalTransfer(
            'invalid_f4',
            'PROG001',
            'PROG002',
            'Transfer reason'
        );
    }
    
    /**
     * Test Submit Single Internal Transfer - Same Source and Target Programme
     */
    public function testSubmitSingleInternalTransferWithSameSourceAndTargetProgramme(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Source and target programmes cannot be the same');
        
        $this->resource->submitSingleInternalTransfer(
            'S0123456789',
            'PROG001',
            'PROG001',
            'Transfer reason'
        );
    }
    
    /**
     * Test Submit Single Inter-Institutional Transfer - Valid Data
     */
    public function testSubmitSingleInterInstitutionalTransferWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $fromInstitutionCode = 'INST001';
        $toInstitutionCode = 'INST002';
        $fromProgrammeCode = 'PROG001';
        $toProgrammeCode = 'PROG002';
        $reason = 'Better facilities and resources';
        $additionalData = ['academic_year' => '2025'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Inter-institutional transfers submitted successfully',
            'transfer_type' => 'inter_institutional',
            'submitted_transfers' => 1
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/admission/submitInterInstitutionalTransfers',
                [
                    'operation' => 'submitInterInstitutionalTransfers',
                    'transfer_type' => 'inter_institutional',
                    'transfers' => [
                        [
                            'f4indexno' => $f4indexno,
                            'from_institution_code' => $fromInstitutionCode,
                            'to_institution_code' => $toInstitutionCode,
                            'from_programme_code' => $fromProgrammeCode,
                            'to_programme_code' => $toProgrammeCode,
                            'reason' => $reason,
                            'additional_data' => $additionalData
                        ]
                    ]
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitSingleInterInstitutionalTransfer(
            $f4indexno,
            $fromInstitutionCode,
            $toInstitutionCode,
            $fromProgrammeCode,
            $toProgrammeCode,
            $reason,
            $additionalData
        );
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Submit Single Inter-Institutional Transfer - Same Source and Target Institution
     */
    public function testSubmitSingleInterInstitutionalTransferWithSameSourceAndTargetInstitution(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Source and target institutions cannot be the same');
        
        $this->resource->submitSingleInterInstitutionalTransfer(
            'S0123456789',
            'INST001',
            'INST001',
            'PROG001',
            'PROG002',
            'Transfer reason'
        );
    }
    
    /**
     * Test Empty Transfer Data
     */
    public function testSubmitInternalTransfersWithEmptyData(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Transfer data cannot be empty');
        
        $this->resource->submitInternalTransfers([]);
    }
    
    /**
     * Test Get Internal Transfer Status - Valid Programme Code
     */
    public function testGetInternalTransferStatusWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Internal transfer status retrieved successfully',
            'programme_code' => $programmeCode,
            'transfers' => [
                [
                    'f4indexno' => 'S0123456789',
                    'transfer_id' => 'INT_2025_001',
                    'status' => 'approved',
                    'from_programme_code' => 'PROG001',
                    'to_programme_code' => 'PROG002'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/transfers/getInternalStatus',
                [
                    'operation' => 'getInternalTransferStatus',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getInternalTransferStatus($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Internal Transfer Status - Invalid Programme Code
     */
    public function testGetInternalTransferStatusWithInvalidProgrammeCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid programme code format');
        
        $this->resource->getInternalTransferStatus('invalid_programme');
    }
    
    /**
     * Test Get Inter-Institutional Transfer Status - Valid Programme Code
     */
    public function testGetInterInstitutionalTransferStatusWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Inter-institutional transfer status retrieved successfully',
            'programme_code' => $programmeCode,
            'transfers' => [
                [
                    'f4indexno' => 'S0123456789',
                    'transfer_id' => 'INTER_2025_001',
                    'status' => 'pending',
                    'from_institution_code' => 'INST001',
                    'to_institution_code' => 'INST002'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/transfers/getInterInstitutionalStatus',
                [
                    'operation' => 'getInterInstitutionalTransferStatus',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getInterInstitutionalTransferStatus($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Transfer History - Valid F4 Index Number
     */
    public function testGetTransferHistoryWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Transfer history retrieved successfully',
            'f4indexno' => $f4indexno,
            'transfers' => [
                [
                    'transfer_id' => 'INT_2025_001',
                    'transfer_type' => 'internal',
                    'status' => 'approved',
                    'submitted_at' => '2025-01-09 15:30:00',
                    'approved_at' => '2025-01-09 16:00:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/transfers/history',
                [
                    'operation' => 'getTransferHistory',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getTransferHistory($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Transfer History - Invalid F4 Index Number
     */
    public function testGetTransferHistoryWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getTransferHistory('invalid_f4');
    }
    
    /**
     * Test Cancel Transfer - Valid Data
     */
    public function testCancelTransferWithValidData(): void
    {
        $transferId = 'INT_2025_001';
        $reason = 'Changed my mind about the transfer';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Transfer cancelled successfully',
            'transfer_id' => $transferId,
            'reason' => $reason,
            'cancelled_at' => '2025-01-09 17:00:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/transfers/cancel',
                [
                    'operation' => 'cancelTransfer',
                    'transfer_id' => $transferId,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->cancelTransfer($transferId, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Cancel Transfer - Empty Transfer ID
     */
    public function testCancelTransferWithEmptyTransferId(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Transfer ID is required');
        
        $this->resource->cancelTransfer('', 'Some reason');
    }
    
    /**
     * Test Cancel Transfer - Empty Reason
     */
    public function testCancelTransferWithEmptyReason(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cancellation reason is required');
        
        $this->resource->cancelTransfer('INT_2025_001', '');
    }
}