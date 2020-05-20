<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\FsWatchBased;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener as ChangesListenerInterface;
use Seregazhuk\ReactFsWatch\FsWatch;

final class ChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private FsWatch $fsWatch;

    public function __construct(WatchList $watchList, LoopInterface $loop)
    {
        $this->fsWatch = new FsWatch($this->makeOptions($watchList), $loop);
    }

    public static function isAvailable(): bool
    {
        return FsWatch::isAvailable();
    }

    public function start(): void
    {
        $this->fsWatch->run();
        $this->fsWatch->on(
            'change',
            function () {
                $this->emit('change');
            }
        );
    }

    public function onChange(callable $callback): void
    {
        $this->on('change', $callback);
    }

    public function stop(): void
    {
        $this->fsWatch->stop();
    }

    private function makeOptions(WatchList $watchList): string
    {
        $options = [];

        // first come paths
        if ($watchList->paths()) {
            $options[] = implode(' ', $watchList->paths());
        }

        // then we ignore
        if ($watchList->ignore()) {
            $options[] = '-e ' . implode(' ', $watchList->ignore());
        }

        // then include
        if ($watchList->fileExtensions()) {
            $options = array_merge($options, $this->makeIncludeOptions($watchList));
        }

        $options[] = '-I'; // Case-insensitive

        return implode(' ', $options);
    }

    private function makeIncludeOptions(WatchList $watchList): array
    {
        $options = [];
        // Before including we need to ignore everything
        if (empty($watchList->ignore())) {
            $options[] = '-e ".*"';
        }

        $regexpWithExtensions = array_map(
            static function ($extension) {
                return str_replace('*.', '.', $extension) . '$';
            },
            $watchList->fileExtensions()
        );
        $options[] = '-i ' . implode(' ', $regexpWithExtensions);
        return $options;
    }
}
