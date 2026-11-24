<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Framework-AF Core Application Bootstrap
 */
class App
{
    public const VERSION = '2.1.0';

    public static function getVersion(): string
    {
        return self::VERSION;
    }
}
