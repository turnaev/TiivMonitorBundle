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

##@ dev
install: ## install
	@composer install

test: ## test
	@phpunit
test-dev: ## test
	@phpunit -c phpunit.dev.xml
