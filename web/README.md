# Econumo

An open-source budget application built with Vue.js and Quasar Framework.

Inspired by https://www.youtube.com/watch?v=ngdoUQBvAjo

## Getting Started

### Install dependencies
```shell
pnpm install
# or using Make
make install
```

### Run development server
```shell
npm run dev
# or using Make
make dev
```

### Build for production
```shell
npm run build
# or using Make
make build
```

## Technical Stack

- **Frontend Framework**: [Vue 3](https://vuejs.org/) with TypeScript
- **UI Framework**: [Quasar](https://quasar.dev/)
- **State Management**: [Pinia](https://pinia.vuejs.org/)
- **Package Manager**: [PNPM](https://pnpm.io/) version 9.0.4+
- **Node Version**: ^20 || ^18 || ^16

## Project Structure

```
econumo/
├── src/
│   ├── shared/         # Shared types & DTOs
│   ├── boot/           # Quasar boot files
│   ├── components/     # Vue components
│   ├── composables/    # Composition API utilities
│   ├── layouts/        # Layout components
│   ├── modules/        # Business logic & API client
│   ├── pages/          # Route-level components
│   ├── router/         # Vue Router configuration
│   └── stores/         # Pinia state management
├── public/             # Static assets
├── quasar.config.js    # Quasar configuration
└── package.json
```

## Available Commands

### Using Make

```shell
make help        # Show all available commands
make install     # Install dependencies
make dev         # Start development server
make build       # Build for production
make lint        # Run ESLint
make format      # Format code with Prettier
make clean       # Clean build artifacts
make clean-all   # Clean build artifacts and dependencies
```

### Using npm

```shell
npm run dev      # Start development server
npm run build    # Build for production
npm run lint     # Run ESLint
npm run format   # Format code with Prettier
```