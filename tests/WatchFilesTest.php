<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class WatchFilesTest extends WatcherTestCase
{
    private const SCRIPT_TO_RUN = 'tests/fixtures/watch_test.php';

    /** @test */
    public function it_watches_changes_in_a_certain_file(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', $fileToWatch]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringContainsString('Something changed', $watcher->getOutput());
    }

    /** @test */
    public function it_reloads_by_changes_in_a_watched_file(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run(self::SCRIPT_TO_RUN, ['--watch', $fileToWatch]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringContainsString('restarting due to changes...', $watcher->getOutput());
    }
}
