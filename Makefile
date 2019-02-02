#!/usr/bin/make
# Makefile readme (ru): <http://linux.yaroslavl.ru/docs/prog/gnu_make_3-79_russian_manual.html>
# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

docker_bin := $(shell command -v docker 2> /dev/null)

SHELL = /bin/sh
PHP_IMAGE = composer:1.8
RUN_ARGS = --rm -v "$(shell pwd):/src:cached" -v "/etc/passwd:/etc/passwd:ro" -v "/etc/group:/etc/group:ro" \
           --workdir "/src" -u "$(shell id -u):$(shell id -g)"
RUN_INTERACTIVE ?= --tty --interactive

.PHONY : help install test shell
.SILENT : help install shell test
.DEFAULT_GOAL : help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install all php dependencies
	$(docker_bin) run $(RUN_ARGS) $(RUN_INTERACTIVE) "$(PHP_IMAGE)" composer install --no-interaction --ansi --no-suggest --prefer-dist

test: ## Execute php tests and linters
	$(docker_bin) run $(RUN_ARGS) $(RUN_INTERACTIVE) "$(PHP_IMAGE)" bash -c "composer phpstan && composer test"

shell: ## Start shell into container with php
	$(docker_bin) run $(RUN_ARGS) $(RUN_INTERACTIVE) \
	  -e "PS1=\[\033[1;32m\]🐳 \[\033[1;36m\][\u@\h] \[\033[1;34m\]\w\[\033[0;35m\] \[\033[1;36m\]# \[\033[0m\]" \
	  "$(PHP_IMAGE)" bash

clean: ## Remove all dependencies and unimportant files
	-rm -Rf ./composer.lock ./vendor
