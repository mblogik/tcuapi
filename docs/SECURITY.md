# Security Guidelines for TCU API Client

## Company Information
- **Company:** MBLogik
- **Author:** Ombeni Aidani <developer@mblogik.com>
- **Date:** 2025-01-09
- **Version:** 1.0.0

## 🚨 CRITICAL - Never Commit These Files

### ❌ Credentials and Sensitive Data
- `.env` (use `.env.example` instead)
- Any file containing real API keys or tokens
- Database passwords or connection strings
- `tcu_api_credentials.json`
- `tcu_session_tokens.txt`
- `config/database.php` (use example templates)

### ❌ Personal Development Files
- IDE configuration (`.idea/`, `.vscode/`)
- AI assistant files (`.claude/`, `.anthropic/`, `claude_*`)
- Local database files (`.sqlite`, `.db`)
- Log files (`*.log`)
- Cache directories (`cache/`, `tmp/`)
- Backup files (`*.bak`, `*.backup`)

### ❌ Generated or Temporary Files
- `vendor/` directory
- `composer.lock` (for libraries)
- Coverage reports
- Build artifacts
- PHPUnit cache files

## ✅ Safe to Commit

### ✅ Source Code
- All PHP source files in `src/`
- Test files in `tests/`
- Migration files (without sensitive data)

### ✅ Configuration Templates
- `.env.example`
- `phpunit.xml.dist`
- Configuration examples with placeholder values

### ✅ Documentation
- README files
- API documentation
- Code comments and headers

### ✅ Project Configuration
- `composer.json`
- `.gitignore`
- `.editorconfig`
- License files

## 🔒 Security Best Practices

### Environment Variables
```bash
# ✅ Good - Use environment variables
TCU_API_USERNAME=your_username
TCU_API_SECURITY_TOKEN=your_token

# ❌ Bad - Hard-coded in source
$username = 'real_username';
$token = 'real_token_value';
```

### Configuration Files
```php
// ✅ Good - Use environment variables
'username' => $_ENV['TCU_API_USERNAME'] ?? '',
'security_token' => $_ENV['TCU_API_SECURITY_TOKEN'] ?? '',

// ❌ Bad - Hard-coded values
'username' => 'actual_username',
'security_token' => 'actual_token',
```

### Database Configuration
```php
// ✅ Good - Environment-based
'database' => [
    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'username' => $_ENV['DB_USERNAME'] ?? '',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
]

// ❌ Bad - Hard-coded credentials
'database' => [
    'driver' => 'mysql',
    'host' => 'production-server.com',
    'username' => 'real_username',
    'password' => 'real_password',
]
```

## 🛡️ Pre-commit Checks

Before committing, always verify:

1. **No sensitive data in code:**
   ```bash
   grep -r "password\|token\|secret" src/ --exclude-dir=vendor
   ```

2. **Environment files are not staged:**
   ```bash
   git status | grep -E "\.env$|credentials|secrets"
   ```

3. **Run security scan:**
   ```bash
   # If git-secrets is installed
   git secrets --scan
   ```

## 🚨 What to Do If You Accidentally Commit Secrets

1. **Immediately rotate/change the compromised credentials**
2. **Remove from git history:**
   ```bash
   git filter-branch --force --index-filter \
   'git rm --cached --ignore-unmatch path/to/file' \
   --prune-empty --tag-name-filter cat -- --all
   ```
3. **Force push to update remote:**
   ```bash
   git push origin --force --all
   ```
4. **Inform team members to re-clone repository**

## 📋 Security Checklist

### Before First Commit
- [ ] Copy `.env.example` to `.env` and fill with real values
- [ ] Verify `.env` is in `.gitignore`
- [ ] Check no hard-coded credentials in source files
- [ ] Ensure database config uses environment variables
- [ ] Test with dummy/test credentials first

### Before Each Commit
- [ ] Run `git status` and verify no sensitive files are staged
- [ ] Check diff for any accidentally added credentials
- [ ] Ensure log files are not included
- [ ] Verify cache/temporary files are excluded

### Production Deployment
- [ ] Use separate production credentials
- [ ] Never deploy with debug mode enabled
- [ ] Ensure proper file permissions on server
- [ ] Use encrypted environment variable storage
- [ ] Enable database encryption for sensitive API logs

## 📞 Emergency Contacts

If you suspect a security breach:

1. **Contact:** developer@mblogik.com
2. **Immediately revoke compromised credentials**
3. **Check access logs for unauthorized usage**
4. **Update security documentation**

## 🔧 Tools and Setup

### Install git-secrets (recommended):
```bash
# Install git-secrets
git clone https://github.com/awslabs/git-secrets.git
cd git-secrets && make install

# Configure for this repository
git secrets --install
git secrets --register-aws
git secrets --add 'tcu_api_key.*[=:].*['""][^'""]+['""]'
git secrets --add 'security_token.*[=:].*['""][^'""]+['""]'
```

### Set up pre-commit hooks:
```bash
# Add to .git/hooks/pre-commit
#!/bin/sh
git secrets --pre_commit_hook -- "$@"
```

Remember: **Security is everyone's responsibility!**