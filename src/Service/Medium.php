<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Service;

enum Medium
{
    case Text;
    case Code;
    case Image;
    case Speech;
    case Video;
    case Audio;
}
