# Repository Guidelines

This file provides guidance to AI agents when working with code in this repository.

## Project Overview

Econumo is a self-hosted personal finance and budgeting application. It consists of:
- **Backend**: Symfony 5.4 (PHP 8.2+) API with hexagonal architecture
- **Frontend**: Vue 3 + Quasar 2 SPA with TypeScript
- **Database**: SQLite (default) or PostgreSQL

## Development Commands

### Backend (PHP/Symfony)

All backend commands run inside Docker containers via `docker-compose`:

```bash
# Start application (includes migrations)
make up

# Stop application
make down

# Open shell in app container
make sh

# Run tests (recreates test DB and loads fixtures)
make test                    # All tests
make test ARGS='unit'       # Unit tests only
make test ARGS='functional' # Functional tests only

# Inside container shell (docker-compose exec -uwww-data app sh):
bin/console doctrine:migrations:migrate           # Run migrations
bin/console doctrine:fixtures:load                # Load fixtures
bin/console doctrine:database:create              # Create database
bin/console cache:clear                           # Clear cache
vendor/bin/codecept run unit --steps -v          # Run unit tests with verbose output
```

### Frontend (Vue/Quasar)

Frontend commands run on the host machine in the `web/` directory:

```bash
# Install dependencies (uses pnpm)
make install
# or: cd web && pnpm install

# Start development server (SPA mode)
make dev
# or: cd web && npm run dev

# Build for production
make bundle
# or: cd web && npm run build

# Run linter
make lint
# or: cd web && npm run lint

# Format code
cd web && npm run format
```

## Architecture

### Bundle Structure

The backend is organized into three Symfony bundles:

1. **EconumoBundle** (`src/EconumoBundle/`)
   - Core application logic
   - Contains all layers: Domain, Application, Infrastructure, UI
   - Handles accounts, transactions, budgets, categories, currencies, payees, tags

2. **EconumoToolsBundle** (`src/EconumoToolsBundle/`)
   - Utility commands and maintenance tools
   - Currency rate updates (OpenExchangeRates API)
   - Database migration tools (SQLite ↔ PostgreSQL)

### Layered Architecture (Hexagonal + DDD)

The codebase follows a strict layered architecture with dependency inversion:

```
Domain (Core Business Logic)
    ↓ depends on nothing
Application (Use Cases & Orchestration)
    ↓ depends on Domain
Infrastructure (Symfony/Doctrine Implementation)
    ↓ implements Domain interfaces
UI (HTTP Controllers & API)
    ↓ uses Application services
```

**Key Layer Responsibilities:**

- **Domain** (`src/EconumoBundle/Domain/`)
  - Entity models with business logic
  - Value objects (Id, DecimalNumber, AccountName, etc.)
  - Repository interfaces (no implementation)
  - Domain services (complex business rules)
  - Domain events

- **Application** (`src/EconumoBundle/Application/`)
  - Feature-organized services (AccountService, BudgetService, etc.)
  - Request/Result DTOs with V1 suffix
  - Assemblers (convert Domain ↔ DTOs)
  - Use case handlers

- **Infrastructure** (`src/EconumoBundle/Infrastructure/`)
  - `Doctrine/`: Repository implementations, ORM mappings, migrations, custom types
  - `Symfony/`: Forms, Messenger handlers, Mailer, Translation
  - `Auth/`: JWT authentication
  - `Datetime/`: DateTime services

- **UI** (`src/EconumoBundle/UI/`)
  - `Controller/Api/`: API controllers (one action per class)
  - `Middleware/`: Exception handling, language detection, API protection
  - `Service/`: Validators, response factories

### API Controller Pattern

Controllers follow a resource-based, single-action pattern:

```php
// Pattern: {Resource}/{SubResource?}/{Action}V1Controller
// Route: /api/v1/{resource}/{action}

class CreateAccountV1Controller extends AbstractController {
    #[Route(path: '/api/v1/account/create-account', methods: ['POST'])]
    public function __invoke(Request $request): Response {
        // 1. Validate via Form classes
        $this->validator->validate(CreateAccountV1Form::class, ...);
        // 2. Call Application service
        $result = $this->accountService->createAccount($dto, $userId);
        // 3. Return standardized response
        return ResponseFactory::createOkResponse($request, $result);
    }
}
```

