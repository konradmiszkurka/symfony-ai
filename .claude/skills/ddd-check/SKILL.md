---
name: ddd-check
description: Sprawdza czy struktura kodu jest zgodna z DDD i CQRS. Użyj gdy tworzysz nowy moduł, przenosisz kod, lub chcesz zweryfikować architekturę.
---

# DDD & CQRS Structure Validator

## Module Structure Check
Every module MUST follow this structure:
```
src/{Module}/
├── Domain/
│   ├── Entity/          # Aggregates and Entities
│   ├── ValueObject/     # Immutable typed values
│   ├── Event/           # Domain events
│   ├── Repository/      # Interfaces ONLY
│   └── Exception/       # Domain-specific exceptions
├── Application/
│   ├── Command/         # Write operations (return void)
│   ├── Query/           # Read operations (return DTO)
│   ├── Handler/         # One handler per command/query
│   ├── Facade/          # Single entry point for the module
│   └── DTO/             # Data transfer objects
├── Infrastructure/
│   ├── Persistence/     # Doctrine repositories (implements Domain interfaces)
│   ├── Api/             # External API clients
│   └── EventListener/   # Symfony event listeners
└── UI/
    ├── Controller/      # HTTP controllers
    ├── Command/         # CLI commands (Symfony console)
    └── Form/            # Symfony forms
```

## Dependency Rules (CRITICAL)
Check imports in every file:

### Domain Layer
- ✅ Can import: PHP built-ins, other Domain classes in SAME module, Shared/Domain
- ❌ CANNOT import: Symfony, Doctrine, Application, Infrastructure, UI, other modules' non-Shared code

### Application Layer
- ✅ Can import: Domain (same module), Shared, Symfony Messenger interfaces
- ❌ CANNOT import: Infrastructure, UI, Doctrine, other modules directly

### Infrastructure Layer
- ✅ Can import: Domain (same module), Application (same module), Symfony, Doctrine
- ❌ CANNOT import: UI, other modules' Infrastructure

### UI Layer
- ✅ Can import: Application/Facade, Application/DTO, Symfony HTTP/Form
- ❌ CANNOT import: Domain directly, Infrastructure directly

## Validation Steps
1. Scan `src/` for all modules
2. For each module, verify directory structure exists
3. Check `use` statements in Domain files — flag any framework imports
4. Check Controllers — flag any direct Domain/Infrastructure usage (must go through Facade)
5. Check that each Command/Query has exactly one Handler
6. Check that Repository interfaces are in Domain, implementations in Infrastructure
7. Report violations with file path and suggested fix
