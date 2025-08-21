<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Service;

enum Medium: string
{
    case Text = 'text';
    case Code = 'code';
    case Json = 'json';
    case Image = 'image';
    case Speech = 'speech';
    case Video = 'video';
    case Audio = 'audio';
}
