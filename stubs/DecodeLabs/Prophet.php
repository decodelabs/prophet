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
use DecodeLabs\Prophet\Repository as Ref1;
use DecodeLabs\Prophet\Blueprint as Ref2;
use DecodeLabs\Prophet\Generator as Ref3;
use DecodeLabs\Prophet\Subject as Ref4;
use DecodeLabs\Prophet\Model\Assistant as Ref5;
use DecodeLabs\Prophet\Model\Thread as Ref6;
use DecodeLabs\Prophet\Model\MessageList as Ref7;
use DecodeLabs\Prophet\Model\Message as Ref8;

class Prophet implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Prophet';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;

    public static function loadPlatform(string $name): Ref0 {
        return static::$_veneerInstance->loadPlatform(...func_get_args());
    }
    public static function getRepository(): Ref1 {
        return static::$_veneerInstance->getRepository();
    }
    public static function loadBlueprint(string $name): Ref2 {
        return static::$_veneerInstance->loadBlueprint(...func_get_args());
    }
    public static function loadGenerator(string $name): Ref3 {
        return static::$_veneerInstance->loadGenerator(...func_get_args());
    }
    public static function generate(string $name, Ref4 $subject): mixed {
        return static::$_veneerInstance->generate(...func_get_args());
    }
    public static function tryLoadAssistant(Ref2|string $blueprint, string $serviceName): ?Ref5 {
        return static::$_veneerInstance->tryLoadAssistant(...func_get_args());
    }
    public static function loadAssistant(Ref2|string $blueprint, string $serviceName): Ref5 {
        return static::$_veneerInstance->loadAssistant(...func_get_args());
    }
    public static function loadFreshAssistant(Ref2|string $blueprint, string $serviceName): Ref5 {
        return static::$_veneerInstance->loadFreshAssistant(...func_get_args());
    }
    public static function updateAssistant(Ref5 $assistant): void {}
    public static function loadAndDeleteAssistant(Ref2|string $blueprint, string $serviceName): bool {
        return static::$_veneerInstance->loadAndDeleteAssistant(...func_get_args());
    }
    public static function deleteAssistant(Ref5 $assistant): bool {
        return static::$_veneerInstance->deleteAssistant(...func_get_args());
    }
    public static function tryLoadThread(Ref2|string $blueprint, Ref4 $subject): ?Ref6 {
        return static::$_veneerInstance->tryLoadThread(...func_get_args());
    }
    public static function loadThread(Ref2|string $blueprint, Ref4 $subject): Ref6 {
        return static::$_veneerInstance->loadThread(...func_get_args());
    }
    public static function refreshThread(Ref6 $thread): void {}
    public static function loadAndPollThread(Ref2|string $blueprint, Ref4 $subject): Ref6 {
        return static::$_veneerInstance->loadAndPollThread(...func_get_args());
    }
    public static function pollThread(Ref6 $thread, int $attempts = 5): Ref6 {
        return static::$_veneerInstance->pollThread(...func_get_args());
    }
    public static function loadAndDeleteThread(Ref2|string $blueprint, Ref4 $subject): bool {
        return static::$_veneerInstance->loadAndDeleteThread(...func_get_args());
    }
    public static function deleteThread(Ref6 $thread): bool {
        return static::$_veneerInstance->deleteThread(...func_get_args());
    }
    public static function serializeThreadWithMessages(Ref6 $thread, int $limit = 20, ?string $afterId = NULL): array {
        return static::$_veneerInstance->serializeThreadWithMessages(...func_get_args());
    }
    public static function fetchMessages(Ref6 $thread, int $limit = 20, ?string $afterId = NULL): Ref7 {
        return static::$_veneerInstance->fetchMessages(...func_get_args());
    }
    public static function reply(Ref6 $thread, string $message): Ref8 {
        return static::$_veneerInstance->reply(...func_get_args());
    }
};
