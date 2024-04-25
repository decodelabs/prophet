<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use Carbon\Carbon;

interface Suggestion
{
    public function getId(): ?string;
    public function getAction(): string;
    public function getThreadId(): ?string;

    /**
     * @return array<string>
     */
    public function getOptions(): array;
    public function getCreatedAt(): Carbon;
    public function getUpdatedAt(): Carbon;
}
