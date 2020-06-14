<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

interface ChangesListener
{
    public function start(): void;

    public function onChange(callable $callback): void;

    public function stop(): void;
}
