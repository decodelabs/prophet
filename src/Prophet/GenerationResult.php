<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Service\Medium;
use JsonSerializable;

final class GenerationResult implements JsonSerializable
{
    /**
     * @param ?array<string,mixed> $json
     */
    public function __construct(
        public readonly string $platformName,
        public readonly string $model,
        public readonly Medium $medium,
        public readonly ?string $text = null,
        public readonly ?array $json = null,
        public readonly ?Usage $usage = null,
        public readonly mixed $raw = null
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'platformName' => $this->platformName,
            'model' => $this->model,
            'medium' => $this->medium->value,
            'text' => $this->text,
            'json' => $this->json,
            'usage' => $this->usage,
            'raw' => $this->raw
        ];
    }
}
