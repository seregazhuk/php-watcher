<?php declare(strict_types=1);

namespace tests\Helper;

use Symfony\Component\Process\Process;

final class WatcherRunner
{
    public function run($scriptToRun, array $arguments = []): Process
    {
        $arguments = array_merge($arguments, ['--delay', 0.25]);
        //if (!isset($arguments['config'])) {
        //    $arguments = array_merge($arguments, ['--delay', 0.25]);
        //}
        $process = new Process(array_merge(['./php-watcher', $scriptToRun], $arguments));
        $process->start(function ($a, $g) {
            //echo $g . PHP_EOL;
        });

        return $process;
    }
}
