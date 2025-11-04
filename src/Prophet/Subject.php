<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

interface Subject
{
    public function getSubjectType(): string;
    public function getSubjectId(): ?string;
}
