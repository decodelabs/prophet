<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model\Content;

use DecodeLabs\Prophet\Model\Content;
use DecodeLabs\Prophet\Service\Medium;

class Text implements Content
{
    protected Medium $medium = Medium::Text;
    protected string $content;

    public function __construct(
        string $content,
        Medium $medium = Medium::Text
    ) {
        $this->content = $content;
        $this->medium = $medium;
    }

    public function getMedium(): Medium
    {
        return $this->medium;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'medium' => $this->medium->value,
            'content' => $this->content
        ];
    }
}
