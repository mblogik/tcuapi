# Deployment Guide

This guide explains how to set up automated deployment to GitHub and Packagist for the TCU API Client.

## 🚀 GitHub Setup

### 1. Repository Setup

1. **Create/Update GitHub Repository**
   ```bash
   # If you haven't already, create the repository on GitHub
   # Then configure your local repository
   git remote add origin https://github.com/mblogik/tcuapi.git
   # OR if using SSH
   git remote add origin git@github.com:mblogik/tcuapi.git
   ```

2. **Configure Branch Protection**
   - Go to `Settings > Branches`
   - Add protection rule for `main` branch
   - Enable "Require pull request reviews before merging"
   - Enable "Require status checks to pass before merging"

### 2. Secrets Configuration

Add the following secrets in `Settings > Secrets and variables > Actions`:

```
PACKAGIST_USERNAME=your_packagist_username
PACKAGIST_TOKEN=your_packagist_api_token
```

**How to get Packagist API Token:**
1. Go to https://packagist.org/profile/
2. Click "Show API Token"
3. Copy the token and add it to GitHub secrets

### 3. Enable GitHub Actions

The workflows are already configured in `.github/workflows/`. They will automatically:
- Run CI tests on push/PR
- Create releases when you push tags
- Update Packagist automatically

## 📦 Packagist Setup

### 1. Initial Package Submission

1. **Create Packagist Account**
   - Go to https://packagist.org/
   - Sign up or log in

2. **Submit Package**
   - Click "Submit Package"
   - Enter your GitHub repository URL: `https://github.com/mblogik/tcuapi`
   - Click "Check"
   - If validation passes, click "Submit"

3. **Package Configuration**
   - Set package name: `mblogik/tcuapiclient`
   - Add description: "PHP TCU API Client for communicating between a University and TCU in Tanzania"
   - Set keywords: `php`, `tcu`, `api`, `client`, `tanzania`, `university`

### 2. Auto-Update Hook

Packagist will automatically update when you push to GitHub if you:

1. **Enable GitHub Service Hook** (Recommended)
   - Go to your GitHub repository
   - Navigate to `Settings > Webhooks`
   - Add webhook with payload URL: `https://packagist.org/api/github?username=YOUR_USERNAME`
   - Set content type to `application/json`
   - Select "Just the push event"

2. **Or Configure Manual API Updates**
   - The GitHub Actions workflow will handle this automatically
   - Uses the `PACKAGIST_TOKEN` secret to trigger updates

## 🔄 Release Process

### 1. Version Tagging

```bash
# Create a new version tag
git tag -a v2.0.0 -m "Release version 2.0.0"

# Push the tag to GitHub
git push origin v2.0.0
```

### 2. Automated Release Pipeline

When you push a tag, the following happens automatically:

1. **CI Validation**
   - Tests run across multiple PHP versions
   - Code quality checks
   - Database compatibility tests

2. **GitHub Release**
   - Creates a new GitHub release
   - Generates release notes
   - Uploads assets if needed

3. **Packagist Update**
   - Notifies Packagist of the new version
   - Updates package information
   - Makes new version available via Composer

### 3. Manual Release Steps

If you need to create a release manually:

```bash
# 1. Update version in composer.json if needed
# 2. Update CHANGELOG.md
# 3. Commit changes
git add .
git commit -m "Prepare release v2.0.0"

# 4. Create and push tag
git tag -a v2.0.0 -m "Release version 2.0.0"
git push origin main
git push origin v2.0.0
```

## 🧪 Testing the Setup

### 1. Test GitHub Actions

```bash
# Push a commit to trigger CI
git add .
git commit -m "Test CI workflow"
git push origin main

# Check workflow status at:
# https://github.com/mblogik/tcuapi/actions
```

### 2. Test Release Process

```bash
# Create a test release
git tag -a v2.0.0-beta -m "Beta release for testing"
git push origin v2.0.0-beta

# Check that:
# - GitHub release is created
# - Packagist is updated
# - New version is available
```

### 3. Test Packagist Installation

```bash
# Create a test project
composer create-project --no-install test-project
cd test-project

# Add your package
composer require mblogik/tcuapiclient:^2.0.0

# Verify installation
composer show mblogik/tcuapiclient
```

## 🔧 Troubleshooting

### Common Issues

1. **Authentication Errors**
   ```bash
   # Check GitHub remote configuration
   git remote -v
   
   # Update remote URL if needed
   git remote set-url origin https://github.com/mblogik/tcuapi.git
   ```

2. **Packagist Not Updating**
   - Check webhook configuration
   - Verify API token permissions
   - Manually trigger update: `curl -XPOST https://packagist.org/api/update-package?username=USERNAME&apiToken=TOKEN -d'{"repository":{"url":"https://github.com/mblogik/tcuapi"}}'`

3. **GitHub Actions Failing**
   - Check workflow logs in GitHub Actions tab
   - Verify secrets are configured
   - Check PHP version compatibility

### Debug Commands

```bash
# Check current tags
git tag -l

# Check remote branches
git branch -r

# Check GitHub Actions status
gh workflow list  # requires GitHub CLI

# Test Packagist API
curl -s "https://packagist.org/packages/mblogik/tcuapiclient.json" | jq '.'
```

## 📋 Pre-Release Checklist

Before creating a new release:

- [ ] Update version in `composer.json`
- [ ] Update `CHANGELOG.md`
- [ ] Run tests locally: `composer test`
- [ ] Check code style: `composer cs-check`
- [ ] Update documentation if needed
- [ ] Verify examples work with new version
- [ ] Test with different PHP versions
- [ ] Test with different databases
- [ ] Create comprehensive release notes

## 🚦 CI/CD Pipeline Status

The following workflows are configured:

- **CI**: Runs on every push/PR
- **Release**: Runs on tag creation
- **Packagist Update**: Runs on main branch push

Monitor status at: https://github.com/mblogik/tcuapi/actions

## 📞 Support

If you encounter issues with deployment:

1. Check the GitHub Actions logs
2. Review Packagist package status
3. Verify webhook configurations
4. Check this documentation for updates

For package-specific issues, create an issue at: https://github.com/mblogik/tcuapi/issues