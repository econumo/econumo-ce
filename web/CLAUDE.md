# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Econumo is an open-source budget application built with Vue.js and the Quasar Framework. The project is a single-page application (SPA) with TypeScript throughout.

## Technology Stack

- **Frontend**: Quasar Framework (Vue 3) + TypeScript
- **State Management**: Pinia
- **Package Manager**: PNPM (version 9.0.4+)

## Development Commands

### Running the Application

```bash
# Development server
npm run dev

# Production build
npm run build

# Linting
npm run lint

# Formatting
npm run format
```

### Package Management

```bash
# Install dependencies
pnpm install

# Add a dependency
pnpm add PACKAGE_NAME
```

## Code Style Guidelines

The project follows specific conventions defined in `.cursorrules`:

1. **Default Framework**: Use Vue.js (Vue 3) with TypeScript and Quasar components
2. **TypeScript**: Leverage static typing to minimize runtime errors
3. **Vue Composition API**: Always use the Composition API (not Options API) for new components
4. **Component Structure**: Use `<script setup>` syntax with TypeScript

## Architecture

### Project Structure

```
econumo/
├── src/
│   ├── shared/              # Shared types & DTOs
│   ├── boot/                # Quasar boot files
│   ├── components/          # Vue components
│   ├── composables/         # Composition API utilities
│   ├── layouts/             # Layout components
│   ├── modules/             # Business logic & API client
│   ├── pages/               # Route-level components
│   ├── router/              # Vue Router configuration
│   └── stores/              # Pinia state management
├── public/                  # Static assets
├── quasar.config.js         # Quasar configuration
└── package.json
```

### Application Architecture (src/)

The application follows a modular architecture:

- **stores/** - Pinia state management stores for domain entities
  - Key stores: `accounts.ts`, `budgets.ts`, `categories.ts`, `transactions.ts`, `users.ts`, `sync.ts`
  - Uses `@vueuse/core` for localStorage persistence (via `useLocalStorage`)
  - Each store follows a composition-style pattern using `defineStore`

- **pages/** - Route-level Vue components
  - Main pages: `Account.vue`, `Budget/`, `Home.vue`, `Login.vue`, `Onboarding.vue`
  - Settings pages under `Settings/` subdirectory

- **components/** - Reusable Vue components
  - Organized by feature (e.g., `Budget/`, `Categories/`, `Calculator/`, `Connections/`)
  - Modal components follow naming pattern `*Modal.vue`

- **composables/** - Composition API reusable logic
  - Domain composables: `useAccount.ts`, `useCurrency.ts`, `useMoney.ts`, `useValidation.ts`
  - Utility composables: `useDecimalNumber.ts`, `useLongPress.ts`, `useAvatar.ts`
  - Feature-specific composables organized in subdirectories (e.g., `categories/`, `connections/`)

- **modules/** - Business logic and utilities
  - `api/` - API client and v1 endpoints
  - `config.ts` - Application configuration
  - `metrics.ts` - Analytics/telemetry
  - `storage.ts` - Storage key constants
  - `icons.ts` - Icon definitions
  - `helpers/` - Utility functions

- **router/** - Vue Router configuration
  - `routes.ts` defines all application routes
  - Uses route-level meta for authentication (`requireAuth`)
  - Two main layouts: `LoginLayout` and `ApplicationLayout`

- **mixins/** - Legacy Vue mixins (being migrated to composables)
  - Note: Prefer using composables over mixins for new code

- **boot/** - Quasar boot files
  - `pinia.ts`, `i18n.ts`, `axios.ts`

### Shared Code

Shared types and DTOs are located in `src/shared/` and accessible via TypeScript path alias:

```typescript
// Import shared types:
import { AccountDto } from '@shared/dto/account.dto';
import { Id, DateString, Icon } from '@shared/types';
```

This is configured in `tsconfig.json`:
```json
"@shared/*": ["src/shared/*"]
```

### API Integration

- API base URL: `https://api.econumo.com` (configured in `src/boot/axios.ts`)
- API client pattern: `src/modules/api/v1/`
- All API responses are typed using DTOs from `src/shared/dto/`

### State Management Pattern

Stores use a specific pattern:
1. Persistent state via `useLocalStorage` from `@vueuse/core`
2. Computed properties for derived state
3. Actions for mutations and API calls
4. Timestamps for tracking data freshness (e.g., `accountsLoadedAt`)

Example pattern:
```typescript
export const useExampleStore = defineStore('example', () => {
  const items = useLocalStorage(StorageKeys.ITEMS, []);
  const itemsLoadedAt = useLocalStorage(StorageKeys.ITEMS_LOADED_AT, null);

  const isLoaded = computed(() => !!itemsLoadedAt.value);

  function fetchItems() { /* ... */ }

  return { items, isLoaded, fetchItems };
});
```

### TypeScript Path Aliases

The following path aliases are configured for imports:

```typescript
components/*  → src/components/*
layouts/*     → src/layouts/*
pages/*       → src/pages/*
mixins/*      → src/mixins/*
modules/*     → src/modules/*
stores/*      → src/stores/*
@shared/*     → src/shared/*
```

### Component Patterns

- Use TypeScript `setup` syntax (composition API)
- Import composables for reusable logic
- Leverage Quasar components for UI
- Follow the existing modal pattern for dialogs (see `*Modal.vue` components)

## Key Domain Concepts

The application models personal finance with these core entities:

- **Accounts** - Financial accounts (checking, savings, credit cards, etc.)
- **Account Folders** - Organization for grouping accounts
- **Budgets** - Budget envelopes for expense tracking
- **Categories** - Transaction categories (income/expense)
- **Transactions** - Financial transactions
- **Payees** - Transaction recipients/payers
- **Tags** - Labels for organizing transactions
- **Currencies** - Multi-currency support
- **Connections** - Account sharing/collaboration
- **Users** - User management and authentication

## Important Notes

1. **Quasar SPA Mode**: The application runs in SPA mode (`--mode=spa`)
2. **Node Version**: Requires Node ^20 || ^18 || ^16
3. **Package Manager**: Uses PNPM version 9.0.4+
4. **Boot Files**: Application initialization happens via Quasar boot files (pinia, i18n, axios)
5. **Localization**: The app uses vue-i18n for internationalization
