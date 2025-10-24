<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\ChangesListener;

use Evenement\EventEmitter;
use seregazhuk\PhpWatcher\Config\WatchList;
use Seregazhuk\ReactFsWatch\Change;
use Seregazhuk\ReactFsWatch\FsWatch;

final class FSWatchChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private ?FsWatch $fsWatch = null;

    public static function isAvailable(): bool
    {
        return FsWatch::isAvailable();
    }

    public function start(WatchList $watchList): void
    {
        // We need to manually check ignored paths
        // https://stackoverflow.com/questions/34713278/fswatch-to-watch-only-a-certain-file-extension
        $checkPathIsIgnored = function (string $path) use ($watchList): bool {
            foreach ($watchList->getIgnored() as $ignoredPath) {
                if (realpath($ignoredPath) === false && basename($path) === $ignoredPath) {
                    return true;
                }
                if (realpath($ignoredPath) !== false && $path === $ignoredPath) {
                    return true;
                }
            }

            return false;
        };

        $this->fsWatch = new FsWatch($this->makeOptions($watchList));
        $this->fsWatch->run();
        $this->fsWatch->on('error', static fn ($error) => print_r($error));
        $this->fsWatch->onChange(function (Change $fsWatchChange) use ($checkPathIsIgnored): void {
            $isIgnored = $checkPathIsIgnored($fsWatchChange->file());
            if (! $isIgnored) {
                $this->emit('change');
            }
        });
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
        // Before including we need to ignore everything
        $options[] = '-e ".*"';

        $regexpWithExtensions = array_map(
            static fn ($extension): string => '"'.str_replace('*.', '.', $extension).'$"',
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
