<?php declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;

final class WatchDirectoriesTest extends TestCase
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }

    /** @test */
    public function it_watches_changes_in_a_certain_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', __DIR__]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringContainsString('Something changed', $watcher->getOutput());
    }

    /** @test */
    public function it_reloads_by_changes_in_a_watched_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', __DIR__]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringContainsString('restarting due to changes...', $watcher->getOutput());
    }
}
