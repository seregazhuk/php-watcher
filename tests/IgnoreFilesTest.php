<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class IgnoreFilesTest extends WatcherTestCase
{
    /** @test */
    public function it_doesnt_reload_when_ignored_files_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner())->run($fileToWatch, ['--watch' , __DIR__, '--ignore', basename($fileToWatch),]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertStringNotContainsString('restarting due to changes...', $watcher->getOutput());
    }

    /** @test */
    public function it_doesnt_reload_when_ignored_directories_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner())->run($fileToWatch, ['--watch' , __DIR__, '--ignore', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertStringNotContainsString('restarting due to changes...', $watcher->getOutput());
    }
}
