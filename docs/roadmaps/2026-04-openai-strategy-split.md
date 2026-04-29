# OpenAI Strategy Split

Status: active
Owner: `prophet`

## Goal

Protect Prophet public contracts while `prophet-openai` splits Assistants and
Responses behind one stable platform entrypoint.

## Batches

1. Promote the real core contracts into package-local Northstar docs.
2. Keep the first adapter refactor inside those contracts.
3. Add local verification around unchanged public behavior where needed.
4. Document any later core cleanup as a new lane, not as spillover.

## Done In This Batch

- package-local docs spine installed
- `reply()` semantics corrected to async submission wording
- no-schema-change boundary made explicit
- assistant and thread compatibility envelope documented
- adapter-side strategy split, Responses config gate, and local verification now
  exist in `prophet-openai`
- rollout notes now keep Inventors first and Acowtancy deferred

## Next Task

Keep Prophet stable unless the Inventors rollout exposes a real core contract
gap that must be promoted here explicitly.
