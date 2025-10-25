<?php

namespace seregazhuk\PhpWatcher\Filesystem\ChangesListener;

use seregazhuk\PhpWatcher\Config\WatchList;

interface ChangesListenerInterface
{
    public function start(WatchList $watchList): void;

    public function onChange(callable $callback): void;

    public function stop(): void;

    public function getName(): string;
}
