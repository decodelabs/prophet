<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model\Content;

use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Prophet\Model\Content;
use DecodeLabs\Prophet\Service\Medium;

class Json implements Content
{
    public protected(set) string $content;

    public function __construct(
        string $content
    ) {
        $this->content = $content;
    }

    public function getMedium(): Medium
    {
        return Medium::Json;
    }

    public function getRawContent(): string
    {
        return $this->content;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        $output = json_decode($this->content, true);

        if ($output === null) {
            throw Exceptional::UnexpectedValue(
                message: 'Invalid JSON response',
                data: $this->content
            );
        }

        /** @var array<string,mixed> */
        $output = Coercion::asArray($output);
        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'medium' => $this->getMedium(),
            'content' => $this->getContent()
        ];
    }
}
