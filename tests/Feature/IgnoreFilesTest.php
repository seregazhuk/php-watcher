<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class IgnoreFilesTest extends WatcherTestCase
{
    #[Test]
    public function it_doesnt_reload_when_ignored_files_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', __DIR__, '--ignore', basename($fileToWatch)]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputDoesntContain('restarting due to changes...');
    }

    #[Test]
    public function it_doesnt_reload_when_ignored_directories_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', __DIR__, '--ignore', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputDoesntContain('restarting due to changes');
    }
}
