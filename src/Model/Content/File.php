<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model\Content;

use DecodeLabs\Prophet\Model\Content;
use DecodeLabs\Prophet\Service\Medium;

class File implements Content
{
    protected(set) Medium $medium;
    protected(set) string $fileId;

    public function __construct(
        string $fileId,
        Medium $medium = Medium::Image
    ) {
        $this->medium = $medium;
        $this->fileId = $fileId;
    }

    public function getMedium(): Medium
    {
        return $this->medium;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'medium' => $this->medium->value,
            'fileId' => $this->fileId
        ];
    }
}