**Validation**: Each controller has a corresponding Form class in `Validation/` subdirectory with declarative constraints.

### Frontend Architecture (Vue 3 + Quasar)

Directory structure in `web/src/`:

- **pages/**: Route pages (file-based routing)
- **components/**: Reusable Vue components
- **composables/**: Vue 3 Composition API hooks (e.g., `useApi`)
- **modules/api/v1/**: API client services (account.ts, budget.ts, etc.)
- **stores/**: Pinia state management (accounts, budgets, transactions, etc.)
- **router/**: Vue Router configuration
- **i18n/**: Internationalization (vue-i18n)

**State Management**: Pinia stores per feature with reactive state, getters, actions.

**API Integration**: `modules/api/v1/` contains typed API clients matching backend endpoints.

## Important Patterns

### Value Objects
- All IDs are wrapped in typed value objects (`AccountId`, `UserId`, etc.)
- Numbers use `DecimalNumber` for precision
- Names/strings often have dedicated value objects with validation
- Value objects are immutable

### Repository Pattern
- Domain layer defines `*RepositoryInterface`
- Infrastructure layer implements in `Infrastructure/Doctrine/Repository/`
- Never use Doctrine directly in Domain or Application layers

### Assemblers
- Convert between Domain entities and DTOs
- Located in `Application/[Feature]/Assembler/`
- Example: `CreateAccountV1ResultAssembler` converts Account → API response

### Domain Events
- Domain publishes events (e.g., `UserRegisteredEvent`)
- Handled via Symfony Messenger
- Event handlers in `Application/UseCase/` or `Infrastructure/Symfony/Messenger/`

### Custom Doctrine Types
- Value objects have custom Doctrine types in `Infrastructure/Doctrine/Type/`
- Examples: `UuidType`, `DecimalNumberType`, `AccountTypeType`
- Allows using value objects directly in entities

## Testing

**Framework**: Codeception with PHPUnit wrapper

**Test organization** (`tests/`):
- `unit/`: Domain entity and value object tests
- `functional/api/v1/`: Functional API tests
- `integrational/`: Integration tests
- `Helper/`: Test utilities
- `_data/fixtures/`: Test data

**Running tests**:
```bash
make test                                    # Full test suite (recreates DB)
make test ARGS='unit'                       # Unit tests only
docker-compose exec -uwww-data app vendor/bin/codecept run --steps -v
```

**Test environment**:
- Uses separate test database (configured via `APP_ENV=test`)
- Fixtures loaded before each test run
- Configuration in `codeception.yml`

## Configuration

### Environment Variables

Key configuration files:
- `.env.dist`: Template with all available options
- `.env`: Local environment (not committed)
- `.env.local`: Local overrides (not committed)
- `.env.test`: Test-specific settings

**Critical settings**:
- `DATABASE_DRIVER`: `sqlite` or `postgresql`
- `DATABASE_URL`: Automatically set based on driver
- `ECONUMO_ALLOW_REGISTRATION`: Enable/disable user registration
- `ECONUMO_CONNECT_USERS`: Auto-connect new users
- `JWT_SECRET_KEY`/`JWT_PUBLIC_KEY`: JWT authentication keys
- `CORS_ALLOW_ORIGIN`: CORS configuration

### Database Support

**SQLite** (default for development):
- Database file: `var/db/db.sqlite`
- No external dependencies

**PostgreSQL** (not recommended for use, it is in beta):
- Configure via `POSTGRES_*` variables in `.env`
- Better for multi-user scenarios
- Migration tool available: `MigrateSqliteToPostgresCommand`

## Common Development Workflows

### Adding a New API Endpoint

1. Create Request/Result DTOs in `Application/{Feature}/Dto/`
2. Add method to Application Service (e.g., `AccountService`)
3. Create Assembler if needed in `Application/{Feature}/Assembler/`
4. Create Controller in `UI/Controller/Api/{Resource}/{Action}/`
5. Create Form validation class in `{Controller}/Validation/`
6. Add route annotation to controller
7. Write tests in `tests/functional/api/v1/`

### Adding a New Domain Entity

1. Create entity in `Domain/Entity/` with business logic
2. Define value objects in `Domain/Entity/ValueObject/`
3. Create repository interface in `Domain/Repository/`
4. Create custom Doctrine types for value objects in `Infrastructure/Doctrine/Type/`
5. Implement repository in `Infrastructure/Doctrine/Repository/`
6. Create ORM mapping in `Infrastructure/Doctrine/Entity/mapping/`
7. Generate migration: `bin/console doctrine:migrations:diff`
8. Write unit tests in `tests/unit/App/EconumoBundle/Domain/Entity/`

### Working with Migrations

```bash
# Generate migration from entity changes
bin/console doctrine:migrations:diff

# View migration status
bin/console doctrine:migrations:status

# Execute migrations
bin/console doctrine:migrations:migrate

# Rollback migration
bin/console doctrine:migrations:migrate prev
```

## Key Symfony Console Commands

```bash
# Cache management
bin/console cache:clear
bin/console cache:warmup

# Database
bin/console doctrine:database:create
bin/console doctrine:database:drop --force
bin/console doctrine:schema:update --dump-sql
bin/console doctrine:fixtures:load

# JWT keys
bin/console lexik:jwt:generate-keypair

# Currency updates
bin/console econumo:update-currency-rates

# User management (EconumoCloudBundle)
bin/console econumo:activate-user {user-id}
bin/console econumo:deactivate-users
```

## Code Quality Tools

- **Psalm**: Static analysis (`vendor/bin/psalm`)
- **PHPStan**: Static analysis (`vendor/bin/phpstan`)
- **Rector**: Automated refactoring (`vendor/bin/rector`)
- **Codeception**: Testing framework
- **ESLint**: Frontend linting (in `web/`)

## Authentication

- **Method**: JWT tokens via Lexik JWT Authentication Bundle
- **Keys**: RSA keypair in `config/jwt/` (generate with `lexik:jwt:generate-keypair`)
- **Endpoints**:
  - Login: `/api/v1/auth/login`
  - Register: `/api/v1/auth/register` (if `ECONUMO_ALLOW_REGISTRATION=true`)
- **Token refresh**: Not implemented (clients must re-authenticate)

## Deployment

Production deployment uses Docker Compose. See `deployment/docker-compose/` for:
- `docker-compose.yml`: Production stack definition
- `.env.example`: Production environment template

**Build process**:
1. Backend Dockerfile in separate repository: `econumo/build-configuration`
2. Frontend built via `quasar build` (generates static files)
3. Static files served by nginx
4. PHP-FPM handles backend API

**Docker setup**:
- Multi-stage build
- nginx + PHP-FPM in single container
- Entrypoint script handles migrations and initialization
- Initial startup takes ~90 seconds

## Currency Support

**Default**: USD only
**Multi-currency**: Requires currency rate updates

```bash
# Set Open Exchange Rates API token in .env
OPEN_EXCHANGE_RATES_TOKEN=your_token

# Update rates
bin/console econumo:update-currency-rates
```

**Base currency**: Set via `ECONUMO_CURRENCY_BASE` (default: USD)

## Debugging

- **Symfony Profiler**: Available in dev mode at `/_profiler`
- **Logs**: `var/log/dev.log` and `var/log/test.log`
- **Xdebug**: Configure via `XDEBUG_MODE` environment variable
- **NewRelic**: Optional APM integration via `ekino/newrelic-bundle`

## API Documentation

**Nelmio API Doc Bundle** provides OpenAPI/Swagger documentation:
- Development: `http://localhost:8181/api/doc`
- Controllers use OpenAPI annotations for documentation
- Auto-generated from controller annotations
