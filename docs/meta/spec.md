# Prophet — Package Specification

> **Cluster:** `logic`
> **Language:** `php`
> **Milestone:** `m6`
> **Repo:** `https://github.com/decodelabs/prophet`
> **Role:** stateless AI response orchestration

## Overview

### Purpose

Prophet provides a small cross-platform contract for AI generation. It enables:

- defining blueprints with instructions and subject input mapping
- selecting models by runtime override, blueprint default, or platform default
- dispatching one immediate response request to a named platform
- returning normalized text or JSON output
- keeping generator-style consumer APIs where useful
- building grouped model catalogs for provider-aware UI dropdowns

### Non-goals

- assistant lifecycle management
- thread or transcript management
- polling or async orchestration
- repository-backed AI state
- provider-specific chat history abstractions

## Public Surface

### Key Types

- `DecodeLabs\Prophet` — main service for generator dispatch and direct stateless response calls
- `DecodeLabs\Prophet\Blueprint` — blueprint contract for instructions, medium, level, and subject input
- `DecodeLabs\Prophet\BlueprintTrait` — default blueprint helpers
- `DecodeLabs\Prophet\Generator` — optional consumer-facing wrapper interface
- `DecodeLabs\Prophet\Platform` — stateless provider adapter contract
- `DecodeLabs\Prophet\GenerationOptions` — runtime platform and model overrides
- `DecodeLabs\Prophet\GenerationResult` — normalized output payload
- `DecodeLabs\Prophet\ModelCatalog` — grouped provider/model catalog helper
- `DecodeLabs\Prophet\ModelCatalogEntry` — individual model catalog item
- `DecodeLabs\Prophet\Usage` — provider-neutral token accounting
- `DecodeLabs\Prophet\Subject` — typed subject boundary
- `DecodeLabs\Prophet\Platform\OpenRouter` — immediate OpenRouter adapter
- `DecodeLabs\Prophet\Service\Medium` — `Text` and `Json`

### Main Entry Points

- `Prophet::generate(string $name, Subject $subject): mixed`
- `Prophet::respond(string|Blueprint $blueprint, Subject $subject, ?GenerationOptions $options = null): GenerationResult`
- `Prophet::respondText(string|Blueprint $blueprint, Subject $subject, ?GenerationOptions $options = null): string`
- `Prophet::respondJson(string|Blueprint $blueprint, Subject $subject, ?GenerationOptions $options = null): array`

### Contract Rules

- `generate()` remains a generator dispatcher for existing consumer call sites
- `respond()` is the core stateless platform call
- `respondText()` requires a textual result and throws otherwise
- `respondJson()` requires decoded JSON output and throws otherwise
- platform resolution order is runtime option first, then Prophet default platform
- model resolution order is runtime option first, then blueprint default, then platform default
- `ModelCatalog::toGroupedOptions()` returns provider-labeled grouped select data
- `ModelCatalog::filterByPlatforms()` removes options for unavailable platforms

## Dependencies

- `decodelabs/dictum`
- `decodelabs/exceptional`
- `decodelabs/kingdom`
- `decodelabs/slingshot`

## Notes

- OpenRouter remains in the core package as the lightweight non-OpenAI adapter
- OpenAI-specific execution lives in `decodelabs/prophet-openai`
