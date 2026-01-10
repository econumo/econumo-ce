# AGENTS.md

This file provides guidance to LLMs when working with code in this repository.

## Overview

Econumo backend is a personal finance management API built with PHP 8.2, Symfony 5.4, and Doctrine ORM. The project supports both SQLite and PostgreSQL databases and follows Domain-Driven Design (DDD) principles with a clean architecture approach.

## Development Setup

### Running the Application

The project uses [Task](https://taskfile.dev) for common operations:

```bash
task up          # Start application (includes composer install and migrations)
task restart     # Restart application
task rebuild     # Rebuild containers and reinstall dependencies
task down        # Stop application
task sh          # Enter application container
```

The application runs in Docker and is accessible at `http://localhost:8082`.

### Database Configuration

The project supports both SQLite and PostgreSQL. By default, SQLite is configured.

#### Switching Databases

```bash
./bin/switch-database sqlite       # Switch to SQLite
./bin/switch-database postgresql   # Switch to PostgreSQL
task restart                       # Apply changes
```

Alternatively, manually set `DATABASE_URL` in `.env.local`:
- For SQLite: `DATABASE_URL=${SQLITE_DATABASE_URL}`
- For PostgreSQL: `DATABASE_URL=${POSTGRES_DATABASE_URL}`

PostgreSQL is available via Docker Compose and runs on `localhost:5432`.

#### Database Migrations

```bash
task run -- doctrine:migrations:migrate -n        # Run migrations
task run -- doctrine:database:drop --force        # Drop database
task run -- doctrine:database:create              # Create database
task run -- doctrine:fixtures:load --purge-with-truncate -n  # Load fixtures
```

### Running Tests

```bash
task test                  # Run all tests (recreates database)
task test -- unit          # Run only unit tests
task test -- api           # Run only API tests
task test -- functional    # Run only functional tests
task fast-test            # Run tests without recreating database
task test-failed          # Run only failed tests
```

Tests use Codeception framework with suites: `unit`, `api`, `functional`, and `integrational`.

### Other Commands

```bash
task run -- [symfony-command]         # Run any Symfony console command
composer install                      # Install dependencies
vendor/bin/rector process            # Run Rector for code refactoring
vendor/bin/psalm                     # Run Psalm for static analysis
```

### Generate JWT Token for API Testing

```bash
bin/console lexik:jwt:generate-token john@econumo.test
```

Access Swagger documentation at `/api/doc`.

## Architecture

### Project Structure

The codebase is organized into three main bundles under `src/`:

- **EconumoBundle** - Core application logic (main bundle)
- **EconumoCloudBundle** - Cloud-specific features

### Clean Architecture Layers (EconumoBundle)

The project follows DDD with strict layer separation:

#### 1. Domain Layer (`src/EconumoBundle/Domain/`)
The core business logic layer, framework-agnostic.

- **Entities** (`Domain/Entity/`) - Core domain models (Account, Budget, Transaction, Category, Tag, Payee, Currency, etc.)
- **Value Objects** (`Domain/Entity/ValueObject/`) - Immutable domain values (TagName, FolderName, AccountName, etc.)
- **Repositories** (`Domain/Repository/`) - Interfaces for data access
- **Services** (`Domain/Service/`) - Domain business logic
- **Factories** (`Domain/Factory/`) - Entity creation
- **Events** (`Domain/Events/`) - Domain events
- **Exceptions** (`Domain/Exception/`) - Domain-specific exceptions

#### 2. Application Layer (`src/EconumoBundle/Application/`)
Orchestrates use cases and application logic.

Each module (Account, Budget, Transaction, Category, Tag, Payee, User, Connection, Currency, System) contains:
- **Services** - Application services coordinating domain logic
- **Dto/** - Data Transfer Objects for request/response
- **Assembler/** - Converts between domain entities and DTOs

Key modules:
- `Account/` - Account and folder management
- `Budget/` - Budget, envelopes, and budget folders
- `Transaction/` - Financial transactions
- `Category/` - Transaction categorization
- `Tag/` - Transaction tagging
- `Payee/` - Payee management
- `User/` - User management and settings
- `Connection/` - User connections and account sharing
- `Currency/` - Currency and exchange rates
- `System/` - System-level operations

#### 3. Infrastructure Layer (`src/EconumoBundle/Infrastructure/`)
Framework and external dependencies.

- **Doctrine/** - ORM implementation, repositories, custom types
  - `Repository/` - Concrete repository implementations
  - `Type/` - Custom Doctrine types for value objects (UuidType, AccountNameType, TagNameType, etc.)
  - `Entity/mapping/` - XML entity mappings
- **Auth/** - JWT authentication (Lexik JWT bundle)
- **Symfony/** - Symfony-specific implementations
- **Datetime/** - Timezone handling

#### 4. UI Layer (`src/EconumoBundle/UI/`)
Presentation layer and HTTP handling.

- **Controller/Api/** - REST API controllers organized by module:
  - `Account/`, `Budget/`, `Transaction/`, `Category/`, `Tag/`, `Payee/`, `User/`, `Connection/`, `Currency/`, `System/`
  - Each controller folder contains validation forms (`Validation/`)
- **Middleware/** - HTTP middleware (exception handling, API protection)
- **Service/** - UI-specific services

### Key Architectural Patterns

1. **Value Objects** - Domain primitives are wrapped in value objects (e.g., `TagName`, `FolderName`, `AccountName`) with custom Doctrine types for persistence

2. **Repository Pattern** - Domain repository interfaces in `Domain/Repository/` with Doctrine implementations in `Infrastructure/Doctrine/Repository/`

3. **Factory Pattern** - Entity creation through factory interfaces

4. **DTO Pattern** - Request/Response DTOs in Application layer, assembled by dedicated Assembler classes

5. **Service Layer** - Application services coordinate domain logic and are injected into controllers

6. **Custom Doctrine Types** - Extensive use of custom types for value objects (see `config/packages/doctrine.yaml`)

### Dependency Flow

```
UI Layer → Application Layer → Domain Layer
        ↓
Infrastructure Layer (implements Domain interfaces)
```

Dependencies only flow inward. Domain layer has no dependencies on outer layers.

## Code Quality Tools

- **Rector** (`rector.php`) - Automated refactoring, targets PHP 8.2 and Symfony 5.4
- **Psalm** (`psalm.xml`) - Static analysis
- **Codeception** - Testing framework
- **Coding Standards** - Rector enforces code quality, coding style, and type declarations

## Important Notes

- **Database**: Supports both SQLite and PostgreSQL
  - SQLite: Default, with custom busy timeout configuration
  - PostgreSQL: Available via Docker Compose (PostgreSQL 17)
  - Migrations are database-agnostic and work with both platforms
  - SQLite-specific commands (WAL mode) only work with SQLite
- **Doctrine Mappings**: Domain entities use XML mapping files, not annotations
- **Symfony Version**: Project is locked to Symfony 5.4.*
- **PHP Version**: Requires PHP 8.2+
- **Authentication**: JWT-based using Lexik JWT Authentication Bundle
- **API Documentation**: Swagger/OpenAPI via Nelmio API Doc Bundle
- **Environment Variables**: Configuration via `.env` files (see `.env` for defaults, override in `.env.local`)

## Testing Best Practices

- Unit tests should test domain logic in isolation
- API tests should test full HTTP request/response cycle
- Functional tests should test application service layer
- Database is automatically recreated for full test runs
- Use `task fast-test` during development to skip database recreation
