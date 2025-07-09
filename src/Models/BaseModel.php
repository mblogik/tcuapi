<?php

/**
 * TCU API Client - Base Model Class
 * 
 * This file contains the base model class for all TCU API Client models.
 * It provides common functionality like data validation, casting, fluent interface,
 * and JSON serialization for all model classes.
 * 
 * @package    MBLogik\TCUAPIClient\Models
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Foundation class for all models with validation, casting, and
 *             utility methods for structured data handling.
 */

namespace MBLogik\TCUAPIClient\Models;

use MBLogik\TCUAPIClient\Exceptions\ValidationException;

abstract class BaseModel
{
    protected array $data = [];
    protected array $fillable = [];
    protected array $required = [];
    protected array $casts = [];
    
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }
    
    public function fill(array $data): static
    {
        foreach ($data as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->data[$key] = $this->castValue($key, $value);
            }
        }
        
        return $this;
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): static
    {
        if (empty($this->fillable) || in_array($key, $this->fillable)) {
            $this->data[$key] = $this->castValue($key, $value);
        }
        
        return $this;
    }
    
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
    
    public function toArray(): array
    {
        return $this->data;
    }
    
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
    
    public function validate(): array
    {
        $errors = [];
        
        // Check required fields
        foreach ($this->required as $field) {
            if (!$this->has($field) || empty($this->get($field))) {
                $errors[] = "Field '{$field}' is required";
            }
        }
        
        // Custom validation
        $customErrors = $this->customValidation();
        if (!empty($customErrors)) {
            $errors = array_merge($errors, $customErrors);
        }
        
        return $errors;
    }
    
    protected function customValidation(): array
    {
        return [];
    }
    
    protected function castValue(string $key, mixed $value): mixed
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }
        
        $cast = $this->casts[$key];
        
        return match ($cast) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            'object' => (object) $value,
            default => $value
        };
    }
    
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }
}