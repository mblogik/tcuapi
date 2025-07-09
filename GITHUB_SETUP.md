# GitHub Setup Guide

This guide will help you set up the GitHub connection and deploy your TCU API Client package.

## 🔐 Authentication Setup

### Option 1: HTTPS with Personal Access Token (Recommended)

1. **Create Personal Access Token**
   - Go to https://github.com/settings/tokens
   - Click "Generate new token (classic)"
   - Select scopes: `repo`, `workflow`, `write:packages`
   - Copy the token (save it securely)

2. **Configure Git**
   ```bash
   # Set up git credentials
   git config --global user.name "Your Name"
   git config --global user.email "your.email@example.com"
   
   # Configure remote with token
   git remote set-url origin https://YOUR_TOKEN@github.com/mblogik/tcuapi.git
   ```

### Option 2: SSH Key Setup

1. **Generate SSH Key**
   ```bash
   ssh-keygen -t ed25519 -C "your.email@example.com"
   
   # Start ssh-agent
   eval "$(ssh-agent -s)"
   
   # Add key to ssh-agent
   ssh-add ~/.ssh/id_ed25519
   ```

2. **Add to GitHub**
   - Copy public key: `cat ~/.ssh/id_ed25519.pub`
   - Go to https://github.com/settings/keys
   - Click "New SSH key"
   - Paste your public key

3. **Test Connection**
   ```bash
   ssh -T git@github.com
   ```

## 🏗️ Repository Setup

### 1. Create Repository on GitHub

1. Go to https://github.com/new
2. Repository name: `tcuapi`
3. Description: "PHP TCU API Client for communicating between a University and TCU in Tanzania"
4. Make it Public (required for free Packagist)
5. Don't initialize with README (we have one)

### 2. Connect Local Repository

```bash
# Add remote origin
git remote add origin https://github.com/mblogik/tcuapi.git

# Or if you prefer SSH
git remote add origin git@github.com:mblogik/tcuapi.git

# Verify remote
git remote -v
```

## 📦 Packagist Setup

### 1. Register on Packagist

1. Go to https://packagist.org/register
2. Create account or sign in with GitHub
3. Verify email address

### 2. Submit Package

1. Go to https://packagist.org/packages/submit
2. Enter repository URL: `https://github.com/mblogik/tcuapi`
3. Click "Check" and then "Submit"

### 3. Get API Token

1. Go to https://packagist.org/profile/
2. Click "Show API Token"
3. Copy the token for GitHub Actions

## 🔧 GitHub Actions Setup

### 1. Add Repository Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions

Add these secrets:
```
PACKAGIST_USERNAME = your_packagist_username
PACKAGIST_TOKEN = your_packagist_api_token
```

### 2. Enable Actions

- Go to Actions tab in your repository
- Enable GitHub Actions if prompted
- The workflows will be automatically available

## 🚀 Deployment Process

### Method 1: Using the Deploy Script

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh

# Or check readiness first
./deploy.sh check
```

### Method 2: Manual Deployment

```bash
# 1. Commit all changes
git add .
git commit -m "feat: Add Laravel 12 compatibility and multi-database support"

# 2. Create version tag
git tag -a v2.0.0 -m "Release version 2.0.0"

# 3. Push to GitHub
git push origin main
git push origin v2.0.0
```

## 🔍 Troubleshooting

### Common Issues

1. **Authentication Failed**
   ```bash
   # Check current remote
   git remote -v
   
   # Update remote URL with token
   git remote set-url origin https://YOUR_TOKEN@github.com/mblogik/tcuapi.git
   ```

2. **Permission Denied (publickey)**
   ```bash
   # Test SSH connection
   ssh -T git@github.com
   
   # Add SSH key to agent
   ssh-add ~/.ssh/id_ed25519
   ```

3. **Repository Not Found**
   ```bash
   # Verify repository exists and you have access
   # Check repository name and owner
   git remote set-url origin https://github.com/mblogik/tcuapi.git
   ```

### Windows-Specific Issues

If you're on Windows:

1. **Use Git Bash or WSL**
   ```bash
   # In Git Bash or WSL
   ./deploy.sh
   ```

2. **Set up SSH in Windows**
   ```bash
   # In Git Bash
   ssh-keygen -t ed25519 -C "your.email@example.com"
   eval "$(ssh-agent -s)"
   ssh-add ~/.ssh/id_ed25519
   ```

## ✅ Verification Steps

After deployment, verify:

1. **GitHub Release**
   - Go to https://github.com/mblogik/tcuapi/releases
   - Check that v2.0.0 release was created

2. **GitHub Actions**
   - Go to https://github.com/mblogik/tcuapi/actions
   - Verify workflows completed successfully

3. **Packagist Update**
   - Go to https://packagist.org/packages/mblogik/tcuapiclient
   - Check that v2.0.0 is available

4. **Test Installation**
   ```bash
   composer create-project --no-install test-project
   cd test-project
   composer require mblogik/tcuapiclient:^2.0.0
   ```

## 📋 Quick Command Reference

```bash
# Check git status
git status

# Check remote configuration
git remote -v

# List existing tags
git tag -l

# Check GitHub Actions status (requires GitHub CLI)
gh workflow list
gh run list

# Test Packagist package
curl -s "https://packagist.org/packages/mblogik/tcuapiclient.json" | jq '.package.versions | keys[]' | head -5
```

## 🆘 Getting Help

If you encounter issues:

1. Check the deployment logs in GitHub Actions
2. Verify your authentication setup
3. Check repository permissions
4. Review the error messages carefully

For package-specific issues, create an issue at:
https://github.com/mblogik/tcuapi/issues

## 🎯 Next Steps

After successful deployment:

1. **Monitor GitHub Actions** for automated CI/CD
2. **Check Packagist** for package availability
3. **Test the package** in a fresh project
4. **Update documentation** as needed
5. **Set up branch protection** rules
6. **Configure webhooks** for auto-updates

Your TCU API Client is now ready for professional distribution! 🚀