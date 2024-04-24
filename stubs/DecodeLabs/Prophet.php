<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Prophet\Context as Inst;
use DecodeLabs\Prophet\Model\Repository as Ref0;
use DecodeLabs\Prophet\Blueprint as Ref1;
use DecodeLabs\Prophet\Model\Assistant as Ref2;
use DecodeLabs\Prophet\Subject as Ref3;
use DecodeLabs\Prophet\Model\Thread as Ref4;
use DecodeLabs\Prophet\Platform as Ref5;

class Prophet implements Proxy
{
    use ProxyTrait;

    const VENEER = 'DecodeLabs\\Prophet';
    const VENEER_TARGET = Inst::class;

    public static Inst $instance;

    public static function getRepository(): Ref0 {
        return static::$instance->getRepository();
    }
    public static function loadAssistant(Ref1|string $blueprint, string $serviceName): Ref2 {
        return static::$instance->loadAssistant(...func_get_args());
    }
    public static function loadThread(Ref1|string $blueprint, Ref3 $subject): Ref4 {
        return static::$instance->loadThread(...func_get_args());
    }
    public static function serializeThreadWithMessages(Ref4 $thread, ?string $afterId = NULL, int $limit = 20): array {
        return static::$instance->serializeThreadWithMessages(...func_get_args());
    }
    public static function fetchMessages(Ref4 $thread, ?string $afterId = NULL, int $limit = 20): array {
        return static::$instance->fetchMessages(...func_get_args());
    }
    public static function loadPlatform(string $name): Ref5 {
        return static::$instance->loadPlatform(...func_get_args());
    }
};
