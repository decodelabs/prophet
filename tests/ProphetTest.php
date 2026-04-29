<?php

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use DecodeLabs\Exceptional\Exception as ExceptionalException;
use DecodeLabs\Prophet as ProphetService;
use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\GenerationOptions;
use DecodeLabs\Prophet\GenerationResult;
use DecodeLabs\Prophet\Generator;
use DecodeLabs\Prophet\Platform;
use DecodeLabs\Prophet\Service\Medium;
use DecodeLabs\Prophet\Subject;
use DecodeLabs\Prophet\Subject\Generic;
use DecodeLabs\Prophet\Usage;
use PHPUnit\Framework\TestCase;

class ProphetTest extends TestCase
{
    public function testBlueprintTraitProvidesStatelessDefaults(): void
    {
        $blueprint = new AnalyzeBlueprintTrait();

        self::assertSame('analyze-blueprint-trait', $blueprint->getAction());
        self::assertSame('Analyze Blueprint Trait', $blueprint->getName());
        self::assertSame(Medium::Text, $blueprint->getMedium());
        self::assertSame(null, $blueprint->getDefaultModel());
        self::assertSame(null, $blueprint->generateInput(new Generic('demo', 'subject-1')));
    }

    public function testGenerateStillDispatchesThroughGenerator(): void
    {
        $generator = new TestGenerator();
        $prophet = new TestProphet(generators: [
            'demo-generator' => $generator
        ]);

        self::assertSame(
            'generated:subject-1',
            $prophet->generate('demo-generator', new Generic('demo', 'subject-1'))
        );
    }

    public function testRespondUsesDefaultPlatformAndReturnsResult(): void
    {
        $platform = new TestPlatform();
        $prophet = new TestProphet(
            defaultPlatform: 'OpenRouter',
            blueprints: [
                'demo-blueprint' => new TestTextBlueprint()
            ],
            platforms: [
                'OpenRouter' => $platform
            ]
        );

        $result = $prophet->respond('demo-blueprint', new Generic('demo', 'subject-1'));

        self::assertSame('OpenRouter', $result->platformName);
        self::assertSame('openai/gpt-4.1-mini', $result->model);
        self::assertSame('subject-1', $platform->lastSubject?->getSubjectId());
    }

    public function testRuntimePlatformOverrideWins(): void
    {
        $defaultPlatform = new TestPlatform('OpenRouter');
        $overridePlatform = new TestPlatform('OpenAi');
        $prophet = new TestProphet(
            defaultPlatform: 'OpenRouter',
            blueprints: [
                'demo-blueprint' => new TestTextBlueprint()
            ],
            platforms: [
                'OpenRouter' => $defaultPlatform,
                'OpenAi' => $overridePlatform
            ]
        );

        $result = $prophet->respond(
            'demo-blueprint',
            new Generic('demo', 'subject-1'),
            new GenerationOptions(platform: 'OpenAi', model: 'gpt-4o')
        );

        self::assertSame('OpenAi', $result->platformName);
        self::assertSame('gpt-4o', $result->model);
        self::assertSame(null, $defaultPlatform->lastOptions);
        self::assertSame('gpt-4o', $overridePlatform->lastOptions?->model);
    }

    public function testRespondTextReturnsTextOutput(): void
    {
        $platform = new TestPlatform();
        $prophet = new TestProphet(
            defaultPlatform: 'OpenRouter',
            blueprints: [
                'demo-blueprint' => new TestTextBlueprint()
            ],
            platforms: [
                'OpenRouter' => $platform
            ]
        );

        self::assertSame(
            'demo text',
            $prophet->respondText('demo-blueprint', new Generic('demo', 'subject-1'))
        );
    }

    public function testRespondJsonReturnsJsonOutput(): void
    {
        $platform = new TestPlatform(result: new GenerationResult(
            platformName: 'OpenRouter',
            model: 'openai/gpt-4.1-mini',
            medium: Medium::Json,
            json: ['ok' => true]
        ));
        $prophet = new TestProphet(
            defaultPlatform: 'OpenRouter',
            blueprints: [
                'demo-blueprint' => new TestJsonBlueprint()
            ],
            platforms: [
                'OpenRouter' => $platform
            ]
        );

        self::assertSame(
            ['ok' => true],
            $prophet->respondJson('demo-blueprint', new Generic('demo', 'subject-1'))
        );
    }

