<?php

declare(strict_types=1);

namespace App\Infrastructure\Laravel\Traits\Kernel;

use Illuminate\Support\Fluent;

trait ExecutableTrait
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function run(mixed ...$arguments): mixed
    {
        return static::make()->execute(...$arguments);
    }

    public static function runIf(bool $boolean, mixed ...$arguments): mixed
    {
        return $boolean ? static::run(...$arguments) : new Fluent;
    }

    public static function runUnless(bool $boolean, mixed ...$arguments): mixed
    {
        return static::runIf(! $boolean, ...$arguments);
    }
}

