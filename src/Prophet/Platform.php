<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Service\Medium;

interface Platform
{
    public function getName(): string;

    public function supportsMedium(
        Medium $medium
    ): bool;

    public function getDefaultModel(Medium $medium): string;

    /**
     * @param Blueprint<Subject> $blueprint
     */
    public function respond(
        Blueprint $blueprint,
        Subject $subject,
        GenerationOptions $options
    ): GenerationResult;
}
