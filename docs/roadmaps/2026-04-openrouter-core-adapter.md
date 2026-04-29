# OpenRouter Core Adapter

Status: active
Owner: `prophet`

## Goal

Add a dependency-light OpenRouter platform adapter in core while keeping
Prophet public contracts and repository expectations unchanged.

## Batches

1. Promote the additive transcript-store boundary into core contracts.
2. Add the OpenRouter platform and local transcript replay behavior.
3. Add package-local verification for model selection, thread lifecycle,
   payload ordering, pagination, and delete behavior.
4. Keep rollout and provider-specific tuning out of unrelated adapter lanes.

## Done In This Batch

- additive `ThreadTranscriptStore` boundary added in core
- `DecodeLabs\Prophet\Platform\OpenRouter` and `DecodeLabs\Prophet\Platform\OpenRouter\Config` added
- transcript-backed message replay kept out of `Repository`
- local OpenRouter tests added for lifecycle, payload shape, JSON replies, and
  pagination
- README and contract docs updated to expose the adapter as additive core
  surface

## Next Task

Install dev dependencies in this package and run the new PHPUnit suite so the
OpenRouter lane has full local proof, not just syntax and PHPStan coverage.
