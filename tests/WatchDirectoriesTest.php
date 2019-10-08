<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class WatchDirectoriesTest extends WatcherTestCase
{
    /** @test */
    public function it_watches_changes_in_a_certain_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', __DIR__]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertStringContainsString('Something changed', $watcher->getOutput());
    }

    /** @test */
    public function it_reloads_by_changes_in_a_watched_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', __DIR__]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertStringContainsString('restarting due to changes...', $watcher->getOutput());
    }
}
