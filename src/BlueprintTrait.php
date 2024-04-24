<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Dictum;
use DecodeLabs\Prophet\Service\LanguageModelLevel;
use DecodeLabs\Prophet\Service\Medium;
use ReflectionClass;

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

    public function getLanguageModelLevel(): LanguageModelLevel
    {
        return LanguageModelLevel::Standard;
    }

    public function getFeatures(): array
    {
        return [];
    }

    public function getFiles(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [];
    }

    public function generateAdditionalInstructions(
        Subject $target
    ): ?string {
        return null;
    }
}
