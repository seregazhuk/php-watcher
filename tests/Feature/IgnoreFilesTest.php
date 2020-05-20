<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;

final class IgnoreFilesTest extends WatcherTestCase
{
    public function it_doesnt_reload_when_ignored_files_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch' , __DIR__, '--ignore', basename($fileToWatch),]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputDoesntContain('restarting due to changes...');
    }

    /** @test */
    public function it_doesnt_reload_when_ignored_directories_change(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch' , __DIR__, '--ignore', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputDoesntContain('restarting due to changes...');
    }
}
