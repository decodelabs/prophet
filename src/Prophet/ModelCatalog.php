<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;
use JsonSerializable;

final class ModelCatalog implements JsonSerializable, Service
{
    use ServiceTrait;

    public const string ValueSeparator = ':';

    /**
     * @var array<string,string>
     */
    protected array $platformLabels = [];

    /**
     * @var list<ModelCatalogEntry>
     */
    protected array $entries = [];

    /**
     * @param array<string,string> $platformLabels
     * @param list<ModelCatalogEntry> $entries
     */
    public function __construct(
        array $platformLabels = [],
        array $entries = []
    ) {
        $this->platformLabels = $platformLabels;
        $this->entries = $entries;
    }

    public static function loadCommon(): self
    {
        return (new self())
            ->setPlatformLabel('OpenAi', 'OpenAI')
            ->setPlatformLabel('OpenRouter', 'OpenRouter')
            ->add('OpenAi', 'gpt-5.4-mini', 'GPT 5.4 mini')
            ->add('OpenAi', 'gpt-5.4', 'GPT 5.4')
            ->add('OpenAi', 'gpt-4o', 'GPT 4o')
            ->add('OpenRouter', 'anthropic/claude-haiku-4.6', 'Claude Haiku 4.6')
            ->add('OpenRouter', 'anthropic/claude-sonnet-4.6', 'Claude Sonnet 4.6')
            ->add('OpenRouter', 'moonshotai/kimi-k2.6', 'Kimi K2.6');
    }

    public function setPlatformLabel(
        string $platformName,
        string $label
    ): self {
        $this->platformLabels[$platformName] = $label;
        return $this;
    }

    public function add(
        string $platformName,
        string $model,
        ?string $label = null
    ): self {
        $this->entries[] = new ModelCatalogEntry(
            platformName: $platformName,
            model: $model,
            label: $label ?? $model
        );

        if (!isset($this->platformLabels[$platformName])) {
            $this->platformLabels[$platformName] = $platformName;
        }

        return $this;
    }

    /**
     * @param list<string> $platformNames
     */
    public function filterByPlatforms(
        array $platformNames
    ): self {
        $platformNames = array_values(array_unique($platformNames));
        $allowed = array_fill_keys($platformNames, true);
        $output = new self();

        foreach ($platformNames as $platformName) {
            if (isset($this->platformLabels[$platformName])) {
                $output->setPlatformLabel(
                    $platformName,
                    $this->platformLabels[$platformName]
                );
            }
        }

        foreach ($this->entries as $entry) {
            if (!isset($allowed[$entry->platformName])) {
                continue;
            }

            $output->add(
                platformName: $entry->platformName,
                model: $entry->model,
                label: $entry->label
            );
        }

        return $output;
    }

    /**
     * @return list<string>
     */
    public function getPlatformNames(): array
    {
        return array_keys($this->toGroupedOptions());
    }

    /**
     * @return list<ModelCatalogEntry>
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function isEmpty(): bool
    {
        return $this->entries === [];
    }

    public function createValue(
        string $platformName,
        string $model
    ): string {
        return $platformName . self::ValueSeparator . $model;
    }

    public function findByValue(
        string $value
    ): ?ModelCatalogEntry {
        [$platformName, $model] = array_pad(
            explode(self::ValueSeparator, $value, 2),
            2,
            null
        );

        if (
            !is_string($platformName) ||
            $platformName === '' ||
            !is_string($model) ||
            $model === ''
        ) {
            return null;
        }

        return $this->find(
            platformName: $platformName,
            model: $model
        );
    }

    public function findByModel(
        string $model
    ): ?ModelCatalogEntry {
        foreach ($this->entries as $entry) {
            if ($entry->model === $model) {
                return $entry;
            }
        }

        return null;
    }

    public function find(
        string $platformName,
        string $model
    ): ?ModelCatalogEntry {
        foreach ($this->entries as $entry) {
            if (
                $entry->platformName === $platformName &&
                $entry->model === $model
            ) {
                return $entry;
            }
        }

        return null;
    }

    public function createValueForModel(
        string $model
    ): ?string {
        $entry = $this->findByModel($model);

        if ($entry === null) {
            return null;
        }

        return $this->createValue(
            platformName: $entry->platformName,
            model: $entry->model
        );
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function toGroupedOptions(): array
    {
        $output = [];

        foreach ($this->entries as $entry) {
            $platformLabel = $this->platformLabels[$entry->platformName] ?? $entry->platformName;
            $output[$platformLabel] ??= [];
            $output[$platformLabel][$entry->model] = $entry->label;
        }

        return $output;
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function toGroupedValueOptions(): array
    {
        $output = [];

        foreach ($this->entries as $entry) {
            $platformLabel = $this->platformLabels[$entry->platformName] ?? $entry->platformName;
            $output[$platformLabel] ??= [];
            $output[$platformLabel][$this->createValue($entry->platformName, $entry->model)] = $entry->label;
        }

        return $output;
    }

    /**
     * @return array{
     *     platformLabels: array<string,string>,
     *     entries: list<ModelCatalogEntry>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'platformLabels' => $this->platformLabels,
            'entries' => $this->entries
        ];
    }
}
