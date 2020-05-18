<?php

declare(strict_types=1);
namespace seregazhuk\PhpWatcher\Filesystem;

use seregazhuk\PhpWatcher\Config\WatchList;

interface ChangesListener
{
    public function start(WatchList $watchList): void;

    public function onChange(callable $callback): void;
}
