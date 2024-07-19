<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use Carbon\Carbon;
use DecodeLabs\Exceptional;
use JsonSerializable;

class Message implements JsonSerializable
{
    protected string $id;
    protected Carbon $createdAt;
    protected Role $role;

    /**
     * @var array<int, Content>
     */
    protected array $content = [];

    public function __construct(
        string $id,
        Carbon $createdAt,
        Role $role
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->role = $role;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function addContent(
        Content $content
    ): void {
        $this->content[] = $content;
    }

    /**
     * @return array<int, Content>
     */
    public function getAllContent(): array
    {
        return $this->content;
    }

    public function getContent(
        int $index
    ): ?Content {
        return $this->content[$index] ?? null;
    }

    public function getTextContent(): ?string
    {
        $output = null;

        foreach ($this->content as $content) {
            if ($content instanceof Content\Text) {
                if ($output === null) {
                    $output = '';
                }

                $output .= "\n" . $content->getContent();
            }
        }

        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    public function getJsonContent(): array
    {
        foreach ($this->content as $content) {
            if ($content instanceof Content\Json) {
                return $content->getContent();
            }
        }

        throw Exceptional::UnexpectedValue([
            'message' => 'Invalid JSON response',
            'data' => $this->content
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $content = [];

        foreach ($this->content as $item) {
            $content[] = $item->jsonSerialize();
        }

        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'role' => $this->role->value,
            'content' => $content
        ];
    }
}
