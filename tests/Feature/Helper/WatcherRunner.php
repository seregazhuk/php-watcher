<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature\Helper;

use Symfony\Component\Process\Process;

final class WatcherRunner
{
    /**
     * @param  string[]  $arguments
     */
    public function run(string $scriptToRun, array $arguments = []): Process
    {
        $arguments = array_merge($arguments, ['--delay', 0.25]);
        $process = new Process(array_merge(['./php-watcher', $scriptToRun], $arguments));
        $process->start();

        return $process;
    }
}
