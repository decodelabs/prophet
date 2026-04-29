# Prophet System Inventory

Status: active

## Owned Components

`DecodeLabs\Prophet\Prophet`

- service entrypoint
- orchestrates blueprint, generator, and platform usage

`DecodeLabs\Prophet\Blueprint`

- task definition contract
- source of action, instructions, medium, default model, and subject input

`DecodeLabs\Prophet\Platform`

- provider adapter contract
- remote execution boundary

`DecodeLabs\Prophet\ModelCatalog`

- provider-grouped model list for consumer UIs
- supports filtering by configured platform names

`DecodeLabs\Prophet\ModelCatalogEntry`

- single model choice entry for a named provider
- intended for grouped select rendering

## Canonical Concepts

Blueprint:

- stable prompt definition plus subject input mapping
- owns medium and optional blueprint-level default model

Response:

- one immediate provider call
- returns normalized text or JSON output

Model catalog:

- app-owned or package-seeded list of provider-grouped model options
- can be filtered down to currently configured platforms before rendering

## External Dependencies

Consumer applications own:

- subject model wiring
- platform configuration
- UI rendering of model catalogs when needed

Adapter packages own:

- provider-specific request execution
- provider default model decisions
- response normalization
