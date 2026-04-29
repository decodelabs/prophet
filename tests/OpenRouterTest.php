<?php

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use DecodeLabs\Exceptional\Exception as ExceptionalException;
use DecodeLabs\Prophet\GenerationOptions;
use DecodeLabs\Prophet\Platform\OpenRouter;
use DecodeLabs\Prophet\Service\Medium;
use DecodeLabs\Prophet\Subject;
use DecodeLabs\Prophet\Subject\Generic;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class OpenRouterTest extends TestCase
{
    public function testSupportsExpectedMediums(): void
    {
        $platform = new TestOpenRouter('test-key');

        self::assertTrue($platform->supportsMedium(Medium::Text));
        self::assertTrue($platform->supportsMedium(Medium::Json));
    }

    public function testDefaultModelUsesPlatformFallback(): void
    {
        $platform = new TestOpenRouter('test-key');

        self::assertSame('openai/gpt-4.1-mini', $platform->getDefaultModel(Medium::Text));
    }

    public function testRespondBuildsPayloadFromInstructionsAndInput(): void
    {
        $platform = new TestOpenRouter('test-key');
        $platform->nextResponse = [
            'id' => 'chatcmpl-123',
            'choices' => [
                [
                    'message' => [
                        'content' => 'Latest answer'
                    ]
                ]
            ],
            'usage' => [
                'prompt_tokens' => 7,
                'completion_tokens' => 5,
                'total_tokens' => 12
            ]
        ];

        $result = $platform->respond(
            new TestTextBlueprint(),
            new Generic('demo', 'subject-1'),
            new GenerationOptions(temperature: 0.4, maxOutputTokens: 500)
        );

        self::assertSame('Latest answer', $result->text);
        self::assertSame(12, $result->usage?->totalTokens);
        $payload = $this->requirePayload($platform);
        $messages = $this->requireMessages($payload);
        self::assertSame('Base instructions', $messages[0]['content']);
        self::assertSame('Subject=subject-1', $messages[1]['content']);
        self::assertSame(0.4, $payload['temperature']);
        self::assertSame(500, $payload['max_tokens']);
    }

    public function testRespondHonorsExplicitModelOverride(): void
    {
        $platform = new TestOpenRouter('test-key');
        $platform->nextResponse = [
            'id' => 'chatcmpl-123',
            'choices' => [
                [
                    'message' => [
                        'content' => 'Latest answer'
                    ]
                ]
            ]
        ];

        $result = $platform->respond(
            new TestTextBlueprint(),
            new Generic('demo', 'subject-1'),
            new GenerationOptions(model: 'anthropic/claude-sonnet-4')
        );

        self::assertSame('anthropic/claude-sonnet-4', $result->model);
        self::assertSame('anthropic/claude-sonnet-4', $this->requirePayload($platform)['model']);
    }

    public function testJsonResponsesAreParsedAndRequestJsonMode(): void
    {
        $platform = new TestOpenRouter('test-key');
        $platform->nextResponse = [
            'id' => 'chatcmpl-json',
            'choices' => [
                [
                    'message' => [
                        'content' => '{"answer":"ok"}'
                    ]
                ]
            ]
        ];

        $result = $platform->respond(
            new TestJsonBlueprint(),
            new Generic('demo', 'subject-1'),
            new GenerationOptions()
        );

        self::assertSame(['answer' => 'ok'], $result->json);
        self::assertSame(['type' => 'json_object'], $this->requirePayload($platform)['response_format']);
    }

    public function testStructuredInputIsEncodedBeforeSending(): void
    {
        $platform = new TestOpenRouter('test-key');
        $platform->nextResponse = [
            'id' => 'chatcmpl-json',
            'choices' => [
                [
                    'message' => [
                        'content' => 'done'
                    ]
                ]
            ]
        ];

        $platform->respond(
            new TestStructuredBlueprint(),
            new Generic('demo', 'subject-1'),
            new GenerationOptions()
        );

        $messages = $this->requireMessages($this->requirePayload($platform));
        self::assertSame('{"subject":"subject-1"}', $messages[1]['content']);
    }

    public function testInvalidJsonResponseFailsFast(): void
    {
        $platform = new TestOpenRouter('test-key');
        $platform->nextResponse = [
            'id' => 'chatcmpl-json',
            'choices' => [
                [
                    'message' => [
                        'content' => 'not json'
                    ]
                ]
            ]
        ];

        $this->expectException(ExceptionalException::class);
        $this->expectExceptionMessage('OpenRouter response was not valid JSON');

        $platform->respond(new TestJsonBlueprint(), new Generic('demo', 'subject-1'), new GenerationOptions());
    }

    /**
     * @return array<string,mixed>
     */
    private function requirePayload(
        TestOpenRouter $platform
    ): array {
        if ($platform->lastPayload === null) {
            self::fail('Expected payload');
        }

        return $platform->lastPayload;
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<int,array{role:string,content:string}>
     */
    private function requireMessages(
        array $payload
    ): array {
        $messages = $payload['messages'] ?? null;

        if (!is_array($messages)) {
            self::fail('Expected messages payload');
        }

        /** @var array<int,array{role:string,content:string}> $messages */
        return $messages;
    }
}

class TestOpenRouter extends OpenRouter
{
    /**
     * @var ?array<string,mixed>
     */
    public ?array $lastPayload = null;

    /**
     * @var ?array<string,mixed>
     */
    public ?array $nextResponse = null;

    protected function requestChatCompletion(
        array $payload
    ): array {
        $this->lastPayload = $payload;

        if ($this->nextResponse === null) {
            throw new RuntimeException('No test response queued');
        }

        return $this->nextResponse;
    }
}

/**
 * @implements \DecodeLabs\Prophet\Blueprint<Subject>
 */
class TestStructuredBlueprint implements \DecodeLabs\Prophet\Blueprint
{
    use \DecodeLabs\Prophet\BlueprintTrait;

    public function getInstructions(): string
    {
        return 'Base instructions';
    }

    public function generateInput(
        Subject $subject
    ): string|array|null {
        return [
            'subject' => $subject->getSubjectId()
        ];
    }
}
