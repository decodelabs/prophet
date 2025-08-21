<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use Closure;
use DecodeLabs\Atlas\File;
use DecodeLabs\Prophet\Service\Feature;
use DecodeLabs\Prophet\Service\LanguageModelLevel;
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
    public function getLanguageModelLevel(): LanguageModelLevel;

    /**
     * @return array<Feature>
     */
    public function getFeatures(): array;

    /**
     * @return array<File>
     */
    public function getFiles(): array;

    /**
     * @return array<Closure>
     */
    public function getFunctions(): array;

    /**
     * @param T $subject
     */
    public function generateAdditionalInstructions(
        Subject $subject
    ): ?string;
}
