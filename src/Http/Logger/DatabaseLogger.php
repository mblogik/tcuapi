<?php

/**
 * TCU API Client - Enterprise Database Logger
 * 
 * This file contains the DatabaseLogger class for comprehensive API request/response
 * logging with support for MySQL and PostgreSQL databases. It provides enterprise-level
 * logging capabilities with proper transaction handling and connection management.
 * 
 * @package    MBLogik\TCUAPIClient\Http\Logger
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Enterprise-level database logging with MySQL/PostgreSQL support,
 *             comprehensive request/response tracking, and performance monitoring.
 */

namespace MBLogik\TCUAPIClient\Http\Logger;

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use MBLogik\TCUAPIClient\Exceptions\TcuApiException;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class DatabaseLogger
{
    private DatabaseConfig $config;
    private ?PDO $connection = null;
    private ?LoggerInterface $systemLogger;
    private array $preparedStatements = [];
    
    // Table names
    private const TABLE_API_LOGS = 'tcu_api_logs';
    private const TABLE_AUTH_LOGS = 'tcu_api_auth_logs';
    private const TABLE_ERROR_LOGS = 'tcu_api_error_logs';
    
    /**
     * DatabaseLogger constructor
     */
    public function __construct(DatabaseConfig $config, ?LoggerInterface $systemLogger = null)
    {
        $this->config = $config;
        $this->systemLogger = $systemLogger;
        $this->initializeConnection();
    }
    
    /**
     * Initialize database connection
     */
    private function initializeConnection(): void
    {
        try {
            $dsn = $this->buildDsn();
            
            $this->connection = new PDO(
                $dsn,
                $this->config->getUsername(),
                $this->config->getPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_TIMEOUT => $this->config->getConnectionTimeout()
                ]
            );
            
            // Set timezone for consistent logging
            $this->setTimezone();
            
            // Log successful connection
            $this->logSystemMessage('Database connection established successfully', 'info');
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Database connection failed: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Build DSN string based on database driver
     */
    private function buildDsn(): string
    {
        $driver = $this->config->getDriver();
        
        switch ($driver) {
            case 'mysql':
                return sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $this->config->getHost(),
                    $this->config->getPort(),
                    $this->config->getDatabase()
                );
                
            case 'pgsql':
                return sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s;options=--client_encoding=UTF8',
                    $this->config->getHost(),
                    $this->config->getPort(),
                    $this->config->getDatabase()
                );
                
            default:
                throw new TcuApiException("Unsupported database driver: {$driver}");
        }
    }
    
    /**
     * Set appropriate timezone
     */
    private function setTimezone(): void
    {
        try {
            $driver = $this->config->getDriver();
            
            if ($driver === 'mysql') {
                $this->connection->exec("SET time_zone = '+00:00'");
            } elseif ($driver === 'pgsql') {
                $this->connection->exec("SET TIME ZONE 'UTC'");
            }
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to set timezone: ' . $e->getMessage(), 'warning');
        }
    }
    
    /**
     * Log API request
     */
    public function logRequest(array $requestData): int
    {
        $sql = "INSERT INTO " . self::TABLE_API_LOGS . " (
            endpoint,
            method,
            request_data,
            request_size,
            request_headers,
            ip_address,
            user_agent,
            session_id,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->getPreparedStatement($sql);
            
            $stmt->execute([
                $requestData['endpoint'] ?? '',
                $requestData['method'] ?? 'POST',
                $requestData['request_data'] ?? '',
                $requestData['request_size'] ?? 0,
                json_encode($requestData['headers'] ?? []),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                session_id(),
                date('Y-m-d H:i:s')
            ]);
            
            $logId = (int) $this->connection->lastInsertId();
            
            $this->logSystemMessage("API request logged with ID: {$logId}", 'debug');
            
            return $logId;
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to log API request: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to log API request: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Log API response
     */
    public function logResponse(int $logId, array $responseData): void
    {
        $sql = "UPDATE " . self::TABLE_API_LOGS . " SET
            response_data = ?,
            response_size = ?,
            status_code = ?,
            execution_time = ?,
            response_headers = ?,
            updated_at = ?
        WHERE id = ?";
        
        try {
            $stmt = $this->getPreparedStatement($sql);
            
            $stmt->execute([
                $responseData['response_data'] ?? '',
                $responseData['response_size'] ?? 0,
                $responseData['status_code'] ?? 0,
                $responseData['execution_time'] ?? 0,
                json_encode($responseData['headers'] ?? []),
                date('Y-m-d H:i:s'),
                $logId
            ]);
            
            $this->logSystemMessage("API response logged for ID: {$logId}", 'debug');
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to log API response: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to log API response: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Log API error
     */
    public function logError(int $logId, array $errorData): void
    {
        // Update main log with error
        $sql = "UPDATE " . self::TABLE_API_LOGS . " SET
            error_message = ?,
            error_code = ?,
            execution_time = ?,
            updated_at = ?
        WHERE id = ?";
        
        try {
            $stmt = $this->getPreparedStatement($sql);
            
            $stmt->execute([
                $errorData['error_message'] ?? '',
                $errorData['error_code'] ?? 0,
                $errorData['execution_time'] ?? 0,
                date('Y-m-d H:i:s'),
                $logId
            ]);
            
            // Log detailed error in separate table
            $this->logDetailedError($logId, $errorData);
            
            $this->logSystemMessage("API error logged for ID: {$logId}", 'debug');
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to log API error: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to log API error: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Log detailed error information
     */
    private function logDetailedError(int $logId, array $errorData): void
    {
        $sql = "INSERT INTO " . self::TABLE_ERROR_LOGS . " (
            api_log_id,
            error_type,
            error_message,
            error_code,
            stack_trace,
            context_data,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->getPreparedStatement($sql);
            
            $stmt->execute([
                $logId,
                $errorData['error_type'] ?? 'api_error',
                $errorData['error_message'] ?? '',
                $errorData['error_code'] ?? 0,
                $errorData['stack_trace'] ?? '',
                json_encode($errorData['context'] ?? []),
                date('Y-m-d H:i:s')
            ]);
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to log detailed error: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Log authentication attempt
     */
    public function logAuthenticationAttempt(array $authData): void
    {
        $sql = "INSERT INTO " . self::TABLE_AUTH_LOGS . " (
            username,
            operation,
            success,
            execution_time,
            error_message,
            ip_address,
            user_agent,
            session_id,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->getPreparedStatement($sql);
            
            $stmt->execute([
                $authData['username'] ?? '',
                $authData['operation'] ?? '',
                $authData['success'] ?? false,
                $authData['execution_time'] ?? 0,
                $authData['error_message'] ?? null,
                $authData['ip_address'] ?? 'unknown',
                $authData['user_agent'] ?? 'unknown',
                session_id(),
                date('Y-m-d H:i:s')
            ]);
            
            $this->logSystemMessage("Authentication attempt logged for user: {$authData['username']}", 'debug');
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to log authentication attempt: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to log authentication attempt: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Get prepared statement (cached)
     */
    private function getPreparedStatement(string $sql): \PDOStatement
    {
        $hash = md5($sql);
        
        if (!isset($this->preparedStatements[$hash])) {
            $this->preparedStatements[$hash] = $this->connection->prepare($sql);
        }
        
        return $this->preparedStatements[$hash];
    }
    
    /**
     * Get API logs with filters
     */
    public function getApiLogs(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM " . self::TABLE_API_LOGS;
        $params = [];
        $conditions = [];
        
        // Apply filters
        if (!empty($filters['endpoint'])) {
            $conditions[] = "endpoint = ?";
            $params[] = $filters['endpoint'];
        }
        
        if (!empty($filters['status_code'])) {
            $conditions[] = "status_code = ?";
            $params[] = $filters['status_code'];
        }
        
        if (!empty($filters['date_from'])) {
            $conditions[] = "created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to fetch API logs: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to fetch API logs: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Get API statistics
     */
    public function getApiStatistics(string $dateFrom, string $dateTo): array
    {
        $sql = "SELECT 
            COUNT(*) as total_requests,
            COUNT(CASE WHEN status_code = 200 THEN 1 END) as successful_requests,
            COUNT(CASE WHEN status_code != 200 THEN 1 END) as failed_requests,
            AVG(execution_time) as avg_execution_time,
            MAX(execution_time) as max_execution_time,
            MIN(execution_time) as min_execution_time
        FROM " . self::TABLE_API_LOGS . "
        WHERE created_at BETWEEN ? AND ?";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$dateFrom, $dateTo]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logSystemMessage('Failed to fetch API statistics: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to fetch API statistics: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Clean old logs
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        try {
            $this->connection->beginTransaction();
            
            // Delete old error logs
            $stmt = $this->connection->prepare("DELETE FROM " . self::TABLE_ERROR_LOGS . " WHERE created_at < ?");
            $stmt->execute([$cutoffDate]);
            $errorLogsDeleted = $stmt->rowCount();
            
            // Delete old auth logs
            $stmt = $this->connection->prepare("DELETE FROM " . self::TABLE_AUTH_LOGS . " WHERE created_at < ?");
            $stmt->execute([$cutoffDate]);
            $authLogsDeleted = $stmt->rowCount();
            
            // Delete old API logs
            $stmt = $this->connection->prepare("DELETE FROM " . self::TABLE_API_LOGS . " WHERE created_at < ?");
            $stmt->execute([$cutoffDate]);
            $apiLogsDeleted = $stmt->rowCount();
            
            $this->connection->commit();
            
            $totalDeleted = $errorLogsDeleted + $authLogsDeleted + $apiLogsDeleted;
            
            $this->logSystemMessage("Cleaned {$totalDeleted} old log entries", 'info');
            
            return $totalDeleted;
            
        } catch (PDOException $e) {
            $this->connection->rollback();
            $this->logSystemMessage('Failed to clean old logs: ' . $e->getMessage(), 'error');
            throw new TcuApiException('Failed to clean old logs: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Test database connection
     */
    public function testConnection(): bool
    {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            $this->logSystemMessage('Database connection test failed: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Log system message
     */
    private function logSystemMessage(string $message, string $level = 'info'): void
    {
        if ($this->systemLogger) {
            $this->systemLogger->$level($message, ['component' => 'DatabaseLogger']);
        }
    }
    
    /**
     * Close database connection
     */
    public function close(): void
    {
        $this->preparedStatements = [];
        $this->connection = null;
        $this->logSystemMessage('Database connection closed', 'info');
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }
}