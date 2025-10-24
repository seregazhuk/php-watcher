<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\ChangesListener;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use Seregazhuk\ReactFsWatch\FsWatch;
use Symfony\Component\Process\Process;

final class FSWatchChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private ?Process $process = null;

    private ?TimerInterface $timer = null;

    private const INTERVAL = 0.15;

    public function __construct(private readonly LoopInterface $loop) {

    }
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

        $argsAndOptions = $this->makeOptions($watchList);
        $this->process = new Process(command: ["fswatch", "-xrn", ...$argsAndOptions]);
        $this->process->start();

        $this->timer = $this->loop->addPeriodicTimer(
            self::INTERVAL,
            function () use ($checkPathIsIgnored): void {
                $output = $this->process->getIncrementalOutput();
                if ($output === '') {
                    return;
                }
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if ($line === '') {
                        continue;
                    }
                    [$path, ] = explode(' ', $line);
                    if (!$checkPathIsIgnored($path)) {
                        $this->emit('change');
                    }
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
        if ($this->process instanceof Process && $this->process->isRunning()) {
            $this->process->stop();
        }

        if ($this->timer instanceof TimerInterface) {
            $this->loop->cancelTimer($this->timer);
        }
    }

    private function makeOptions(WatchList $watchList): array
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

        return $options;
    }

    /**
     * @return string[]
     */
    private function makeIncludeOptions(WatchList $watchList): array
    {
        // Before including we need to ignore everything
        $options[] = '-e';
        $options[] = '.*';
        $options[] = '-i';

        $regexpWithExtensions = array_map(
            static fn ($extension): string => str_replace(['*.', '.'], '\\.', $extension).'$',
            $watchList->getFileExtensions()
        );
        return array_merge($options, $regexpWithExtensions);
    }

    public function getName(): string
    {
        return 'fswatch';
    }
}
