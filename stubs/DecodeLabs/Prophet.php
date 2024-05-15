<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Prophet\Context as Inst;
use DecodeLabs\Prophet\Platform as Ref0;
use DecodeLabs\Prophet\Model\Repository as Ref1;
use DecodeLabs\Prophet\Blueprint as Ref2;
use DecodeLabs\Prophet\Model\Assistant as Ref3;
use DecodeLabs\Prophet\Subject as Ref4;
use DecodeLabs\Prophet\Model\Thread as Ref5;

class Prophet implements Proxy
{
    use ProxyTrait;

    const VENEER = 'DecodeLabs\\Prophet';
    const VENEER_TARGET = Inst::class;

    public static Inst $instance;

    public static function loadPlatform(string $name): Ref0 {
        return static::$instance->loadPlatform(...func_get_args());
    }
    public static function getRepository(): Ref1 {
        return static::$instance->getRepository();
    }
    public static function tryLoadAssistant(Ref2|string $blueprint, string $serviceName): ?Ref3 {
        return static::$instance->tryLoadAssistant(...func_get_args());
    }
    public static function loadAssistant(Ref2|string $blueprint, string $serviceName): Ref3 {
        return static::$instance->loadAssistant(...func_get_args());
    }
    public static function loadFreshAssistant(Ref2|string $blueprint, string $serviceName): Ref3 {
        return static::$instance->loadFreshAssistant(...func_get_args());
    }
    public static function deleteAssistant(Ref2|string $blueprint, string $serviceName): bool {
        return static::$instance->deleteAssistant(...func_get_args());
    }
    public static function tryLoadThread(Ref2|string $blueprint, Ref4 $subject): ?Ref5 {
        return static::$instance->tryLoadThread(...func_get_args());
    }
    public static function loadThread(Ref2|string $blueprint, Ref4 $subject): Ref5 {
        return static::$instance->loadThread(...func_get_args());
    }
    public static function deleteThread(Ref2|string $blueprint, Ref4 $subject): bool {
        return static::$instance->deleteThread(...func_get_args());
    }
    public static function serializeThreadWithMessages(Ref5 $thread, ?string $afterId = NULL, int $limit = 20): array {
        return static::$instance->serializeThreadWithMessages(...func_get_args());
    }
    public static function fetchMessages(Ref5 $thread, ?string $afterId = NULL, int $limit = 20): array {
        return static::$instance->fetchMessages(...func_get_args());
    }
    public static function reply(Ref5 $thread, string $message): array {
        return static::$instance->reply(...func_get_args());
    }
};
