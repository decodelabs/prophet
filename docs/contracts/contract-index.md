# Prophet Contract Index

Status: active

This package owns the cross-platform contracts below.

## Public Contract Set

`Platform`

- adapter boundary for assistant, thread, message, and reply operations
- stays stable in the first OpenAI migration lane

`Repository`

- consumer-owned persistence boundary
- no first-lane required schema change

`ThreadTranscriptStore`

- additive local transcript boundary for stateless adapters
- no change to `Repository` required for existing consumers

`Assistant`

- persisted assistant record
- remote identity is optional at the abstraction layer, not guaranteed

`Thread`

- persisted lifecycle record
- `serviceId`, `runId`, `rawStatus`, `status`, and timestamps remain the
  compatibility envelope

`MessageList`

- ordered retrieval contract for thread history

## Contract Corrections Promoted In This Lane

`reply()` semantics:

- `Prophet::reply()` keeps its current signature
- it submits work through the platform and persists updated thread state
- it returns immediate submission-side message output
- it does not guarantee final assistant content

Thread readiness:

- `Thread::isReady()` remains the readiness gate
- ready means `completedAt` is set and status is no longer queued, in progress,
  or requires action
- polling stays the caller-visible path for final completion

Assistant persistence:

- Prophet persists assistant records as task-level configuration state
- adapter packages may require remote assistant assets or may treat the local
  record as sufficient readiness state
- the core contract does not force one remote materialization model

Stateless adapter history:

- adapters may satisfy `fetchMessages()` from additive local transcript storage
- this must not force a `Repository` contract rewrite in the same lane

## Adapter Rule

Adapter packages may add internal strategy layers. They must preserve these
core contracts unless a later lane promotes a core change here first.
