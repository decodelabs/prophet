# Prophet Vision

Status: active

## Role

Prophet is the stable core package for assistant orchestration across multiple
platform adapters.

It owns:

- blueprint loading
- repository orchestration
- assistant and thread lifecycle entrypoints
- message retrieval shape
- public async workflow semantics

It does not own:

- OpenAI API shape
- provider-specific persistence rules
- provider-specific status vocabularies
- rollout policy for one adapter beyond the core compatibility boundary

## First-Lane Rule

The OpenAI strategy split must not require consumer app schema changes here.

No first-lane breaking changes to:

- `DecodeLabs\Prophet\Platform`
- `DecodeLabs\Prophet\Repository`
- `DecodeLabs\Prophet\Model\Assistant`
- `DecodeLabs\Prophet\Model\Thread`
- `DecodeLabs\Prophet::reply()`

## Next Task

Finish the additive OpenRouter core adapter lane without reopening the core
contracts or requiring repository schema changes.
