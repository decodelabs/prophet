<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Platform\OpenRouter;

class Config
{
    public function __construct(
        public readonly string $baseUri = 'https://openrouter.ai/api/v1',
        public readonly ?string $httpReferer = null,
        public readonly ?string $title = null,
        public readonly int $timeout = 60,
        public readonly int $connectTimeout = 10
    ) {
    }
}
