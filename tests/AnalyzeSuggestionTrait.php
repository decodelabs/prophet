<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use Carbon\Carbon;
use DecodeLabs\Prophet\Model\Suggestion;
use DecodeLabs\Prophet\Model\SuggestionTrait;

class AnalyzeSuggestionTrait implements Suggestion
{
    use SuggestionTrait;

    public function getId(): ?string
    {
        return null;
    }

    public function getAction(): string
    {
        return 'analyze';
    }

    public function getThreadId(): ?string
    {
        return null;
    }

    /**
     * @return array<string>
     */
    public function getOptions(): array
    {
        return [];
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::now();
    }

    public function getUpdatedAt(): Carbon
    {
        return Carbon::now();
    }
}
