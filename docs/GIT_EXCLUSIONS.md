# Git Exclusions Guide for TCU API Client

## Company Information
- **Company:** MBLogik
- **Author:** Ombeni Aidani <developer@mblogik.com>
- **Date:** 2025-01-09
- **Version:** 1.0.0

## 🚫 Complete List of Excluded Files and Directories

### 🔐 **CRITICAL SECURITY EXCLUSIONS**

#### Credentials and Secrets
```
.env                           # Environment variables with real credentials
.env.local                     # Local environment overrides
.env.production               # Production environment config
.env.staging                  # Staging environment config
.env.testing                  # Testing environment config
config/database.php           # Database credentials
config/api_credentials.php    # API keys and tokens
src/Config/secrets.php        # Application secrets
*.key                         # Private keys
*.pem                         # SSL certificates
*.p12, *.pfx                  # Certificate bundles
api_keys.txt                  # API key files
credentials.json              # Credential files
secrets.json                  # Secret configuration
tcu_api_credentials.json      # TCU-specific credentials
tcu_session_tokens.txt        # Session token files
```

**Why excluded:** Contains sensitive data that could compromise security if exposed.

#### Database Files
```
*.sqlite, *.sqlite3, *.db     # SQLite database files
*.sql                         # Database dumps
database_backup_*             # Database backups
db_dump_*                     # Database export files
.migration_status             # Migration tracking
migration_lock                # Migration lock files
```

**Why excluded:** Contains potentially sensitive data and large files that don't belong in version control.

### 📁 **DEPENDENCY AND BUILD EXCLUSIONS**

#### Composer Dependencies
```
/vendor/                      # Composer packages
composer.lock                 # Dependency lock file (for libraries)
composer.phar                 # Composer executable
```

**Why excluded:** Dependencies should be managed via composer.json. Lock file excluded for libraries to allow flexible dependency resolution.

#### Cache and Temporary Files
```
cache/                        # Application cache
tmp/, temp/                   # Temporary directories
storage/cache/                # Framework cache
var/cache/                    # System cache
*.tmp, *.temp, *.cache        # Temporary files
.cache/                       # Hidden cache directories
opcache/, .opcache/           # PHP OPcache
```

**Why excluded:** Temporary files that are regenerated and can contain sensitive data.

### 🛠️ **DEVELOPMENT TOOL EXCLUSIONS**

#### AI Assistant Files
```
.claude/                      # Claude AI assistant files
.claude-*                     # Claude configuration files
claude_*                      # Claude-related files
.anthropic/                   # Anthropic AI files
anthropic_*                   # Anthropic configuration
.openai/                      # OpenAI assistant files
.chatgpt/                     # ChatGPT files
.copilot/                     # GitHub Copilot files
.ai-assistant/                # Generic AI assistant directories
```

**Why excluded:** AI assistant files may contain conversation history, API keys, or personal development context that shouldn't be shared.

#### IDE and Editor Files
```
.idea/                        # PhpStorm/IntelliJ
*.iml, *.iws, *.ipr          # IntelliJ project files
.vscode/                      # Visual Studio Code
*.code-workspace              # VS Code workspaces
*.sublime-project             # Sublime Text projects
*.sublime-workspace           # Sublime Text workspaces
nbproject/                    # NetBeans
.nb-gradle/                   # NetBeans Gradle
.project, .buildpath          # Eclipse
.settings/                    # Eclipse settings
*.swp, *.swo, *~             # Vim swap files
.vim/                         # Vim configuration
```

**Why excluded:** Personal development environment configurations that vary between developers.

#### Testing and Coverage
```
.phpunit.result.cache         # PHPUnit cache
phpunit.xml                   # Local PHPUnit config
/tests/coverage/              # Coverage reports
coverage/                     # Coverage output
clover.xml, coverage.xml      # Coverage reports
test_output/                  # Test artifacts
test_results/                 # Test reports
tests/fixtures/generated/     # Generated test data
```

**Why excluded:** Generated files and personal test configurations.

### 💻 **OPERATING SYSTEM EXCLUSIONS**

#### Windows
```
Thumbs.db                     # Windows thumbnail cache
ehthumbs.db                   # Enhanced thumbnails
Desktop.ini                   # Desktop configuration
$RECYCLE.BIN/                 # Recycle bin
*.cab, *.msi, *.msm, *.msp   # Windows installers
*.lnk                         # Windows shortcuts
```

#### macOS
```
.DS_Store                     # Finder metadata
.AppleDouble                  # Apple metadata
.LSOverride                   # Launch Services
Icon?                         # Custom folder icons
._*                           # Apple metadata files
.DocumentRevisions-V100       # Document versions
.fseventsd                    # File system events
.Spotlight-V100               # Spotlight index
.TemporaryItems               # Temporary items
.Trashes                      # Trash folders
.VolumeIcon.icns             # Volume icons
.AppleDB, .AppleDesktop      # Apple database files
```

#### Linux
```
*~                            # Backup files
.fuse_hidden*                 # FUSE hidden files
.directory                    # KDE directory settings
.Trash-*                      # Trash folders
.nfs*                         # NFS files
```

**Why excluded:** Operating system specific files that don't contribute to the project.

