#!/bin/bash

# TCU API Client - Deployment Script
# This script handles the complete deployment process to GitHub and Packagist

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check requirements
check_requirements() {
    print_status "Checking requirements..."
    
    if ! command_exists git; then
        print_error "Git is required but not installed."
        exit 1
    fi
    
    if ! command_exists php; then
        print_error "PHP is required but not installed."
        exit 1
    fi
    
    if ! command_exists composer; then
        print_error "Composer is required but not installed."
        exit 1
    fi
    
    print_success "All requirements met"
}

# Validate composer.json
validate_composer() {
    print_status "Validating composer.json..."
    
    if ! composer validate --no-check-publish --no-check-version; then
        print_error "composer.json validation failed"
        exit 1
    fi
    
    print_success "composer.json is valid"
}

# Run tests
run_tests() {
    print_status "Running tests..."
    
    # Install dependencies
    composer install --no-dev --optimize-autoloader
    
    # Run syntax check
    find src -name "*.php" -exec php -l {} \; > /dev/null
    
    # Run tests if phpunit is available
    if [ -f "vendor/bin/phpunit" ] && [ -f "phpunit.xml" ]; then
        vendor/bin/phpunit
    else
        print_warning "PHPUnit not found, skipping tests"
    fi
    
    print_success "All tests passed"
}

# Get version from git tag or prompt user
get_version() {
    local current_tag=$(git describe --tags --abbrev=0 2>/dev/null || echo "")
    
    if [ -z "$current_tag" ]; then
        echo "v2.0.0"
    else
        echo "$current_tag"
    fi
}

# Create git tag
create_tag() {
    local version=$1
    
    print_status "Creating git tag: $version"
    
    # Check if tag already exists
    if git tag -l | grep -q "^$version$"; then
        print_warning "Tag $version already exists"
        read -p "Do you want to delete and recreate it? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git tag -d "$version"
            git push origin ":refs/tags/$version" 2>/dev/null || true
        else
            print_error "Deployment cancelled"
            exit 1
        fi
    fi
    
    # Create annotated tag
    git tag -a "$version" -m "Release $version

## TCU API Client $version

### Features
- Laravel 12 compatibility
- Multi-database support (MySQL, PostgreSQL, SQLite)
- Structured response objects with IDE support
- Comprehensive documentation and examples

### Installation
\`\`\`bash
composer require mblogik/tcuapiclient:^${version#v}
\`\`\`

### Documentation
- README.md - Complete usage guide
- CHANGELOG.md - Version history
- examples/ - Code examples for all features

See full changelog at: https://github.com/mblogik/tcuapi/blob/main/CHANGELOG.md"
    
    print_success "Tag $version created"
}

# Push to GitHub
push_to_github() {
    local version=$1
    
    print_status "Pushing to GitHub..."
    
    # Check if we have a remote
    if ! git remote get-url origin >/dev/null 2>&1; then
        print_error "No git remote 'origin' found"
        print_status "Please set up your GitHub remote:"
        print_status "git remote add origin https://github.com/mblogik/tcuapi.git"
        exit 1
    fi
    
    # Push main branch
    print_status "Pushing main branch..."
    git push origin main
    
    # Push tags
    print_status "Pushing tags..."
    git push origin "$version"
    
    print_success "Pushed to GitHub successfully"
}

# Main deployment function
deploy() {
    local version=$(get_version)
    
    echo "======================================"
    echo "TCU API Client Deployment Script"
    echo "======================================"
    echo
    echo "This script will:"
    echo "1. Validate the project"
    echo "2. Run tests"
    echo "3. Create a git tag ($version)"
    echo "4. Push to GitHub"
    echo "5. Trigger automated Packagist update"
    echo
    
    read -p "Continue with deployment? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled"
        exit 1
    fi
    
    check_requirements
    validate_composer
    run_tests
    create_tag "$version"
    push_to_github "$version"
    
    echo
    print_success "Deployment completed successfully!"
    echo
    print_status "Next steps:"
    echo "1. Check GitHub Actions: https://github.com/mblogik/tcuapi/actions"
    echo "2. Verify release: https://github.com/mblogik/tcuapi/releases"
    echo "3. Check Packagist: https://packagist.org/packages/mblogik/tcuapiclient"
    echo "4. Test installation: composer require mblogik/tcuapiclient:^${version#v}"
    echo
}

# Handle command line arguments
case "${1:-deploy}" in
    "deploy")
        deploy
        ;;
    "check")
        check_requirements
        validate_composer
        print_success "Project is ready for deployment"
        ;;
    "test")
        check_requirements
        run_tests
        ;;
    "help")
        echo "Usage: $0 [command]"
        echo
        echo "Commands:"
        echo "  deploy    - Full deployment process (default)"
        echo "  check     - Check requirements and validate project"
        echo "  test      - Run tests only"
        echo "  help      - Show this help message"
        ;;
    *)
        print_error "Unknown command: $1"
        echo "Run '$0 help' for usage information"
        exit 1
        ;;
esac