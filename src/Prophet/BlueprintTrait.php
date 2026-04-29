<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Dictum;
use DecodeLabs\Prophet\Service\Medium;
use ReflectionClass;

/**
 * @phpstan-require-implements Blueprint
 */
trait BlueprintTrait
{
    public function getAction(): string
    {
        $output = (new ReflectionClass($this))->getShortName();
        return Dictum::slug($output);
    }

    public function getName(): string
    {
        return Dictum::name($this->getAction());
    }

    public function getMedium(): Medium
    {
        return Medium::Text;
    }

    public function getDefaultModel(): ?string
    {
        return null;
    }

    public function generateInput(
        Subject $target
    ): string|array|null {
        return null;
    }
}
