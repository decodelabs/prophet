<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\BlueprintTrait;
use DecodeLabs\Prophet\Subject;

/**
 * @implements Blueprint<Subject>
 */
class AnalyzeBlueprintTrait implements Blueprint
{
    use BlueprintTrait;

    public function getInstructions(): string
    {
        return 'This is a test';
    }
}
