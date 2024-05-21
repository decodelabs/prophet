<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use Carbon\Carbon;
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt(),
            'role' => $this->getRole(),
            'content' => $this->getAllContent()
        ];
    }
}
