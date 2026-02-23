# CLAUDE.md — Symfony Betting App

## Project Overview
Sports betting probability calculator with admin panel.
Symfony 8.0, PHP 8.4, FrankenPHP, MySQL 8.4, Redis, RabbitMQ.

## Architecture: CQRS + DDD + Facade Pattern

### Module Structure (per module, each has layers inside)
```
src/
├── User/              # Authentication, authorization, roles
├── Match/             # Sports matches, teams, leagues, results
├── Betting/           # Probability calculation, odds, predictions
└── Shared/            # Base classes, interfaces, domain events
```

### Layer Structure (inside each module)
```
Module/
├── Domain/            # Entities, Value Objects, Repository Interfaces, Domain Events
│   ├── Entity/
│   ├── ValueObject/
│   ├── Event/
│   ├── Repository/    # Interfaces only!
│   └── Exception/
├── Application/       # Commands, Queries, Handlers, Facades, DTOs
│   ├── Command/
│   ├── Query/
│   ├── Handler/
│   ├── Facade/        # Single entry point for the module
│   └── DTO/
├── Infrastructure/    # Doctrine repos, external APIs, framework glue
│   ├── Persistence/
│   ├── Api/
│   └── EventListener/
└── UI/                # Controllers, CLI commands, Forms, Templates
    ├── Controller/
    ├── Command/       # Symfony CLI commands
    └── Form/
```

### Key Rules

**Facade Pattern:**
- UI layer talks to domain ONLY through Facade
- Facade delegates to CommandBus / QueryBus
- One Facade per module (e.g., MatchFacade, BettingFacade)

**CQRS:**
- Commands: write operations, return void
- Queries: read operations, return DTO
- Each Command/Query has exactly one Handler
- Bus: Symfony Messenger (sync for queries, async for events)

**DDD:**
- Domain layer has ZERO framework dependencies
- Repository interfaces in Domain, implementations in Infrastructure
- Rich domain models — logic in entities, not in services
- Value Objects for typed data (Score, Odds, TeamName, etc.)
- Domain Events for cross-module communication via RabbitMQ

**Dependency Direction:**
```
UI → Application → Domain
         ↑
   Infrastructure
```
Infrastructure depends on Domain (implements interfaces).
Domain depends on NOTHING.

## Coding Standards

### PHP
- PHP 8.4 features: readonly classes, typed properties, named arguments, enums, match expressions
- Strict types in every file: `declare(strict_types=1);`
- Final classes by default (open for extension only when explicitly needed)
- Return types on every method
- No magic methods unless absolutely necessary

### Symfony
- Attributes for routing, DI, Doctrine mapping (never annotations or YAML)
- Constructor injection only (no setter injection)
- Thin controllers (max 10-15 lines per action), delegate to Facade
- Forms: use DTO as data_class, never entities directly
- Security: Voters for authorization, not hardcoded role checks

### Doctrine
- Attributes for mapping (not XML/YAML)
- Repository interfaces in Domain, DoctrineRepository in Infrastructure
- Always use QueryBuilder, never DQL strings
- Careful with lazy loading — use fetch joins to prevent N+1

### Naming Conventions
- Commands: `CreateMatchCommand`, `UpdateScoreCommand`
- Queries: `GetMatchByIdQuery`, `ListUpcomingMatchesQuery`
- Handlers: `CreateMatchHandler`, `GetMatchByIdHandler`
- Events: `MatchCreatedEvent`, `ScoreUpdatedEvent`
- Facades: `MatchFacade`, `BettingFacade`
- DTOs: `MatchDTO`, `CreateMatchDTO`
- Value Objects: `Score`, `Odds`, `TeamName`, `LeagueName`
- Entities: `Match`, `Team`, `League`, `Prediction`

### Tests
- PHPUnit for unit and integration tests
- Unit tests: Domain and Application layers (no DB, no framework)
- Integration tests: Infrastructure layer (with DB)
- Functional tests: UI layer (HTTP requests)
- Test location mirrors source: `tests/Match/Domain/Entity/MatchTest.php`
- Naming: `test_it_creates_match_with_valid_data()`

## Available Commands
- `make up` — start Docker containers
- `make down` — stop containers
- `make sh` — shell into PHP container
- `make test` — run PHPUnit
- `make stan` — run PHPStan
- `make cs-fix` — fix coding standards
- `make db-migrate` — run migrations
- `make db-diff` — generate migration from entity changes
- `make messenger` — consume async messages
- `make cache-clear` — clear Symfony cache

## Tech Stack
- PHP 8.4 + Symfony 8.0
- FrankenPHP (worker mode)
- MySQL 8.4 (Doctrine ORM)
- Redis 7 (cache, sessions)
- RabbitMQ 4 (Messenger async transport)
- Mailpit (local email testing)
- PHPUnit, PHPStan, PHP CS Fixer

## Sports Supported
- Football (soccer) — first implementation
- Basketball — planned next
