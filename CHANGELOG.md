# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2025-01-09

### 🎉 Major Release - Laravel 12 Support & Structured Response Objects

### Added
- **Structured Response Objects**: All API endpoints now return typed objects with IDE support
  - `CheckStatusTcuResponse` - Applicant status with helper methods
  - `AdmittedApplicantTcuResponse` - Admitted applicant data with formatting
  - `ConfirmAdmissionTcuResponse` - Admission confirmation tracking
  - `DashboardPopulateTcuResponse` - Dashboard statistics with analysis
  - `SubmitProgrammeTcuResponse` - Programme submission with status
- **Multi-Database Support**: Full compatibility with MySQL, PostgreSQL, and SQLite
- **Laravel 12 Ready**: Compatible with Laravel 10, 11, and 12
- **Enhanced Developer Experience**: Full IDE autocompletion and type safety
- **Docker Integration**: Docker Compose examples for MySQL and PostgreSQL
- **Comprehensive Documentation**: Multi-database configuration examples
- **GitHub Actions**: CI/CD workflows for testing and automated releases
- **Packagist Auto-Update**: Automatic package publishing via GitHub webhooks

### Changed
- Updated `illuminate/database` to support `^10.0|^11.0|^12.0`
- Updated `symfony/cache` to support `^6.0|^7.0`
- Updated `phpunit/phpunit` to support `^9.0|^10.0|^11.0`
- Database configuration enhanced with driver-specific optimizations
- Migration files updated for cross-database compatibility
- README.md completely restructured with comprehensive examples

### Fixed
- Database migrations now use standardized `timestamps()` method
- PostgreSQL compatibility issues resolved
- Cross-database charset and collation handling
- Confirmation code validation made more flexible for TCU requirements

### Breaking Changes
- **API Return Types**: All resource methods now return structured objects instead of raw arrays
  - `checkStatus()` returns `CheckStatusTcuResponse` or `CheckStatusTcuResponse[]`
  - `getAdmitted()` returns `AdmittedApplicantTcuResponse[]`
  - `confirm()` returns `ConfirmAdmissionTcuResponse`
  - `populate()` returns `DashboardPopulateTcuResponse`
- **Database Configuration**: Updated structure for multi-driver support
- **Migration Format**: Timestamp columns now use Laravel's standard `timestamps()` method

### Migration Guide from v1.x to v2.x

#### 1. Update Response Object Usage
```php
// OLD (v1.x)
$response = $client->applicants()->checkStatus('S1001/0012/2018');
if ($response['StatusCode'] == 200) {
    echo $response['StatusDescription'];
}

// NEW (v2.x)
$response = $client->applicants()->checkStatus('S1001/0012/2018');
if ($response->isSuccess()) {
    echo $response->getCurrentState();
}
```

#### 2. Update Database Configuration
```php
// OLD (v1.x)
'database' => [
    'host' => 'localhost',
    'database' => 'tcu_logs',
    'username' => 'root',
    'password' => 'password'
]

// NEW (v2.x)
'database_config' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'tcu_logs',
    'username' => 'root',
    'password' => 'password',
    'charset' => 'utf8mb4'
]
```

#### 3. Re-run Migrations
```bash
# Rollback existing migrations
composer run migrate:rollback

# Run updated migrations
composer run migrate
```

### Security
- Enhanced input validation for all endpoints
- Secure database connection handling
- Proper SQL injection prevention across all database drivers

## [1.0.0] - 2025-01-09

### Added
- Initial release of TCU API Client
- Basic API endpoint support for applicant management
- MySQL database logging support
- Configuration management system
- Validation helpers for TCU data formats
- XML request/response handling
- Authentication with UsernameToken
- Comprehensive error handling
- Database migration system
- Basic documentation and examples

### Features
- Applicant status checking
- Applicant registration
- Programme submission
- Admission confirmation
- Dashboard statistics
- Transfer management
- Verification processes
- Enrollment tracking
- Graduate management
- Staff operations
- Foreign applicant support

[Unreleased]: https://github.com/mblogik/tcuapi/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/mblogik/tcuapi/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/mblogik/tcuapi/releases/tag/v1.0.0