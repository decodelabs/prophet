<?php

declare(strict_types=1);

namespace PHPUnit\Framework;

abstract class TestCase
{
    public static function assertTrue(
        bool $condition,
        string $message = ''
    ): void {
    }

    public static function assertFalse(
        bool $condition,
        string $message = ''
    ): void {
    }

    public static function assertSame(
        mixed $expected,
        mixed $actual,
        string $message = ''
    ): void {
    }

    /**
     * @param \Countable|iterable<mixed> $haystack
     */
    public static function assertCount(
        int $expectedCount,
        \Countable|iterable $haystack,
        string $message = ''
    ): void {
    }

    public static function assertNotNull(
        mixed $actual,
        string $message = ''
    ): void {
    }

    public static function assertInstanceOf(
        string $expected,
        mixed $actual,
        string $message = ''
    ): void {
    }

    public static function fail(
        string $message = ''
    ): never {
        throw new \RuntimeException($message);
    }

    public function expectException(
        string $exception
    ): void {
    }

    public function expectExceptionMessage(
        string $message
    ): void {
    }
}
