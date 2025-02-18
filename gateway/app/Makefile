# Variables for commonly used paths
PHPSTAN = tools/vendor/bin/phpstan
PHPCS_FIXER = tools/vendor/bin/php-cs-fixer

# Default target (runs all checks)
ci-check: phpstan php-cs-dry-run

# Run PHPStan code analysis
phpstan:
	@echo "Running PHPStan static analysis"
	@$(PHPSTAN) analyze

# Fix coding standards with PHP CS Fixer
php-cs-fix:
	@echo "Auto Fix coding standards with PHP CS Fixer"
	@$(PHPCS_FIXER) fix

# Dyr run possible coding standards fix using PHP CS Fixer
php-cs-dry-run:
	@echo "Checking coding standards with PHP CS Fixer in dry-run mode"
	@$(PHPCS_FIXER) fix --dry-run

# Help target to list available commands
help:
	@echo "Available commands:"
	@echo "  make phpstan          Run PHPStan static analysis to check for code issues"
	@echo "  make php-cs-fix       Automatically fix coding standards with PHP CS Fixer"
	@echo "  make php-cs-dry-run   Check coding standards without making changes (dry-run mode)"
	@echo "  make ci-check         Run all gitlab-ci checks: PHPUnit, PHPStan, and PHP CS Fixer dry-run"
