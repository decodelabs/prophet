# Prophet System Architecture

Status: active
Lane: stateless response core

## Purpose

Prophet is the cross-platform contract layer for stateless AI generation.
It coordinates local blueprints, optional generator wrappers, and provider
adapters.

## Runtime Shape

`Prophet` service:

- resolves blueprints and generators
- loads the named platform adapter
- dispatches one immediate response request
- provides text and JSON convenience wrappers

Platform boundary:

- owns remote provider requests
- chooses a provider default model when no runtime or blueprint override exists
- normalizes provider output into `GenerationResult`

Model catalog boundary:

- owns grouped provider/model display data
- remains provider-agnostic at the Prophet core level
- can be filtered by available platform names before rendering

## Lifecycle Model

Response lifecycle:

1. Caller selects a blueprint by name or instance.
2. Prophet resolves the target platform from runtime options or the default
   platform binding.
3. The platform resolves the final model from runtime override, blueprint
   default, then platform default.
4. The platform executes one provider request.
5. Prophet returns a normalized `GenerationResult`, or `respondText()` /
   `respondJson()` unwrap the expected payload.

## Adapter Freedom

Adapter packages may:

- shape requests differently per provider
- choose provider-specific default models
- normalize provider responses into shared Prophet result types
- expose curated provider model ids through app-owned `ModelCatalog` instances

Adapter packages may not:

- redefine the meaning of core public types without promoting a core contract
  change here first
- require repository-backed state in the core contract
- bypass runtime model overrides or blueprint default models
