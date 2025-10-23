<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class ScriptExitTest extends WatcherTestCase
{
    #[Test]
    public function it_detects_when_script_exits(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertOutputContains('clean exit - waiting for changes before restart');
    }

    #[Test]
    public function it_detects_when_script_crashes(): void
    {
        $fileToWatch = Filesystem::createPHPFileThatCrashes();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertOutputContains('app crashed - waiting for file changes before starting');
    }
}
