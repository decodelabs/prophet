<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

enum RunStatus: string
{
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case RequiresAction = 'requires_action';
    case Cancelling = 'cancelling';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case Completed = 'completed';
    case Expired = 'expired';
}
