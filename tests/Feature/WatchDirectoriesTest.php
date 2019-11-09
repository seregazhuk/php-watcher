<?php declare(strict_types=1);

namespace tests\Feature;

use tests\Feature\Helper\WatcherTestCase;
use tests\Helper\Filesystem;

final class WatchDirectoriesTest extends WatcherTestCase
{
    /** @test */
    public function it_watches_changes_in_a_certain_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputContains('Something changed');
    }

    /** @test */
    public function it_reloads_by_changes_in_a_watched_dir(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputContains('restarting due to changes...');
    }
}
