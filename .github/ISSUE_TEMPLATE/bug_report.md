---
name: Bug Report
about: Create a report to help us improve
title: '[BUG] '
labels: bug
assignees: ''

---

**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Configure the client with '...'
2. Call method '....'
3. See error

**Expected behavior**
A clear and concise description of what you expected to happen.

**Code Example**
```php
// Provide a minimal code example that reproduces the issue
$config = new Configuration([
    'username' => 'TEST',
    'security_token' => 'TEST_TOKEN'
]);

$client = new TCUAPIClient($config);
$response = $client->applicants()->checkStatus('S1001/0012/2018');
```

**Error Message**
```
Paste the full error message here
```

**Environment:**
- PHP Version: [e.g. 8.1.0]
- TCU API Client Version: [e.g. 2.0.0]
- Database: [e.g. MySQL 8.0, PostgreSQL 15, SQLite]
- Laravel Version: [e.g. 11.0, if applicable]
- Operating System: [e.g. Ubuntu 22.04, Windows 11]

**Additional context**
Add any other context about the problem here.

**Configuration**
```php
// Provide your configuration (without sensitive data)
$config = new Configuration([
    'username' => 'USERNAME',
    'security_token' => 'TOKEN',
    'base_url' => 'https://api.tcu.go.tz',
    'database_config' => [
        'driver' => 'mysql',
        // ... other config
    ]
]);
```