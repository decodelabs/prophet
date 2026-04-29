<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Exceptional\NotFoundException;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;
use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\GenerationOptions;
use DecodeLabs\Prophet\GenerationResult;
use DecodeLabs\Prophet\Generator;
use DecodeLabs\Prophet\Platform;
use DecodeLabs\Prophet\Subject;

class Prophet implements Service
{
    use ServiceTrait;

    protected Slingshot $slingshot;

    public function __construct(
        ?Slingshot $slingshot = null,
        protected ?string $defaultPlatform = null
    ) {
        $this->slingshot = $slingshot ?? new Slingshot();
    }

    public function loadPlatform(
        string $name
    ): Platform {
        return $this->slingshot->resolveNamedInstance(Platform::class, $name);
    }

    /**
     * @return Blueprint<Subject>
     */
    public function loadBlueprint(
        string $name
    ): Blueprint {
        $name = Dictum::id($name);
        return $this->slingshot->resolveNamedInstance(Blueprint::class, $name);
    }

    /**
     * @param string|Blueprint<Subject> $blueprint
     * @return Blueprint<Subject>
     */
    protected function normalizeBlueprint(
        string|Blueprint $blueprint
    ): Blueprint {
        if (is_string($blueprint)) {
            return $this->loadBlueprint($blueprint);
        }

        return $blueprint;
    }

    /**
     * @return Generator<Subject,mixed>
     */
    public function loadGenerator(
        string $name
    ): Generator {
        $output = $this->slingshot->tryResolveNamedInstance(Generator::class, $name);

        if ($output !== null) {
            return $output;
        }

        try {
            $blueprint = $this->normalizeBlueprint($name);

            if ($blueprint instanceof Generator) {
                return $blueprint;
            }
        } catch (NotFoundException) {
        }

        throw Exceptional::NotFound(
            message: 'Unable to resolve generator ' . $name
        );
    }

    public function generate(
        string $name,
        Subject $subject
    ): mixed {
        return $this->loadGenerator($name)->generate($subject);
    }

    /**
     * @param string|Blueprint<Subject> $blueprint
     */
    public function respond(
        string|Blueprint $blueprint,
        Subject $subject,
        ?GenerationOptions $options = null
    ): GenerationResult {
        $options ??= new GenerationOptions();
        $blueprint = $this->normalizeBlueprint($blueprint);

        return $this->loadPlatform(
            $options->platform ?? $this->defaultPlatform ?? throw Exceptional::Runtime(
                message: 'No Prophet platform was configured'
            )
        )->respond($blueprint, $subject, $options);
    }

    /**
     * @param string|Blueprint<Subject> $blueprint
     */
    public function respondText(
        string|Blueprint $blueprint,
        Subject $subject,
        ?GenerationOptions $options = null
    ): string {
        $result = $this->respond($blueprint, $subject, $options);

        if ($result->text === null) {
            throw Exceptional::Runtime(
                message: 'Prophet platform did not return text output'
            );
        }

        return $result->text;
    }

    /**
     * @param string|Blueprint<Subject> $blueprint
     * @return array<string,mixed>
     */
    public function respondJson(
        string|Blueprint $blueprint,
        Subject $subject,
        ?GenerationOptions $options = null
    ): array {
        $result = $this->respond($blueprint, $subject, $options);

        if ($result->json === null) {
            throw Exceptional::Runtime(
                message: 'Prophet platform did not return JSON output'
            );
        }

        return $result->json;
    }
}
