<?php

/**
 * TCU API Client - Username Token Authentication
 * 
 * This file contains the UsernameToken class for TCU API authentication.
 * It handles the username and session token required for TCU API access
 * according to the official API specification.
 * 
 * @package    MBLogik\TCUAPIClient\Authentication
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Authentication token management for TCU API access with validation
 */

namespace MBLogik\TCUAPIClient\Authentication;

use MBLogik\TCUAPIClient\Exceptions\AuthenticationException;

class UsernameToken
{
    private string $username;
    private string $sessionToken;
    private \DateTime $createdAt;
    
    /**
     * UsernameToken constructor
     */
    public function __construct(string $username, string $sessionToken)
    {
        $this->setUsername($username);
        $this->setSessionToken($sessionToken);
        $this->createdAt = new \DateTime();
    }
    
    /**
     * Set username with validation
     */
    private function setUsername(string $username): void
    {
        if (empty($username)) {
            throw new AuthenticationException('Username cannot be empty');
        }
        
        if (strlen($username) > 50) {
            throw new AuthenticationException('Username cannot exceed 50 characters');
        }
        
        $this->username = $username;
    }
    
    /**
     * Set session token with validation
     */
    private function setSessionToken(string $sessionToken): void
    {
        if (empty($sessionToken)) {
            throw new AuthenticationException('Session token cannot be empty');
        }
        
        if (strlen($sessionToken) < 10) {
            throw new AuthenticationException('Session token must be at least 10 characters');
        }
        
        $this->sessionToken = $sessionToken;
    }
    
    /**
     * Get username
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * Get session token
     */
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }
    
    /**
     * Get creation timestamp
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        return !empty($this->username) && !empty($this->sessionToken);
    }
    
    /**
     * Check if token is expired (based on creation time)
     */
    public function isExpired(int $expiryHours = 24): bool
    {
        $now = new \DateTime();
        $expiry = clone $this->createdAt;
        $expiry->add(new \DateInterval("PT{$expiryHours}H"));
        
        return $now > $expiry;
    }
    
    /**
     * Convert to XML array for request building
     */
    public function toXmlArray(): array
    {
        return [
            'Username' => $this->username,
            'SessionToken' => $this->sessionToken
        ];
    }
    
    /**
     * Get token as XML string
     */
    public function toXmlString(): string
    {
        return sprintf(
            "<UsernameToken>\n    <Username>%s</Username>\n    <SessionToken>%s</SessionToken>\n</UsernameToken>",
            htmlspecialchars($this->username),
            htmlspecialchars($this->sessionToken)
        );
    }
    
    /**
     * Create token from array
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['username']) || !isset($data['session_token'])) {
            throw new AuthenticationException('Username and session_token are required');
        }
        
        return new self($data['username'], $data['session_token']);
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'session_token' => $this->sessionToken,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'is_valid' => $this->isValid(),
            'is_expired' => $this->isExpired()
        ];
    }
    
    /**
     * Get token hash for caching/comparison
     */
    public function getHash(): string
    {
        return md5($this->username . ':' . $this->sessionToken);
    }
    
    /**
     * String representation
     */
    public function __toString(): string
    {
        return sprintf(
            'UsernameToken[username=%s, token=%s, created=%s]',
            $this->username,
            substr($this->sessionToken, 0, 10) . '...',
            $this->createdAt->format('Y-m-d H:i:s')
        );
    }
}