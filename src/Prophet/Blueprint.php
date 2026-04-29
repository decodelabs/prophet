<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Service\Medium;

/**
 * @template T of Subject
 */
interface Blueprint
{
    public function getAction(): string;
    public function getName(): string;
    public function getInstructions(): string;

    public function getMedium(): Medium;
    public function getDefaultModel(): ?string;

    /**
     * @param T $subject
     * @return string|array<string,mixed>|null
     */
    public function generateInput(
        Subject $subject
    ): string|array|null;
}
