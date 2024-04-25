<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Subject;

use DecodeLabs\Prophet\Subject;

class Generic implements Subject
{
    protected string $type;
    protected ?string $id;

    public function __construct(
        string $type,
        ?string $id
    ) {
        $this->type = $type;
        $this->id = $id;
    }

    public function getSubjectType(): string
    {
        return $this->type;
    }

    public function getSubjectId(): ?string
    {
        return $this->id;
    }
}
