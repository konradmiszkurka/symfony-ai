# ──────────────────────────────────────────────────────────
#  Symfony + Docker Makefile
# ──────────────────────────────────────────────────────────

.DEFAULT_GOAL := help
DOCKER_COMP   = docker compose
PHP           = $(DOCKER_COMP) exec php
CONSOLE       = $(PHP) php bin/console
COMPOSER      = $(PHP) composer

# ─── Docker ──────────────────────────────────────────────

.PHONY: build up down restart logs ps

build: ## Build containers
	$(DOCKER_COMP) build --pull

up: ## Start all containers
	$(DOCKER_COMP) up -d --wait

down: ## Stop all containers
	$(DOCKER_COMP) down

restart: down up ## Restart all containers

logs: ## Show container logs (follow)
	$(DOCKER_COMP) logs -f

ps: ## List running containers
	$(DOCKER_COMP) ps

# ─── PHP / Symfony ───────────────────────────────────────

.PHONY: sh console composer

sh: ## Open shell in PHP container
	$(PHP) bash

console: ## Run Symfony console (usage: make console CMD="debug:router")
	$(CONSOLE) $(CMD)

composer: ## Run Composer (usage: make composer CMD="require symfony/orm-pack")
	$(COMPOSER) $(CMD)

# ─── Database ────────────────────────────────────────────

.PHONY: db-create db-migrate db-diff db-reset

db-create: ## Create database
	$(CONSOLE) doctrine:database:create --if-not-exists

db-migrate: ## Run migrations
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

db-diff: ## Generate migration from entity changes
	$(CONSOLE) doctrine:migrations:diff

db-reset: ## Drop + create + migrate database
	$(CONSOLE) doctrine:database:drop --force --if-exists
	$(CONSOLE) doctrine:database:create
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

# ─── Quality ─────────────────────────────────────────────

.PHONY: test stan cs cs-fix lint

test: ## Run PHPUnit tests
	$(PHP) php bin/phpunit

stan: ## Run PHPStan
	$(PHP) vendor/bin/phpstan analyse

cs: ## Check coding standards (dry-run)
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix coding standards
	$(PHP) vendor/bin/php-cs-fixer fix

lint: ## Lint Twig, YAML, container
	$(CONSOLE) lint:twig templates/
	$(CONSOLE) lint:yaml config/
	$(CONSOLE) lint:container

# ─── Messenger ───────────────────────────────────────────

.PHONY: messenger

messenger: ## Consume messages
	$(CONSOLE) messenger:consume async -vv

# ─── Xdebug ──────────────────────────────────────────────

.PHONY: xdebug-on xdebug-off xdebug-profile

xdebug-on: ## Enable Xdebug (step debugging)
	XDEBUG_MODE=debug $(DOCKER_COMP) up -d php

xdebug-off: ## Disable Xdebug
	XDEBUG_MODE=off $(DOCKER_COMP) up -d php

xdebug-profile: ## Enable Xdebug profiler
	XDEBUG_MODE=profile $(DOCKER_COMP) up -d php

# ─── Cache ───────────────────────────────────────────────

.PHONY: cache-clear

cache-clear: ## Clear Symfony cache
	$(CONSOLE) cache:clear

# ─── Help ────────────────────────────────────────────────

.PHONY: help

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