### 📝 **LOG AND REPORT EXCLUSIONS**

#### Application Logs
```
logs/, log/                   # Log directories
*.log, *.log.*               # Log files
error.log, access.log        # Common log files
debug.log                    # Debug logs
api_calls.log                # API call logs
tcu_api_*.log               # TCU-specific logs
api_requests_*.log          # Request logs
database_queries.log        # Database query logs
```

**Why excluded:** Log files can contain sensitive information and grow very large.

#### Security and Reports
```
security_scan_results/        # Security scan output
vulnerability_reports/        # Vulnerability reports
.security/                    # Security tool output
reports/                      # Generated reports
metrics/                      # Metrics output
```

**Why excluded:** Contains potentially sensitive security information.

### 🏗️ **BUILD AND DEPLOYMENT EXCLUSIONS**

#### Build Artifacts
```
build/                        # Build output
dist/                         # Distribution files
release/                      # Release packages
.build/                       # Hidden build directories
```

#### Deployment Files
```
deploy.php                    # Deployment scripts with sensitive info
deployment_config.php         # Deployment configuration
.deployment                   # Deployment metadata
.github/workflows/secrets/    # GitHub Actions secrets
.gitlab-ci-local/            # GitLab CI local files
```

**Why excluded:** Build artifacts should be generated during CI/CD, and deployment files often contain sensitive information.

### 📦 **PACKAGE MANAGER EXCLUSIONS**

#### Node.js (if used)
```
node_modules/                 # NPM packages
npm-debug.log*               # NPM debug logs
yarn-debug.log*              # Yarn debug logs
yarn-error.log*              # Yarn error logs
package-lock.json            # NPM lock file
yarn.lock                   # Yarn lock file
bower_components/            # Bower packages
```

**Why excluded:** Dependencies should be managed via package.json.

### 🧰 **TOOL-SPECIFIC EXCLUSIONS**

#### PHP Development Tools
```
.phpcs-cache                  # PHP_CodeSniffer cache
.php_cs.cache                # PHP-CS-Fixer cache
.php-cs-fixer.cache         # PHP-CS-Fixer cache
.psalm/                      # Psalm cache
psalm.xml                    # Local Psalm config
.phpstan/                    # PHPStan cache
phpstan.neon                 # Local PHPStan config
.phan/                       # Phan cache
```

#### API Testing Tools
```
*.postman_collection.json     # Postman collections with real data
*.postman_environment.json   # Postman environments with credentials
postman_tests/               # Postman test files
.insomnia/                   # Insomnia workspace
```

**Why excluded:** Tools generate cache files and may contain sensitive test data.

### 🎯 **PROJECT-SPECIFIC EXCLUSIONS**

#### Development and Testing
```
scratch/                      # Temporary development files
playground/                   # Experimental code
sandbox/                      # Testing area
examples/with_real_data/      # Examples with sensitive data
examples/production_test/     # Production test files
local_config.php             # Local development config
dev_config.php               # Development configuration
```

#### Generated Documentation
```
api_documentation/generated/  # Generated API docs
docs/generated/              # Generated documentation
apidoc/, phpdoc/             # API documentation output
```

#### Performance and Profiling
```
profiling/                    # Performance profiling data
*.prof                        # Profile files
xdebug.profiler_output_dir/  # Xdebug profiler output
```

**Why excluded:** These are temporary development files or generated content.

### 📋 **BACKUP AND ARCHIVE EXCLUSIONS**

#### Backup Files
```
*.bak                         # Backup files
*.backup                      # Backup files
*.old                         # Old versions
*_backup                      # Backup suffixes
*_old                         # Old suffixes
backup_*                      # Backup prefixes
```

#### Compressed Archives
```
*.zip                         # ZIP archives
*.tar.gz, *.tar.bz2         # TAR archives
*.rar                         # RAR archives
*.7z                          # 7-Zip archives
```

**Why excluded:** Backup files and archives don't belong in version control.

## ✅ **What SHOULD Be Committed**

### Essential Project Files
- `composer.json` (dependency definition)
- Source code in `src/`
- Tests in `tests/`
- Configuration templates (`.env.example`)
- Documentation (`README.md`, `*.md`)
- Git configuration (`.gitignore`, `.gitattributes`)
- Editor configuration (`.editorconfig`)
- Distribution configs (`phpunit.xml.dist`)

### Example Templates
- `.env.example` (template with placeholder values)
- `config/*.example.php` (configuration templates)
- `phpunit.xml.dist` (PHPUnit distribution config)

## 🔍 **How to Verify Exclusions**

### Check what's being tracked:
```bash
git ls-files
```

### Check for sensitive patterns:
```bash
git log -p | grep -E "(password|token|secret|key)" --color=always
```

### Verify .gitignore is working:
```bash
git status --ignored
```

### Test with dummy files:
```bash
# Create test files that should be ignored
touch .env composer.lock vendor/test logs/test.log
git status  # Should not show these files
```

## 📞 **Support and Questions**

For questions about git exclusions or security:
- **Email:** developer@mblogik.com
- **Review:** Check SECURITY.md for security guidelines

Remember: **When in doubt, exclude it!** It's better to explicitly add files later than to accidentally commit sensitive data.