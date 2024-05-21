<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

enum Role: string
{
    case Assistant = 'assistant';
    case System = 'system';
    case User = 'user';
}
