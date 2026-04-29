# Prophet

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/prophet?style=flat)](https://packagist.org/packages/decodelabs/prophet)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/prophet.svg?style=flat)](https://packagist.org/packages/decodelabs/prophet)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/prophet.svg?style=flat)](https://packagist.org/packages/decodelabs/prophet)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/prophet/integrate.yml?branch=develop)](https://github.com/decodelabs/prophet/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/prophet?style=flat)](https://packagist.org/packages/decodelabs/prophet)

### Stateless AI response orchestration

Prophet provides a small contract for blueprint-driven AI generation. A
blueprint defines instructions, medium, default model, and subject input. A
platform executes one request and returns the result immediately.

---

## Installation

This package requires PHP 8.4 or higher.

Install via Composer:

```bash
composer require decodelabs/prophet
```

## Usage

Core surfaces:

- `DecodeLabs\Prophet` for generator dispatch and direct response calls
- `DecodeLabs\Prophet\Blueprint` for instructions and subject input shaping
- `DecodeLabs\Prophet\Platform` for stateless provider adapters
- `DecodeLabs\Prophet\GenerationOptions` for runtime platform/model overrides
- `DecodeLabs\Prophet\GenerationResult` for normalized outputs
- `DecodeLabs\Prophet\ModelCatalog` for grouped provider/model option lists

OpenRouter support is available in the core package through
`DecodeLabs\Prophet\Platform\OpenRouter`.

`ModelCatalog` can be used to build grouped select options and filter them
down to whichever platforms are configured in the host app:

```php
use DecodeLabs\Prophet\ModelCatalog;

$catalog = ModelCatalog::common()
    ->filterByPlatforms(['OpenAi', 'OpenRouter']);

$options = $catalog->toGroupedOptions();
```

Package-local docs and specs live under [docs/](./docs/README.md).

## Licensing

Prophet is licensed under the MIT License. See [LICENSE](./LICENSE) for the
full license text.
