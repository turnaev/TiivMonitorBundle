SHELL=/bin/bash

C_NO           = "\033[0m" # No Color
C_BLACK        = "\033[0;30m"
C_DARK_GRAY    = "\033[1;30m"
C_RED          = "\033[0;31m"
C_LIGHT_RED    = "\033[1;31m"
C_GREEN        = "\033[0;32m"
C_LIGHT_GREEN  = "\033[1;32m"
C_BROWN        = "\033[0;33m"
C_YELLOW       = "\033[1;33m"
C_BLUE         = "\033[0;34m"
C_LIGHT_BLUE   = "\033[1;34m"
C_PURPLE       = "\033[0;35m"
C_LIGHT_PURPLE = "\033[1;35m"
C_CYAN         = "\033[0;36m"
C_LIGHT_CYAN   = "\033[1;36m"
C_LIGHT_GRAY   = "\033[0;37m"
C_WHITE        = "\033[1;37m"
MARKER_START   = "+----------------+"
MARKER_END     = "+----------------+"

.DEFAULT_GOAL:=help

##@ Helpers
.PHONY: help

help:  ## Display this help
	@awk  'BEGIN { \
	FS = ":.*##"; \
	printf "Usage:\nmake [VARS] %s<target>%s\n", $(C_CYAN), $(C_NO)} /^[a-zA-Z_-]+:.*?##/  \
	{ printf " %s %-15s%s %s\n", $(C_CYAN), $$1, $(C_NO), $$2} /^##@/ \
	{ printf "\n%s%s%s\n", $(C_WHITE), substr($$0, 5), $(C_NO)} ' $(MAKEFILE_LIST)

################################################################################

##@ doc
build-doc: ## Build doc file README.md
	markdown-pp -o README.md doc/README.mdpp

watch-doc: ## Watch and build doc file README.md
	markdown-pp -w -o README.md ./

##@ install
install: ## install
	@composer install

update: ## install
	@composer update

##@ tests
test: ## test dist
	@phpunit

test-dev: ## test phpunit.dev.xml
	@phpunit -c phpunit.dev.xml

##@ dev
dev-install: ## install
	pip install -r requirements.txt && \
	composer global require friendsofphp/php-cs-fixer

##@ fix and check
check: cs-php-check composer-check yaml-check ## fix and validate

composer-check: ## composer validate
	composer validate

yaml-check: ## yaml validate
	yamllint -c .yamllint .

cs-php-check: ## php check
	php-cs-fixer fix --ansi --verbose --diff --dry-run .

cs-php-fix: ## php fix
	php-cs-fixer fix --ansi --verbose .
