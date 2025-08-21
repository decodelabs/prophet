<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Service;

enum LanguageModelLevel
{
    case Basic;
    case Standard;
    case Advanced;
}
