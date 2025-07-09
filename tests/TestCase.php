<?php

namespace MBLogik\TCUAPIClient\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use MBLogik\TCUAPIClient\Config\Configuration;
use MBLogik\TCUAPIClient\Config\DatabaseConfig;

abstract class TestCase extends BaseTestCase
{
    protected function getTestConfiguration(): Configuration
    {
        return new Configuration([
            'username' => 'test_user',
            'security_token' => 'test_token_123',
            'base_url' => 'https://api.tcu.go.tz',
            'timeout' => 30,
            'retry_attempts' => 3,
            'enable_logging' => false,
            'enable_database_logging' => false,
            'enable_cache' => false
        ]);
    }
    
    protected function getTestDatabaseConfig(): DatabaseConfig
    {
        return new DatabaseConfig([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'table_prefix' => 'test_'
        ]);
    }
    
    protected function getTestApplicantData(): array
    {
        return [
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe',
            'gender' => 'M',
            'form_four_index_number' => 'S0123/0001/2023',
            'form_six_index_number' => 'S0123/0001/2025',
            'nationality' => 'Tanzanian',
            'year_of_birth' => 2000,
            'applicant_category' => 'Government',
            'phone_number' => '+255123456789',
            'email_address' => 'john.doe@example.com',
            'disability_status' => false
        ];
    }
    
    protected function getTestProgrammeData(): array
    {
        return [
            'programme_code' => 'BSCS001',
            'programme_name' => 'Bachelor of Science in Computer Science',
            'programme_type' => 'degree',
            'level' => 'undergraduate',
            'duration' => 4,
            'faculty' => 'Faculty of Computing',
            'department' => 'Computer Science',
            'institution_code' => 'UDSM',
            'institution_name' => 'University of Dar es Salaam',
            'capacity' => 100,
            'available_slots' => 25,
            'minimum_grade' => 'B',
            'is_active' => true,
            'academic_year' => '2025/2026',
            'tuition_fee' => 1500000.00,
            'currency' => 'TZS',
            'mode_of_study' => 'Full Time'
        ];
    }
    
    protected function getTestApiResponse(int $statusCode = 200, string $statusDescription = 'Success', array $data = []): array
    {
        return [
            'status_code' => $statusCode,
            'status_description' => $statusDescription,
            'message' => $statusDescription,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    protected function getTestApiErrorResponse(int $statusCode = 400, string $message = 'Bad Request', array $errors = []): array
    {
        return [
            'status_code' => $statusCode,
            'status_description' => 'Error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    protected function assertValidationErrors(array $errors, array $expectedErrors): void
    {
        $this->assertNotEmpty($errors, 'Expected validation errors but none were found');
        
        foreach ($expectedErrors as $expectedError) {
            $this->assertContains($expectedError, $errors, "Expected error '{$expectedError}' not found in validation errors");
        }
    }
    
    protected function assertNoValidationErrors(array $errors): void
    {
        $this->assertEmpty($errors, 'Expected no validation errors but found: ' . implode(', ', $errors));
    }
}