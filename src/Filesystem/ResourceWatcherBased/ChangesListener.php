<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\ResourceWatcherBased;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener as ChangesListenerInterface;
use Yosymfony\ResourceWatcher\ResourceWatcher;

final class ChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private const INTERVAL = 0.15;

    private LoopInterface $loop;

    private ResourceWatcher $watcher;

    public function __construct(WatchList $watchList, LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->watcher = ResourceWatcherBuilder::create($watchList);
    }

    public function start(): void
    {
        $this->loop->addPeriodicTimer(
            self::INTERVAL,
            function () {
                if ($this->watcher->findChanges()->hasChanges()) {
                    $this->emit('change');
                }
            }
        );
    }

    public function onChange(callable $callback): void
    {
        $this->on('change', $callback);
    }

    public function stop(): void
    {
    }
}
