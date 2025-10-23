<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class WatchFilesTest extends WatcherTestCase
{
    #[Test]
    public function it_watches_changes_in_a_certain_file(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputContains('Something changed');
    }

    #[Test]
    public function it_reloads_by_changes_in_a_watched_file(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        Filesystem::changeFileContentsWith($fileToWatch, '<?php echo "Something changed"; ');
        $this->wait();
        $this->assertOutputContains('restarting due to changes...');
    }
}
