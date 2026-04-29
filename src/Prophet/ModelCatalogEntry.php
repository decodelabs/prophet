<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

final class ModelCatalogEntry implements \JsonSerializable
{
    public function __construct(
        public readonly string $platformName,
        public readonly string $model,
        public readonly string $label,
        public readonly int $weight = 0
    ) {
    }

    /**
     * @return array{
     *     platformName: string,
     *     model: string,
     *     label: string,
     *     weight: int
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'platformName' => $this->platformName,
            'model' => $this->model,
            'label' => $this->label,
            'weight' => $this->weight
        ];
    }
}
