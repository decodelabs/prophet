<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

final class GenerationOptions
{
    public function __construct(
        public readonly ?string $platform = null,
        public readonly ?string $model = null,
        public readonly ?float $temperature = null,
        public readonly ?int $maxOutputTokens = null,
        public readonly ?string $user = null
    ) {
    }
}
