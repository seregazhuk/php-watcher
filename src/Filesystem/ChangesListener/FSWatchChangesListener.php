<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\ChangesListener;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use Seregazhuk\ReactFsWatch\FsWatch;

final class FSWatchChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private ?FsWatch $fsWatch = null;

    public function __construct(private readonly LoopInterface $loop) {}

    public static function isAvailable(): bool
    {
        return FsWatch::isAvailable();
    }

    public function start(WatchList $watchList): void
    {
        $this->fsWatch = new FsWatch($this->makeOptions($watchList), $this->loop);

        $this->fsWatch->run();
        $this->fsWatch->on(
            'change',
            function (): void {
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
        if ($this->fsWatch instanceof FsWatch) {
            $this->fsWatch->stop();
        }
    }

    private function makeOptions(WatchList $watchList): string
    {
        $options = [];

        // first come paths
        if ($watchList->getPaths() !== []) {
            $options[] = implode(' ', $watchList->getPaths());
        }

        // then we ignore
        if ($watchList->getIgnored() !== []) {
            $options[] = '-e '.implode(' ', $watchList->getIgnored());
        }

        // then include
        if ($watchList->getFileExtensions() !== []) {
            $options = array_merge($options, $this->makeIncludeOptions($watchList));
        }

        $options[] = '-I'; // Case-insensitive

        return implode(' ', $options);
    }

    /**
     * @return string[]
     */
    private function makeIncludeOptions(WatchList $watchList): array
    {
        $options = [];
        // Before including we need to ignore everything
        if ($watchList->getIgnored() === []) {
            $options[] = '-e ".*"';
        }

        $regexpWithExtensions = array_map(
            static fn ($extension) => str_replace('*.', '.', $extension).'$',
            $watchList->getFileExtensions()
        );
        $options[] = '-i '.implode(' ', $regexpWithExtensions);

        return $options;
    }

    public function getName(): string
    {
        return 'fswatch';
    }
}
