<?php declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;

final class IgnoreFilesTest extends TestCase
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }

    /** @test */
    public function it_doesnt_reload_when_ignored_files_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner())->run($fileToWatch, ['--watch' , __DIR__, '--ignore', basename($fileToWatch),]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringNotContainsString('restarting due to changes...', $watcher->getOutput());
    }

    /** @test */
    public function it_doesnt_reload_when_ignored_directories_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner())->run($fileToWatch, ['--watch' , __DIR__, '--ignore', Filesystem::fixturesDir()]);
        sleep(1);

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        sleep(1);
        $this->assertStringNotContainsString('restarting due to changes...', $watcher->getOutput());
    }
}
