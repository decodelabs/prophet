<?php

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use DecodeLabs\Prophet\ModelCatalog;
use PHPUnit\Framework\TestCase;

class ModelCatalogTest extends TestCase
{
    public function testCommonCatalogBuildsGroupedOptions(): void
    {
        $catalog = ModelCatalog::loadCommon();

        self::assertSame(
            [
                'OpenAI' => [
                    'gpt-5.4' => 'GPT 5.4',
                    'gpt-5.4-mini' => 'GPT 5.4 mini',
                    'gpt-4o' => 'GPT 4o'
                ],
                'OpenRouter' => [
                    'anthropic/claude-opus-4.7' => 'Claude Opus 4.7',
                    'anthropic/claude-sonnet-4.6' => 'Claude Sonnet 4.6',
                    'moonshotai/kimi-k2.6' => 'Kimi K2.6',
                    'anthropic/claude-haiku-4.5' => 'Claude Haiku 4.5'
                ]
            ],
            $catalog->toGroupedOptions()
        );
    }

    public function testCatalogCanBeFilteredByConfiguredPlatforms(): void
    {
        $catalog = ModelCatalog::loadCommon()->filterByPlatforms(['OpenAi']);

        self::assertSame(
            ['OpenAI'],
            $catalog->getPlatformNames()
        );

        self::assertSame(
            [
                'OpenAI' => [
                    'gpt-5.4-mini' => 'GPT 5.4 mini',
                    'gpt-5.4' => 'GPT 5.4',
                    'gpt-4o' => 'GPT 4o'
                ]
            ],
            $catalog->toGroupedOptions()
        );
    }

    public function testCatalogBuildsGroupedEncodedValueOptions(): void
    {
        $catalog = ModelCatalog::loadCommon()->filterByPlatforms(['OpenAi']);

        self::assertSame(
            [
                'OpenAI' => [
                    'OpenAi:gpt-5.4' => 'GPT 5.4',
                    'OpenAi:gpt-5.4-mini' => 'GPT 5.4 mini',
                    'OpenAi:gpt-4o' => 'GPT 4o'
                ]
            ],
            $catalog->toGroupedValueOptions()
        );
    }

    public function testCatalogCanResolveEntryFromEncodedValue(): void
    {
        $catalog = ModelCatalog::loadCommon();
        $entry = $catalog->findByValue('OpenRouter:anthropic/claude-sonnet-4.6');

        self::assertNotNull($entry);

        if ($entry === null) {
            self::fail('Expected model catalog entry to resolve');
        }

        self::assertSame('OpenRouter', $entry->platformName);
        self::assertSame('anthropic/claude-sonnet-4.6', $entry->model);
        self::assertSame('Claude Sonnet 4.6', $entry->label);
        self::assertSame(null, $catalog->findByValue('nope'));
    }

    public function testCatalogCanResolveEncodedValueFromRawModel(): void
    {
        $catalog = ModelCatalog::loadCommon()->filterByPlatforms(['OpenAi']);

        self::assertSame(
            'OpenAi:gpt-4o',
            $catalog->createValueForModel('gpt-4o')
        );

        self::assertSame(
            null,
            $catalog->createValueForModel('missing-model')
        );
    }

    public function testCustomCatalogPreservesManualProviderLabels(): void
    {
        $catalog = (new ModelCatalog())
            ->setPlatformLabel('OpenAi', 'OpenAI')
            ->add('OpenAi', 'gpt-4o', 'GPT 4o', 10)
            ->add('OpenAi', 'gpt-5.5', 'GPT 5.5', 100)
            ->setPlatformLabel('OpenRouter', 'OpenRouter')
            ->add('OpenRouter', 'anthropic/claude-sonnet-4.6', 'Claude Sonnet 4.6');

        self::assertFalse($catalog->isEmpty());
        self::assertCount(3, $catalog->getEntries());
        self::assertSame(
            [
                'OpenAI' => [
                    'gpt-5.5' => 'GPT 5.5',
                    'gpt-4o' => 'GPT 4o'
                ],
                'OpenRouter' => [
                    'anthropic/claude-sonnet-4.6' => 'Claude Sonnet 4.6'
                ]
            ],
            $catalog->toGroupedOptions()
        );
    }
}
