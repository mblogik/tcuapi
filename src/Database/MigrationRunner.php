<?php

/**
 * TCU API Client - Database Migration Runner
 * 
 * Migration runner for TCU API Client database operations.
 * Handles running, rolling back, and managing database migrations
 * with support for batch operations and migration status tracking.
 * 
 * @package    MBLogik\TCUAPIClient\Database
 * @author     Ombeni Aidani <developer@mblogik.com>
 * @company    MBLogik
 * @date       2025-01-09
 * @version    1.0.0
 * @license    MIT
 * 
 * @purpose    Database migration management system with batch operations
 */

namespace MBLogik\TCUAPIClient\Database;

use MBLogik\TCUAPIClient\Config\DatabaseConfig;
use MBLogik\TCUAPIClient\Exceptions\TCUAPIException;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationRunner
{
    private Capsule $capsule;
    private DatabaseConfig $config;
    private string $migrationsPath;
    private string $migrationsTable;
    
    public function __construct(DatabaseConfig $config, string $migrationsPath = null)
    {
        $this->config = $config;
        $this->migrationsPath = $migrationsPath ?: __DIR__ . '/../../database/migrations';
        $this->migrationsTable = $config->getTablePrefix() . 'migrations';
        
        $this->capsule = new Capsule;
        $this->capsule->addConnection($config->toArray());
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
        
        $this->ensureMigrationsTable();
    }
    
    private function ensureMigrationsTable(): void
    {
        if (!$this->capsule->schema()->hasTable($this->migrationsTable)) {
            $this->capsule->schema()->create($this->migrationsTable, function (Blueprint $table) {
                $table->id();
                $table->string('migration', 255);
                $table->integer('batch');
                $table->timestamp('executed_at')->useCurrent();
                
                $table->unique('migration');
                $table->index(['batch', 'executed_at']);
            });
        }
    }
    
    public function migrate(): array
    {
        $migrations = $this->getPendingMigrations();
        $executed = [];
        
        if (empty($migrations)) {
            return ['message' => 'No pending migrations'];
        }
        
        $batch = $this->getNextBatchNumber();
        
        foreach ($migrations as $migration) {
            try {
                $this->executeMigration($migration, $batch);
                $executed[] = $migration;
                echo "Migrated: {$migration}\n";
            } catch (\Exception $e) {
                throw new TCUAPIException(
                    "Migration failed for {$migration}: " . $e->getMessage(),
                    0,
                    $e
                );
            }
        }
        
        return [
            'message' => 'Migration completed successfully',
            'executed' => $executed,
            'batch' => $batch
        ];
    }
    
    public function rollback(int $steps = 1): array
    {
        $rolledBack = [];
        
        for ($i = 0; $i < $steps; $i++) {
            $batch = $this->getLastBatchNumber();
            if ($batch === 0) {
                break;
            }
            
            $migrations = $this->getMigrationsInBatch($batch);
            
            foreach (array_reverse($migrations) as $migration) {
                try {
                    $this->rollbackMigration($migration);
                    $rolledBack[] = $migration;
                    echo "Rolled back: {$migration}\n";
                } catch (\Exception $e) {
                    throw new TCUAPIException(
                        "Rollback failed for {$migration}: " . $e->getMessage(),
                        0,
                        $e
                    );
                }
            }
        }
        
        return [
            'message' => 'Rollback completed successfully',
            'rolled_back' => $rolledBack
        ];
    }
    
    public function reset(): array
    {
        $allMigrations = $this->getExecutedMigrations();
        $rolledBack = [];
        
        foreach (array_reverse($allMigrations) as $migration) {
            try {
                $this->rollbackMigration($migration);
                $rolledBack[] = $migration;
                echo "Rolled back: {$migration}\n";
            } catch (\Exception $e) {
                throw new TCUAPIException(
                    "Reset failed for {$migration}: " . $e->getMessage(),
                    0,
                    $e
                );
            }
        }
        
        return [
            'message' => 'Reset completed successfully',
            'rolled_back' => $rolledBack
        ];
    }
    
    public function status(): array
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        
        $status = [];
        
        foreach ($allMigrations as $migration) {
            $status[] = [
                'migration' => $migration,
                'status' => in_array($migration, $executedMigrations) ? 'executed' : 'pending'
            ];
        }
        
        return $status;
    }
    
    private function getPendingMigrations(): array
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        
        return array_diff($allMigrations, $executedMigrations);
    }
    
    private function getAllMigrationFiles(): array
    {
        $migrations = [];
        
        if (is_dir($this->migrationsPath)) {
            $files = scandir($this->migrationsPath);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $migrations[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }
        
        sort($migrations);
        return $migrations;
    }
    
    private function getExecutedMigrations(): array
    {
        return $this->capsule->table($this->migrationsTable)
            ->orderBy('migration')
            ->pluck('migration')
            ->toArray();
    }
    
    private function getMigrationsInBatch(int $batch): array
    {
        return $this->capsule->table($this->migrationsTable)
            ->where('batch', $batch)
            ->orderBy('migration')
            ->pluck('migration')
            ->toArray();
    }
    
    private function getNextBatchNumber(): int
    {
        $lastBatch = $this->capsule->table($this->migrationsTable)
            ->max('batch');
        
        return ($lastBatch ?? 0) + 1;
    }
    
    private function getLastBatchNumber(): int
    {
        return $this->capsule->table($this->migrationsTable)
            ->max('batch') ?? 0;
    }
    
    private function executeMigration(string $migration, int $batch): void
    {
        $migrationFile = $this->migrationsPath . '/' . $migration . '.php';
        
        if (!file_exists($migrationFile)) {
            throw new TCUAPIException("Migration file not found: {$migrationFile}");
        }
        
        $migrationInstance = require $migrationFile;
        
        if (!($migrationInstance instanceof Migration)) {
            throw new TCUAPIException("Invalid migration class in {$migration}");
        }
        
        // Execute the migration
        $migrationInstance->up();
        
        // Record the migration
        $this->capsule->table($this->migrationsTable)->insert([
            'migration' => $migration,
            'batch' => $batch,
            'executed_at' => now()
        ]);
    }
    
    private function rollbackMigration(string $migration): void
    {
        $migrationFile = $this->migrationsPath . '/' . $migration . '.php';
        
        if (!file_exists($migrationFile)) {
            throw new TCUAPIException("Migration file not found: {$migrationFile}");
        }
        
        $migrationInstance = require $migrationFile;
        
        if (!($migrationInstance instanceof Migration)) {
            throw new TCUAPIException("Invalid migration class in {$migration}");
        }
        
        // Execute the rollback
        $migrationInstance->down();
        
        // Remove the migration record
        $this->capsule->table($this->migrationsTable)
            ->where('migration', $migration)
            ->delete();
    }
}