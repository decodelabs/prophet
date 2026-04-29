<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Service;

enum Medium: string
{
    case Text = 'text';
    case Json = 'json';
}
