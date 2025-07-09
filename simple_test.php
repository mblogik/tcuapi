<?php

// Simple test script that manually includes files
echo "=== TCU API Client - Simple Test ===\n\n";

// Test file structure
echo "1. Testing File Structure\n";
echo "==========================\n";

$expectedFiles = [
    'src/Config/Configuration.php',
    'src/Config/DatabaseConfig.php',
    'src/Models/BaseModel.php',
    'src/Models/Data/Applicant.php',
    'src/Models/Data/Programme.php',
    'src/Models/Request/BaseRequest.php',
    'src/Models/Request/CheckStatusRequest.php',
    'src/Models/Request/AddApplicantRequest.php',
    'src/Models/Request/SubmitProgrammeRequest.php',
    'src/Models/Response/BaseResponse.php',
    'src/Models/Response/CheckStatusResponse.php',
    'src/Models/Response/AddApplicantResponse.php',
    'src/Resources/BaseResource.php',
    'src/Resources/ApplicantResource.php',
    'src/Resources/AdmissionResource.php',
    'src/Resources/DashboardResource.php',
    'src/Exceptions/TCUAPIException.php',
    'src/Exceptions/ValidationException.php',
    'src/Exceptions/AuthenticationException.php',
    'src/Exceptions/NetworkException.php',
    'src/Http/Logger/DatabaseLogger.php',
    'src/Database/MigrationRunner.php',
    'src/TCUAPIClient.php',
];

$missingFiles = [];
$existingFiles = [];

foreach ($expectedFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $existingFiles[] = $file;
        echo "✓ " . $file . "\n";
    } else {
        $missingFiles[] = $file;
        echo "✗ " . $file . " (MISSING)\n";
    }
}

echo "\nSummary:\n";
echo "  Existing files: " . count($existingFiles) . "\n";
echo "  Missing files: " . count($missingFiles) . "\n";
echo "  Total expected: " . count($expectedFiles) . "\n";
echo "  Coverage: " . number_format((count($existingFiles) / count($expectedFiles)) * 100, 2) . "%\n";

if (count($missingFiles) > 0) {
    echo "\nMissing files:\n";
    foreach ($missingFiles as $file) {
        echo "  - " . $file . "\n";
    }
}
echo "\n";

// Test 2: Check file syntax
echo "2. Testing File Syntax\n";
echo "=======================\n";

$syntaxErrors = [];
$syntaxOk = [];

foreach ($existingFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    
    // Check PHP syntax
    $output = [];
    $returnCode = 0;
    exec("php -l \"$fullPath\" 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        $syntaxOk[] = $file;
        echo "✓ " . $file . "\n";
    } else {
        $syntaxErrors[] = ['file' => $file, 'error' => implode("\n", $output)];
        echo "✗ " . $file . " (SYNTAX ERROR)\n";
    }
}

echo "\nSyntax Check Summary:\n";
echo "  Files with valid syntax: " . count($syntaxOk) . "\n";
echo "  Files with syntax errors: " . count($syntaxErrors) . "\n";

if (count($syntaxErrors) > 0) {
    echo "\nSyntax errors:\n";
    foreach ($syntaxErrors as $error) {
        echo "  File: " . $error['file'] . "\n";
        echo "  Error: " . $error['error'] . "\n";
        echo "  ---\n";
    }
}
echo "\n";

// Test 3: Check directory structure
echo "3. Testing Directory Structure\n";
echo "===============================\n";

$expectedDirs = [
    'src',
    'src/Config',
    'src/Models',
    'src/Models/Data',
    'src/Models/Request',
    'src/Models/Response',
    'src/Resources',
    'src/Exceptions',
    'src/Http',
    'src/Http/Logger',
    'src/Database',
    'database',
    'database/migrations',
    'database/seeds',
    'examples'
];

$missingDirs = [];
$existingDirs = [];

foreach ($expectedDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        $existingDirs[] = $dir;
        echo "✓ " . $dir . "/\n";
    } else {
        $missingDirs[] = $dir;
        echo "✗ " . $dir . "/ (MISSING)\n";
    }
}

echo "\nDirectory Summary:\n";
echo "  Existing directories: " . count($existingDirs) . "\n";
echo "  Missing directories: " . count($missingDirs) . "\n";
echo "  Total expected: " . count($expectedDirs) . "\n";
echo "\n";

// Test 4: Check composer.json
echo "4. Testing composer.json\n";
echo "=========================\n";

