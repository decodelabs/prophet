<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

/**
 * @template TSubject of Subject
 * @template TOutput
 */
interface Generator
{
    public function getAction(): string;

    /**
     * @param TSubject $subject
     * @return TOutput
     */
    public function generate(
        Subject $subject
    ): mixed;
}
