---
name: symfony-review
description: Review kodu Symfony pod kątem best practices, security, wydajności i SOLID. Użyj gdy robisz code review, sprawdzasz jakość kodu, lub przed mergem PR.
---

# Symfony Code Review Checklist

When reviewing code in this project, check EVERY file against these criteria:

## Security
- SQL injection: Are we using prepared statements / QueryBuilder?
- XSS: Are outputs escaped in Twig? No `|raw` without justification?
- CSRF: Do forms have tokens?
- Authorization: Using Voters, not hardcoded role checks?
- Secrets: No API keys, passwords, tokens in code?
- Input: All user input validated before processing?

## Symfony Best Practices
- Controllers are thin? (max 10-15 lines per action)
- Business logic in services/handlers, not controllers?
- DTOs instead of raw arrays for data transfer?
- Events/listeners instead of direct coupling?
- Attributes for routing, DI, Doctrine mapping?
- Constructor injection only (no setter injection)?

## Doctrine
- N+1 queries: Missing fetch joins or eager loading?
- QueryBuilder instead of DQL strings?
- Migrations are idempotent?
- Indexes on columns used in WHERE/ORDER BY?
- Proper cascade and orphanRemoval settings?

## PHP 8.4
- `declare(strict_types=1)` in every file?
- readonly classes/properties where possible?
- Enums instead of string constants?
- match instead of switch?
- Named arguments where it improves readability?
- Union types / intersection types where appropriate?

## Tests
- New code has tests?
- Edge cases and error paths covered?
- External dependencies mocked?
- Descriptive test names: `test_it_does_something()`?

## Output
After review, provide:
1. List of issues: Critical / Warning / Info with file and line
2. Concrete fix suggestions with code examples
3. Summary of what's done well
