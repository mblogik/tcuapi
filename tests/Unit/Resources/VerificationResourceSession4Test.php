<?php

/**
 * TCU API Client - Verification Resource Session 4 Unit Tests
 * 
 * Unit tests for Session 4 endpoints (3.16-3.20) of the VerificationResource class.
 * Tests cover validation, request formatting, and response handling for
 * document verification and certificate validation operations.
 * 
 * @package    MBLogik\TCUAPIClient\Tests\Unit\Resources
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Unit tests for Session 4 VerificationResource endpoints
 */

namespace MBLogik\TCUAPIClient\Tests\Unit\Resources;

use MBLogik\TCUAPIClient\Resources\VerificationResource;
use MBLogik\TCUAPIClient\Client\TCUAPIClient;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class VerificationResourceSession4Test extends TestCase
{
    private VerificationResource $resource;
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
        $this->resource = new VerificationResource($this->mockClient);
    }
    
    /**
     * Test 3.16: Verify Student Documents - Valid Data
     */
    public function testVerifyStudentDocumentsWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $documents = [
            [
                'document_type' => 'certificate',
                'document_number' => 'CERT123456',
                'document_data' => 'base64_encoded_document_data'
            ],
            [
                'document_type' => 'transcript',
                'document_number' => 'TRANS789012',
                'document_data' => 'base64_encoded_transcript_data'
            ]
        ];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Documents verified successfully',
            'f4indexno' => $f4indexno,
            'verification_results' => [
                [
                    'document_type' => 'certificate',
                    'document_number' => 'CERT123456',
                    'verification_status' => 'verified',
                    'verified_at' => '2025-01-09 14:30:00'
                ],
                [
                    'document_type' => 'transcript',
                    'document_number' => 'TRANS789012',
                    'verification_status' => 'verified',
                    'verified_at' => '2025-01-09 14:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/verifyStudentDocuments',
                [
                    'operation' => 'verifyStudentDocuments',
                    'f4indexno' => $f4indexno,
                    'documents' => $documents
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->verifyStudentDocuments($f4indexno, $documents);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.16: Verify Student Documents - Invalid F4 Index Number
     */
    public function testVerifyStudentDocumentsWithInvalidF4IndexNumber(): void
    {
        $documents = [
            [
                'document_type' => 'certificate',
                'document_number' => 'CERT123456',
                'document_data' => 'base64_encoded_document_data'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->verifyStudentDocuments('invalid_f4', $documents);
    }
    
    /**
     * Test 3.16: Verify Student Documents - Empty Documents Array
     */
    public function testVerifyStudentDocumentsWithEmptyDocuments(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Documents array cannot be empty');
        
        $this->resource->verifyStudentDocuments('S0123456789', []);
    }
    
    /**
     * Test 3.16: Verify Student Documents - Missing Document Fields
     */
    public function testVerifyStudentDocumentsWithMissingDocumentFields(): void
    {
        $documents = [
            [
                'document_type' => 'certificate',
                'document_number' => 'CERT123456'
                // Missing document_data
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Document data is required');
        
        $this->resource->verifyStudentDocuments('S0123456789', $documents);
    }
    
    /**
     * Test 3.17: Get Verification Status - Valid F4 Index Number
     */
    public function testGetVerificationStatusWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Verification status retrieved successfully',
            'f4indexno' => $f4indexno,
            'verification_status' => 'verified',
            'verified_at' => '2025-01-09 14:30:00',
            'documents' => [
                [
                    'document_type' => 'certificate',
                    'document_number' => 'CERT123456',
                    'status' => 'verified'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/getVerificationStatus',
                [
                    'operation' => 'getVerificationStatus',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getVerificationStatus($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.17: Get Verification Status - Invalid F4 Index Number
     */
    public function testGetVerificationStatusWithInvalidF4IndexNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid F4 Index Number format');
        
        $this->resource->getVerificationStatus('invalid_f4');
    }
    
    /**
     * Test 3.18: Validate Certificate - Valid Data
     */
    public function testValidateCertificateWithValidData(): void
    {
        $certificateNumber = 'CERT123456';
        $certificateType = 'form_four';
        $additionalData = ['year' => '2023'];
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Certificate validated successfully',
            'certificate_number' => $certificateNumber,
            'certificate_type' => $certificateType,
            'validation_status' => 'valid',
            'validated_at' => '2025-01-09 14:30:00',
            'certificate_details' => [
                'issued_by' => 'NECTA',
                'issued_date' => '2023-12-15',
                'grade' => 'A'
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/validateCertificate',
                [
                    'operation' => 'validateCertificate',
                    'certificate_number' => $certificateNumber,
                    'certificate_type' => $certificateType,
                    'additional_data' => $additionalData
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->validateCertificate($certificateNumber, $certificateType, $additionalData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.18: Validate Certificate - Empty Certificate Number
     */
    public function testValidateCertificateWithEmptyCertificateNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Certificate number is required');
        
        $this->resource->validateCertificate('', 'form_four');
    }
    
    /**
     * Test 3.18: Validate Certificate - Invalid Certificate Type
     */
    public function testValidateCertificateWithInvalidCertificateType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid certificate type');
        
        $this->resource->validateCertificate('CERT123456', 'invalid_type');
    }
    
    /**
     * Test 3.19: Submit Document Re-verification - Valid Data
     */
    public function testSubmitDocumentReVerificationWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $documents = [
            [
                'document_id' => 'DOC001',
                'document_type' => 'certificate'
            ]
        ];
        $reason = 'Document was damaged and needs re-verification';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Document re-verification submitted successfully',
            'f4indexno' => $f4indexno,
            'reverification_id' => 'REV_2025_001',
            'submitted_at' => '2025-01-09 14:30:00',
            'reason' => $reason
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/submitDocumentReVerification',
                [
                    'operation' => 'submitDocumentReVerification',
                    'f4indexno' => $f4indexno,
                    'documents' => $documents,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->submitDocumentReVerification($f4indexno, $documents, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.19: Submit Document Re-verification - Empty Reason
     */
    public function testSubmitDocumentReVerificationWithEmptyReason(): void
    {
        $documents = [
            [
                'document_id' => 'DOC001',
                'document_type' => 'certificate'
            ]
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Re-verification reason is required');
        
        $this->resource->submitDocumentReVerification('S0123456789', $documents, '');
    }
    
    /**
     * Test 3.20: Get Document Verification History - Valid F4 Index Number
     */
    public function testGetDocumentVerificationHistoryWithValidF4IndexNumber(): void
    {
        $f4indexno = 'S0123456789';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Document verification history retrieved successfully',
            'f4indexno' => $f4indexno,
            'verification_history' => [
                [
                    'verification_id' => 'VER_2025_001',
                    'document_type' => 'certificate',
                    'verification_status' => 'verified',
                    'verified_at' => '2025-01-09 14:30:00'
                ]
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/getDocumentVerificationHistory',
                [
                    'operation' => 'getDocumentVerificationHistory',
                    'f4indexno' => $f4indexno
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getDocumentVerificationHistory($f4indexno);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test 3.20: Get Document Verification History - With Document Type Filter
     */
    public function testGetDocumentVerificationHistoryWithDocumentTypeFilter(): void
    {
        $f4indexno = 'S0123456789';
        $documentType = 'certificate';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Document verification history retrieved successfully',
            'f4indexno' => $f4indexno,
            'document_type' => $documentType,
            'verification_history' => []
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/getDocumentVerificationHistory',
                [
                    'operation' => 'getDocumentVerificationHistory',
                    'f4indexno' => $f4indexno,
                    'document_type' => $documentType
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getDocumentVerificationHistory($f4indexno, $documentType);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Verify Single Document - Convenience Method
     */
    public function testVerifySingleDocumentConvenienceMethod(): void
    {
        $f4indexno = 'S0123456789';
        $documentType = 'certificate';
        $documentNumber = 'CERT123456';
        $documentData = 'base64_encoded_document_data';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Documents verified successfully'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/verifyStudentDocuments',
                [
                    'operation' => 'verifyStudentDocuments',
                    'f4indexno' => $f4indexno,
                    'documents' => [
                        [
                            'document_type' => $documentType,
                            'document_number' => $documentNumber,
                            'document_data' => $documentData
                        ]
                    ]
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->verifySingleDocument($f4indexno, $documentType, $documentNumber, $documentData);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Re-verify Single Document - Convenience Method
     */
    public function testReVerifySingleDocumentConvenienceMethod(): void
    {
        $f4indexno = 'S0123456789';
        $documentId = 'DOC001';
        $documentType = 'certificate';
        $reason = 'Document needs re-verification';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Document re-verification submitted successfully'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/submitDocumentReVerification',
                [
                    'operation' => 'submitDocumentReVerification',
                    'f4indexno' => $f4indexno,
                    'documents' => [
                        [
                            'document_id' => $documentId,
                            'document_type' => $documentType
                        ]
                    ],
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->reVerifySingleDocument($f4indexno, $documentId, $documentType, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Get Verification Statistics - Valid Programme Code
     */
    public function testGetVerificationStatisticsWithValidProgrammeCode(): void
    {
        $programmeCode = 'PROG001';
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Verification statistics retrieved successfully',
            'programme_code' => $programmeCode,
            'statistics' => [
                'total_verifications' => 150,
                'verified_documents' => 120,
                'pending_verifications' => 20,
                'rejected_verifications' => 10
            ]
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/getVerificationStatistics',
                [
                    'operation' => 'getVerificationStatistics',
                    'programme_code' => $programmeCode
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->getVerificationStatistics($programmeCode);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Verification Status - Valid Data
     */
    public function testUpdateVerificationStatusWithValidData(): void
    {
        $f4indexno = 'S0123456789';
        $status = 'verified';
        $reason = 'All documents verified successfully';
        
        $expectedResponse = [
            'status_code' => 200,
            'status_description' => 'Verification status updated successfully',
            'f4indexno' => $f4indexno,
            'status' => $status,
            'updated_at' => '2025-01-09 14:30:00'
        ];
        
        $this->mockClient->expects($this->once())
            ->method('makeRequest')
            ->with(
                '/verification/updateVerificationStatus',
                [
                    'operation' => 'updateVerificationStatus',
                    'f4indexno' => $f4indexno,
                    'status' => $status,
                    'reason' => $reason
                ],
                'POST'
            )
            ->willReturn($expectedResponse);
        
        $result = $this->resource->updateVerificationStatus($f4indexno, $status, $reason);
        
        $this->assertEquals($expectedResponse, $result);
    }
    
    /**
     * Test Update Verification Status - Invalid Status
     */
    public function testUpdateVerificationStatusWithInvalidStatus(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid verification status');
        
        $this->resource->updateVerificationStatus('S0123456789', 'invalid_status', 'Some reason');
    }
}