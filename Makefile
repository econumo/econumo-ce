.PHONY: help up down sh run test install dev bundle lint

# Default target
.DEFAULT_GOAL := help

# Show available targets
help:
	@echo "Backend commands:"
	@echo "  make up           - Start application with migrations"
	@echo "  make down         - Stop application"
	@echo "  make sh           - Open shell in application container"
	@echo "  make test ARGS='...' - Run tests (recreates test DB)"
	@echo ""
	@echo "Frontend commands:"
	@echo "  make install      - Install web dependencies"
	@echo "  make dev          - Start web development server"
	@echo "  make bundle       - Bundle web for production"
	@echo "  make lint         - Run web linter"

# Start application
up:
	@if [ ! -d vendor ]; then \
		docker-compose exec -uwww-data app composer install; \
	fi
	docker-compose up -d
	docker-compose exec -uwww-data app bin/console doctrine:migrations:migrate -n

# Stop application
down:
	docker-compose down --remove-orphans

# Open shell in application container
sh:
	docker-compose exec -uwww-data app sh

# Run tests
# Usage: make test ARGS='unit'
test:
	docker-compose up -d
	-docker-compose exec -uwww-data app bin/console doctrine:database:drop --force --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:database:create --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:migration:migrate -n --env=test -vvv
	docker-compose exec -uwww-data app bin/console doctrine:fixtures:load --purge-with-truncate -n --env=test -vvv
	-docker-compose exec -uwww-data app vendor/bin/codecept run $(ARGS) --steps -v

# Install web dependencies
install:
	@echo "Installing web dependencies..."
	cd web && pnpm install

# Start web development server
dev:
	@echo "Starting web development server..."
	cd web && npm run dev

# Bundle web for production
bundle:
	@echo "Bundling web for production..."
	cd web && npm run build

# Run web linter
lint:
	@echo "Running web linter..."
	cd web && npm run lint