    public function testMissingPlatformFailsFast(): void
    {
        $prophet = new TestProphet(
            blueprints: [
                'demo-blueprint' => new TestTextBlueprint()
            ]
        );

        $this->expectException(ExceptionalException::class);
        $this->expectExceptionMessage('No Prophet platform was configured');

        $prophet->respond('demo-blueprint', new Generic('demo', 'subject-1'));
    }

    public function testRespondTextFailsWhenPlatformReturnsNonText(): void
    {
        $platform = new TestPlatform(result: new GenerationResult(
            platformName: 'OpenRouter',
            model: 'openai/gpt-4.1-mini',
            medium: Medium::Json,
            json: ['ok' => true]
        ));
        $prophet = new TestProphet(
            defaultPlatform: 'OpenRouter',
            blueprints: [
                'demo-blueprint' => new TestTextBlueprint()
            ],
            platforms: [
                'OpenRouter' => $platform
            ]
        );

        $this->expectException(ExceptionalException::class);
        $this->expectExceptionMessage('Prophet platform did not return text output');

        $prophet->respondText('demo-blueprint', new Generic('demo', 'subject-1'));
    }
}

class TestProphet extends ProphetService
{
    /**
     * @param array<string,Blueprint<Subject>> $blueprints
     * @param array<string,Generator<Subject,mixed>> $generators
     * @param array<string,Platform> $platforms
     */
    public function __construct(
        ?string $defaultPlatform = null,
        private array $blueprints = [],
        private array $generators = [],
        private array $platforms = []
    ) {
        parent::__construct(defaultPlatform: $defaultPlatform);
    }

    public function loadBlueprint(
        string $name
    ): Blueprint {
        return $this->blueprints[$name];
    }

    public function loadGenerator(
        string $name
    ): Generator {
        return $this->generators[$name];
    }

    public function loadPlatform(
        string $name
    ): Platform {
        return $this->platforms[$name];
    }
}

/**
 * @implements Blueprint<Subject>
 * @implements Generator<Subject,string>
 */
class TestTextBlueprint implements Blueprint, Generator
{
    use \DecodeLabs\Prophet\BlueprintTrait;

    public function getInstructions(): string
    {
        return 'Base instructions';
    }

    public function generateInput(
        Subject $subject
    ): string|array|null {
        return 'Subject=' . ($subject->getSubjectId() ?? 'none');
    }

    public function generate(
        Subject $subject
    ): mixed {
        return 'unused';
    }
}

/**
 * @implements Blueprint<Subject>
 */
class TestJsonBlueprint implements Blueprint
{
    use \DecodeLabs\Prophet\BlueprintTrait;

    public function getInstructions(): string
    {
        return 'Return JSON';
    }

    public function getMedium(): Medium
    {
        return Medium::Json;
    }
}

/**
 * @implements Generator<Subject,mixed>
 */
class TestGenerator implements Generator
{
    public function getAction(): string
    {
        return 'demo-generator';
    }

    public function generate(
        Subject $subject
    ): mixed {
        return 'generated:' . ($subject->getSubjectId() ?? 'none');
    }
}

class TestPlatform implements Platform
{
    public ?GenerationOptions $lastOptions = null;
    public ?Subject $lastSubject = null;

    public function __construct(
        private string $name = 'OpenRouter',
        private ?GenerationResult $result = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function supportsMedium(
        Medium $medium
    ): bool {
        return true;
    }

    public function getDefaultModel(
        Medium $medium
    ): string {
        return 'openai/gpt-4.1-mini';
    }

    public function respond(
        Blueprint $blueprint,
        Subject $subject,
        GenerationOptions $options
    ): GenerationResult {
        $this->lastOptions = $options;
        $this->lastSubject = $subject;

        return $this->result ?? new GenerationResult(
            platformName: $this->name,
            model: $options->model ?? $blueprint->getDefaultModel() ?? $this->getDefaultModel($blueprint->getMedium()),
            medium: $blueprint->getMedium(),
            text: 'demo text',
            usage: new Usage(totalTokens: 5)
        );
    }
}
