<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class RunScriptTest extends WatcherTestCase
{
    #[Test]
    public function it_runs_a_php_script(): void
    {
        $scriptToRun = Filesystem::createHelloWorldPHPFile();
        $this->watch($scriptToRun);

        $this->wait();

        $this->assertOutputContains("starting `php $scriptToRun`");
        $this->assertOutputContains('Hello, world');
    }

    #[Test]
    public function it_outputs_the_script_stderr(): void
    {
        $scriptToRun = Filesystem::createStdErrorPHPFile();
        $this->watch($scriptToRun);

        $this->wait();

        $this->assertOutputContains('Some error');
    }
}
