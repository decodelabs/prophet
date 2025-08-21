<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use DecodeLabs\Prophet\Service\Medium;
use JsonSerializable;

interface Content extends JsonSerializable
{
    public function getMedium(): Medium;
}
