<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Service;

enum Feature
{
    case Chat;
    case Thread;
    case CodeCompletion;
    case Function;

    case TextFile;
    case PdfFile;
    case ImageFile;
    case VideoFile;
    case AudioFile;
}
