# Makefile for GPSE WordPress Plugin Distribution
# Author: ACLabs
# Description: Automates building, packaging, and versioning of the GPSE Search plugin

# Variables
PLUGIN_NAME = gpse
VERSION = 1.2.5
PLUGIN_DIR = gpse
DIST_DIR = dist
BUILD_DIR = $(DIST_DIR)/$(PLUGIN_NAME)
ZIP_FILE = $(DIST_DIR)/$(PLUGIN_NAME)-$(VERSION).zip

# Color output
GREEN = \033[0;32m
YELLOW = \033[0;33m
BLUE = \033[0;34m
RED = \033[0;31m
NC = \033[0m # No Color

# Phony targets (not actual files)
.PHONY: all clean build version help lint test test-js test-php icons

# Default target
all: build

# Help target - Display usage information
help:
	@echo "$(BLUE)GPSE Plugin Makefile$(NC)"
	@echo ""
	@echo "$(GREEN)Available targets:$(NC)"
	@echo "  $(YELLOW)make$(NC) or $(YELLOW)make build$(NC)  - Build and package the plugin"
	@echo "  $(YELLOW)make clean$(NC)             - Remove dist/ directory"
	@echo "  $(YELLOW)make version$(NC)           - Update version numbers interactively"
	@echo "  $(YELLOW)make lint$(NC)              - Run JS and CSS linters (mirrors CI lint job)"
	@echo "  $(YELLOW)make test-js$(NC)           - Run Jest unit tests (mirrors CI test-js job)"
	@echo "  $(YELLOW)make test-php$(NC)          - Run PHPUnit via wp-env (mirrors CI test-php job)"
	@echo "  $(YELLOW)make test$(NC)              - Run all tests (test-js + test-php)"
	@echo "  $(YELLOW)make icons$(NC)             - Export icon SVG to 128×128 and 256×256 PNG (requires librsvg)"
	@echo "  $(YELLOW)make help$(NC)              - Show this help message"
	@echo ""
	@echo "$(GREEN)Current Configuration:$(NC)"
	@echo "  Plugin Name: $(PLUGIN_NAME)"
	@echo "  Version:     $(VERSION)"
	@echo "  Output:      $(ZIP_FILE)"

# Clean target - Remove distribution directory
clean:
	@echo "$(YELLOW)Cleaning distribution directory...$(NC)"
	@rm -rf $(DIST_DIR)
	@echo "$(GREEN)✓ Clean complete$(NC)"

# Build target - Compile assets and create distribution
build: clean
	@echo "$(BLUE)Building GPSE Plugin v$(VERSION)$(NC)"
	@echo ""

	# Check if npm is installed
	@if ! command -v npm >/dev/null 2>&1; then \
		echo "$(RED)✗ Error: npm is not installed$(NC)"; \
		exit 1; \
	fi

	# Install dependencies if node_modules doesn't exist
	@if [ ! -d "node_modules" ]; then \
		echo "$(YELLOW)Installing npm dependencies...$(NC)"; \
		npm install; \
		echo ""; \
	fi

	# Compile assets (src/ → build/)
	@echo "$(YELLOW)Compiling assets...$(NC)"
	@npm run build
	@if [ $$? -ne 0 ]; then \
		echo "$(RED)✗ Build failed$(NC)"; \
		exit 1; \
	fi
	@echo "$(GREEN)✓ Assets compiled$(NC)"
	@echo ""

	# Create distribution directory structure
	@echo "$(YELLOW)Creating distribution structure...$(NC)"
	@mkdir -p $(BUILD_DIR)

	# Copy files to distribution directory (excluding dev files)
	@echo "$(YELLOW)Copying plugin files...$(NC)"
	@rsync -av \
		--exclude='src/' \
		--exclude='node_modules/' \
		--exclude='.git*' \
		--exclude='package*.json' \
		--exclude='*.md' \
		--exclude='.DS_Store' \
		--exclude='dist/' \
		--exclude='*.zip' \
		--exclude='*.sha256' \
		--exclude='plan-*.md' \
		$(PLUGIN_DIR)/ $(BUILD_DIR)/
	@echo "$(GREEN)✓ Files copied$(NC)"
	@echo ""

	# Create zip archive
	@echo "$(YELLOW)Creating zip archive...$(NC)"
	@cd $(DIST_DIR) && zip -r $(PLUGIN_NAME)-$(VERSION).zip $(PLUGIN_NAME) -q
	@if [ $$? -ne 0 ]; then \
		echo "$(RED)✗ Zip creation failed$(NC)"; \
		exit 1; \
	fi
	@echo "$(GREEN)✓ Zip created$(NC)"
	@echo ""

	# Display success message with file size
	@echo "$(GREEN)═══════════════════════════════════════$(NC)"
	@echo "$(GREEN)✓ Build complete!$(NC)"
	@echo "$(GREEN)═══════════════════════════════════════$(NC)"
	@echo ""
	@echo "$(BLUE)Distribution:$(NC) $(ZIP_FILE)"
	@ls -lh $(ZIP_FILE) | awk '{print "$(BLUE)Size:$(NC)         " $$5}'
	@echo ""
	@echo "$(YELLOW)To test the plugin:$(NC)"
	@echo "  1. Upload $(ZIP_FILE) to WordPress"
	@echo "  2. Or extract to wp-content/plugins/"

# Lint target - Run JS and CSS linters (mirrors CI lint job)
lint:
	@echo "$(BLUE)Running linters$(NC)"
	@echo ""
	@if [ ! -d "node_modules" ]; then \
		echo "$(YELLOW)Installing npm dependencies...$(NC)"; \
		npm install; \
		echo ""; \
	fi
	@echo "$(YELLOW)Linting JS...$(NC)"
	@npm run lint:js
	@echo "$(GREEN)✓ JS lint passed$(NC)"
	@echo ""
	@echo "$(YELLOW)Linting CSS...$(NC)"
	@npm run lint:css
	@echo "$(GREEN)✓ CSS lint passed$(NC)"
	@echo ""
	@echo "$(GREEN)✓ All linters passed$(NC)"

# test-js target - Run Jest unit tests (mirrors CI test-js job)
test-js:
	@echo "$(BLUE)Running JS tests$(NC)"
	@echo ""
	@if [ ! -d "node_modules" ]; then \
		echo "$(YELLOW)Installing npm dependencies...$(NC)"; \
		npm install; \
		echo ""; \
	fi
	@npm run test:unit
	@echo "$(GREEN)✓ JS tests passed$(NC)"

# test-php target - Run PHPUnit in Docker (mirrors CI test-php job)
# Builds a PHP 8.2 container with all test dependencies pre-installed.
# WP test library is cached in a named Docker volume between runs.
test-php:
	@echo "$(BLUE)Running PHP tests$(NC)"
	@echo ""
	@if ! command -v docker >/dev/null 2>&1; then \
		echo "$(RED)✗ Error: Docker is required for PHP tests$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)Building test container (first run may take a minute)...$(NC)"
	@docker compose -f docker-compose.test.yml build phpunit
	@echo ""
	@echo "$(YELLOW)Running PHPUnit...$(NC)"
	@docker compose -f docker-compose.test.yml run --rm phpunit
	@docker compose -f docker-compose.test.yml down
	@echo "$(GREEN)✓ PHP tests passed$(NC)"

# test target - Run all tests
test: test-js test-php

# icons target - Export SVG icon to PNG at required WordPress.org sizes
icons:
	@echo "$(BLUE)Exporting plugin icons$(NC)"
	@echo ""
	@if ! command -v rsvg-convert >/dev/null 2>&1; then \
		echo "$(RED)✗ Error: rsvg-convert is not installed$(NC)"; \
		echo "$(YELLOW)Install it with: brew install librsvg$(NC)"; \
		exit 1; \
	fi
	@rsvg-convert -w 128 -h 128 $(PLUGIN_DIR)/assets/icon.svg -o $(PLUGIN_DIR)/assets/icon-128x128.png
	@echo "$(GREEN)✓ icon-128x128.png$(NC)"
	@rsvg-convert -w 256 -h 256 $(PLUGIN_DIR)/assets/icon.svg -o $(PLUGIN_DIR)/assets/icon-256x256.png
	@echo "$(GREEN)✓ icon-256x256.png$(NC)"
	@echo ""
	@echo "$(GREEN)✓ Icons exported$(NC)"

# Version target - Update version numbers across files
version:
	@echo "$(BLUE)Update GPSE Plugin Version$(NC)"
	@echo ""
	@echo "$(YELLOW)Current version: $(VERSION)$(NC)"
	@echo ""
	@read -p "Enter new version (e.g., 1.2.2): " NEW_VERSION; \
	if [ -z "$$NEW_VERSION" ]; then \
		echo "$(RED)✗ Version cannot be empty$(NC)"; \
		exit 1; \
	fi; \
	if ! echo "$$NEW_VERSION" | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+$$'; then \
		echo "$(RED)✗ Invalid version format. Use X.Y.Z (e.g., 1.2.2)$(NC)"; \
		exit 1; \
	fi; \
	echo ""; \
	echo "$(YELLOW)Updating version to $$NEW_VERSION...$(NC)"; \
	sed -i.bak "s/^\( \* Version: \).*/\1$$NEW_VERSION/" $(PLUGIN_DIR)/gpse.php && rm $(PLUGIN_DIR)/gpse.php.bak; \
	sed -i.bak "s/define( 'GPSE_VERSION', '[^']*' );/define( 'GPSE_VERSION', '$$NEW_VERSION' );/" $(PLUGIN_DIR)/gpse.php && rm $(PLUGIN_DIR)/gpse.php.bak; \
	sed -i.bak "s/^Stable tag: .*/Stable tag: $$NEW_VERSION/" $(PLUGIN_DIR)/readme.txt && rm $(PLUGIN_DIR)/readme.txt.bak; \
	sed -i.bak "s/^VERSION = .*/VERSION = $$NEW_VERSION/" Makefile && rm Makefile.bak; \
	echo "$(GREEN)✓ Updated $(PLUGIN_DIR)/gpse.php (plugin header)$(NC)"; \
	echo "$(GREEN)✓ Updated $(PLUGIN_DIR)/gpse.php (version constant)$(NC)"; \
	echo "$(GREEN)✓ Updated $(PLUGIN_DIR)/readme.txt (stable tag)$(NC)"; \
	echo "$(GREEN)✓ Updated Makefile (VERSION variable)$(NC)"; \
	echo ""; \
	echo "$(GREEN)Version updated successfully to $$NEW_VERSION!$(NC)"; \
	echo "$(YELLOW)Don't forget to update the changelog in readme.txt$(NC)"
