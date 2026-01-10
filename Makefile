.PHONY: up restart rebuild down sh root run fast-test test-failed test composer-install help install dev build lint format clean clean-all

# Default target
.DEFAULT_GOAL := help

# Default target - show help
help:
	@echo "Available targets:"
	@echo "  make up           - Run application"
	@echo "  make restart      - Restart application"
	@echo "  make rebuild      - Rebuild application"
	@echo "  make down         - Stop application"
	@echo "  make sh           - Jump into application container"
	@echo "  make root         - Jump into application container as ROOT"
	@echo "  make run ARGS='...' - Run symfony command"
	@echo "  make fast-test ARGS='...' - Run tests without recreating db"
	@echo "  make test-failed ARGS='...' - Run only failed tests"
	@echo "  make test ARGS='...' - Run tests"
	@echo "  make composer-install - Install composer dependencies"
	@echo "  make install      - Install web dependencies"
	@echo "  make dev          - Start web development server"
	@echo "  make build        - Build web for production"
	@echo "  make lint         - Run web lint"
	@echo "  make format       - Format web code"
	@echo "  make clean        - Clean web build artifacts"
	@echo "  make clean-all    - Clean web artifacts and dependencies"

# Run application
up: composer-install
	docker-compose up -d
	docker-compose exec -uwww-data app bin/console doctrine:migrations:migrate -n

# Restart application
restart: down up

# Rebuild application
rebuild:
	docker-compose up -d --build --remove-orphans
	docker-compose exec -uwww-data app composer install
	docker-compose exec -uwww-data app bin/console doctrine:migrations:migrate -n

# Stop application
down:
	docker-compose down --remove-orphans

# Jump into application container
sh:
	docker-compose exec -uwww-data app sh

# Jump into application container as ROOT
root:
	docker-compose exec app sh

# Run symfony command
# Usage: make run ARGS='doctrine:migrations:status'
run:
	docker-compose exec -uwww-data app bin/console $(ARGS)

# Run tests without recreating db
# Usage: make fast-test ARGS='unit'
fast-test:
	docker-compose up -d
	-docker-compose exec -uwww-data app vendor/bin/codecept run $(ARGS) --steps -v

# Run only failed tests
# Usage: make test-failed ARGS='unit'
test-failed:
	docker-compose up -d
	-docker-compose exec -uwww-data app vendor/bin/codecept run $(ARGS) --steps -v -g failed

# Run tests
# Usage: make test ARGS='unit'
test:
	docker-compose up -d
	-docker-compose exec -uwww-data app bin/console doctrine:database:drop --force --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:database:create --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:migration:migrate -n --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:fixtures:load --purge-with-truncate -n --env=test -vvv
	-docker-compose exec -uwww-data app vendor/bin/codecept run $(ARGS) --steps -v

# Install composer dependencies
composer-install:
	@if [ ! -d vendor ]; then \
		docker-compose exec -uwww-data app composer install; \
	fi

# Install web dependencies
install:
	@echo "Installing web dependencies..."
	cd web && pnpm install

# Start web development server
dev:
	@echo "Starting web development server..."
	cd web && npm run dev

# Build web for production
build:
	@echo "Building web for production..."
	cd web && npm run build

# Run ESLint
lint:
	@echo "Running web linter..."
	cd web && npm run lint

# Format code with Prettier
format:
	@echo "Formatting web code..."
	cd web && npm run format

# Clean build artifacts
clean:
	@echo "Cleaning web build artifacts..."
	rm -rf web/dist/
	rm -rf web/.quasar/

# Clean build artifacts and dependencies
clean-all: clean
	@echo "Cleaning all web artifacts + dependencies..."
	rm -rf web/node_modules/
	rm -rf web/pnpm-lock.yaml