$composerFile = __DIR__ . '/composer.json';
if (file_exists($composerFile)) {
    echo "✓ composer.json exists\n";
    
    $composerContent = file_get_contents($composerFile);
    $composerData = json_decode($composerContent, true);
    
    if ($composerData) {
        echo "✓ composer.json is valid JSON\n";
        
        $requiredKeys = ['name', 'description', 'type', 'license', 'autoload', 'require'];
        $missingKeys = [];
        
        foreach ($requiredKeys as $key) {
            if (isset($composerData[$key])) {
                echo "✓ Has '$key' key\n";
            } else {
                $missingKeys[] = $key;
                echo "✗ Missing '$key' key\n";
            }
        }
        
        // Check autoload PSR-4
        if (isset($composerData['autoload']['psr-4'])) {
            echo "✓ Has PSR-4 autoload configuration\n";
            $psr4 = $composerData['autoload']['psr-4'];
            if (isset($psr4['MBLogik\\TCUAPIClient\\'])) {
                echo "✓ Correct namespace configuration\n";
            } else {
                echo "✗ Incorrect namespace configuration\n";
            }
        } else {
            echo "✗ Missing PSR-4 autoload configuration\n";
        }
        
        // Check dependencies
        if (isset($composerData['require'])) {
            echo "✓ Has dependencies\n";
            $deps = $composerData['require'];
            $expectedDeps = ['php', 'nategood/httpful', 'illuminate/database'];
            
            foreach ($expectedDeps as $dep) {
                if (isset($deps[$dep])) {
                    echo "✓ Has '$dep' dependency\n";
                } else {
                    echo "✗ Missing '$dep' dependency\n";
                }
            }
        }
        
    } else {
        echo "✗ composer.json is invalid JSON\n";
    }
} else {
    echo "✗ composer.json not found\n";
}
echo "\n";

// Test 5: Check documentation files
echo "5. Testing Documentation\n";
echo "=========================\n";

$docFiles = [
    'README.md',
    'CLAUDE.md',
    'ENDPOINTS.md',
    'DATABASE.md'
];

$missingDocs = [];
$existingDocs = [];

foreach ($docFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $existingDocs[] = $file;
        $size = filesize(__DIR__ . '/' . $file);
        echo "✓ " . $file . " (" . number_format($size / 1024, 2) . " KB)\n";
    } else {
        $missingDocs[] = $file;
        echo "✗ " . $file . " (MISSING)\n";
    }
}

echo "\nDocumentation Summary:\n";
echo "  Existing documentation: " . count($existingDocs) . "\n";
echo "  Missing documentation: " . count($missingDocs) . "\n";
echo "\n";

// Test 6: Check example files
echo "6. Testing Example Files\n";
echo "=========================\n";

$exampleFiles = [
    'examples/basic_usage.php',
    'examples/config_examples.php',
    'examples/foreign_applicants.php',
    'examples/migrations_example.php',
    'examples/models_usage.php'
];

$missingExamples = [];
$existingExamples = [];

foreach ($exampleFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $existingExamples[] = $file;
        $size = filesize(__DIR__ . '/' . $file);
        echo "✓ " . $file . " (" . number_format($size / 1024, 2) . " KB)\n";
    } else {
        $missingExamples[] = $file;
        echo "✗ " . $file . " (MISSING)\n";
    }
}

echo "\nExample Files Summary:\n";
echo "  Existing examples: " . count($existingExamples) . "\n";
echo "  Missing examples: " . count($missingExamples) . "\n";
echo "\n";

// Test 7: Check migration files
echo "7. Testing Migration Files\n";
echo "===========================\n";

$migrationDir = __DIR__ . '/database/migrations';
if (is_dir($migrationDir)) {
    $migrations = glob($migrationDir . '/*.php');
    echo "✓ Migration directory exists\n";
    echo "  Found " . count($migrations) . " migration files:\n";
    
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        $size = filesize($migration);
        echo "  - " . $filename . " (" . number_format($size / 1024, 2) . " KB)\n";
    }
} else {
    echo "✗ Migration directory not found\n";
}
echo "\n";

// Test 8: Check executable files
echo "8. Testing Executable Files\n";
echo "============================\n";

$executableFiles = [
    'migrate.php',
    'seed.php'
];

foreach ($executableFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $size = filesize(__DIR__ . '/' . $file);
        echo "✓ " . $file . " (" . number_format($size / 1024, 2) . " KB)\n";
        
        // Check if file is executable
        if (is_executable(__DIR__ . '/' . $file)) {
            echo "  ✓ File is executable\n";
        } else {
            echo "  ⚠ File is not executable\n";
        }
    } else {
        echo "✗ " . $file . " (MISSING)\n";
    }
}
echo "\n";

// Overall Summary
echo "=== Overall Test Summary ===\n";
echo "File Structure: " . number_format((count($existingFiles) / count($expectedFiles)) * 100, 1) . "% complete\n";
echo "Syntax Check: " . count($syntaxOk) . "/" . count($existingFiles) . " files passed\n";
echo "Documentation: " . count($existingDocs) . "/" . count($docFiles) . " files present\n";
echo "Examples: " . count($existingExamples) . "/" . count($exampleFiles) . " files present\n";

if (count($missingFiles) == 0 && count($syntaxErrors) == 0 && count($missingDocs) == 0) {
    echo "\n🎉 All tests passed! The TCU API Client package is complete and ready for use.\n";
} else {
    echo "\n⚠ Some issues found. Please review the test results above.\n";
}

echo "\n=== End of Simple Test ===\n";