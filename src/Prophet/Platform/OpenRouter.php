<?php

/**
 * Prophet
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Platform;

use DecodeLabs\Exceptional;
use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\GenerationOptions;
use DecodeLabs\Prophet\GenerationResult;
use DecodeLabs\Prophet\Platform;
use DecodeLabs\Prophet\Platform\OpenRouter\Config;
use DecodeLabs\Prophet\Service\Medium;
use DecodeLabs\Prophet\Subject;
use DecodeLabs\Prophet\Usage;

class OpenRouter implements Platform
{
    public const string FallbackModel = 'openai/gpt-4.1-mini';

    protected Config $config;

    public function __construct(
        protected string $apiKey,
        ?Config $config = null
    ) {
        $this->config = $config ?? new Config();
    }

    public function getName(): string
    {
        return 'OpenRouter';
    }

    public function supportsMedium(
        Medium $medium
    ): bool {
        return match ($medium) {
            Medium::Text,
            Medium::Json => true
        };
    }

    public function getDefaultModel(
        Medium $medium
    ): string {
        if (!$this->supportsMedium($medium)) {
            throw Exceptional::Runtime(
                message: 'Unsupported medium'
            );
        }

        return self::FallbackModel;
    }

    public function respond(
        Blueprint $blueprint,
        Subject $subject,
        GenerationOptions $options
    ): GenerationResult {
        $medium = $blueprint->getMedium();

        if (!$this->supportsMedium($medium)) {
            throw Exceptional::Runtime(
                message: 'Unsupported medium'
            );
        }

        $response = $this->requestChatCompletion(
            $this->buildChatCompletionPayload($blueprint, $subject, $options)
        );

        return $this->mapResponse(
            medium: $medium,
            model: $options->model ?? $blueprint->getDefaultModel() ?? $this->getDefaultModel($medium),
            response: $response
        );
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    protected function requestChatCompletion(
        array $payload
    ): array {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];

        if ($this->config->httpReferer !== null) {
            $headers[] = 'HTTP-Referer: ' . $this->config->httpReferer;
        }

        if ($this->config->title !== null) {
            $headers[] = 'X-Title: ' . $this->config->title;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => json_encode($payload, JSON_THROW_ON_ERROR),
                'timeout' => max($this->config->timeout, $this->config->connectTimeout),
                'ignore_errors' => true
            ]
        ]);

        $body = @file_get_contents(
            rtrim($this->config->baseUri, '/') . '/chat/completions',
            false,
            $context
        );

        if ($body === false) {
            throw Exceptional::Runtime(
                message: 'OpenRouter request failed'
            );
        }

        $statusLine = $http_response_header[0] ?? '';
        preg_match('/\s(\d{3})\s/', $statusLine, $matches);
        $statusCode = (int)($matches[1] ?? 0);

        $response = json_decode($body, true);

        if (!is_array($response)) {
            throw Exceptional::Runtime(
                message: 'OpenRouter response was not valid JSON'
            );
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $errorMessage = 'OpenRouter request failed with status ' . $statusCode;

            if (
                isset($response['error']) &&
                is_array($response['error']) &&
                is_string($response['error']['message'] ?? null)
            ) {
                $errorMessage = $response['error']['message'];
            }

            throw Exceptional::Runtime(
                message: $errorMessage
            );
        }

        /** @var array<string,mixed> $response */
        return $response;
    }

    /**
     * @param Blueprint<Subject> $blueprint
     * @return array<string,mixed>
     */
    protected function buildChatCompletionPayload(
        Blueprint $blueprint,
        Subject $subject,
        GenerationOptions $options
    ): array {
        $medium = $blueprint->getMedium();
        $messages = [];
        $instructions = trim($blueprint->getInstructions());

        if ($instructions !== '') {
            $messages[] = [
                'role' => 'system',
                'content' => $instructions
            ];
        }

        $input = $blueprint->generateInput($subject);

        if ($input !== null) {
            $messages[] = [
                'role' => 'user',
                'content' => $this->normalizeInput($input, $medium)
            ];
        } elseif ($medium === Medium::Json) {
            $messages[] = [
                'role' => 'user',
                'content' => 'Return JSON.'
            ];
        }

        $output = [
            'model' => $options->model ?? $blueprint->getDefaultModel() ?? $this->getDefaultModel($medium),
            'messages' => $messages
        ];

        if ($options->temperature !== null) {
            $output['temperature'] = $options->temperature;
        }

        if ($options->maxOutputTokens !== null) {
            $output['max_tokens'] = $options->maxOutputTokens;
        }

        if ($medium === Medium::Json) {
            $output['response_format'] = [
                'type' => 'json_object'
            ];
        }

        return $output;
    }

    /**
     * @param string|array<string,mixed> $input
     */
    protected function normalizeInput(
        string|array $input,
        Medium $medium
    ): string {
        if ($medium === Medium::Json) {
            return $this->normalizeJsonInput($input);
        }

        if (is_string($input)) {
            return $input;
        }

        return json_encode($input, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string|array<string,mixed> $input
     */
    protected function normalizeJsonInput(
        string|array $input
    ): string {
        if (is_array($input)) {
            $input = json_encode($input, JSON_THROW_ON_ERROR);
        }

        if (stripos($input, 'json') !== false) {
            return $input;
        }

        return "Return JSON.\n\n" . $input;
    }

    /**
     * @param array<string,mixed> $response
     */
    protected function mapResponse(
        Medium $medium,
        string $model,
        array $response
    ): GenerationResult {
        $text = $this->extractResponseText($response);

        return new GenerationResult(
            platformName: $this->getName(),
            model: $model,
            medium: $medium,
            text: $medium === Medium::Text ? $text : null,
            json: $medium === Medium::Json ? $this->decodeJsonOutput($text) : null,
            usage: $this->extractUsage($response),
            raw: $response
        );
    }

    /**
     * @param array<string,mixed> $response
     */
    protected function extractResponseText(
        array $response
    ): string {
        $choices = $response['choices'] ?? null;

        if (!is_array($choices) || !is_array($choices[0] ?? null)) {
            throw Exceptional::Runtime(
                message: 'OpenRouter response did not contain choices'
            );
        }

        $message = $choices[0]['message'] ?? null;

        if (!is_array($message)) {
            throw Exceptional::Runtime(
                message: 'OpenRouter response did not contain a message'
            );
        }

        $content = $message['content'] ?? null;

        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            $buffer = [];

            foreach ($content as $item) {
                if (is_array($item) && is_string($item['text'] ?? null)) {
                    $buffer[] = $item['text'];
                }
            }

            if ($buffer !== []) {
                return implode("\n", $buffer);
            }
        }

        throw Exceptional::Runtime(
            message: 'OpenRouter assistant content was not textual'
        );
    }

    /**
     * @return array<string,mixed>
     */
    protected function decodeJsonOutput(
        string $text
    ): array {
        $output = json_decode($this->normalizeJsonOutput($text), true);

        if (!is_array($output)) {
            throw Exceptional::Runtime(
                message: 'OpenRouter response was not valid JSON'
            );
        }

        /** @var array<string,mixed> $output */
        return $output;
    }

    protected function normalizeJsonOutput(
        string $text
    ): string {
        $text = trim($text);

        if (
            str_starts_with($text, '```') &&
            preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $text, $matches) === 1 &&
            // @phpstan-ignore-next-line
            is_string($matches[1] ?? null)
        ) {
            return trim($matches[1]);
        }

        return $text;
    }

    /**
     * @param array<string,mixed> $response
     */
    protected function extractUsage(
        array $response
    ): ?Usage {
        $usage = $response['usage'] ?? null;

        if (!is_array($usage)) {
            return null;
        }

        return new Usage(
            inputTokens: is_int($usage['prompt_tokens'] ?? null) ? $usage['prompt_tokens'] : null,
            outputTokens: is_int($usage['completion_tokens'] ?? null) ? $usage['completion_tokens'] : null,
            totalTokens: is_int($usage['total_tokens'] ?? null) ? $usage['total_tokens'] : null,
            raw: $this->normalizeRawUsage($usage)
        );
    }

    /**
     * @param array<mixed> $usage
     * @return array<string,mixed>
     */
    protected function normalizeRawUsage(
        array $usage
    ): array {
        $output = [];

        foreach ($usage as $key => $value) {
            if (is_string($key)) {
                $output[$key] = $value;
            }
        }

        return $output;
    }
}
