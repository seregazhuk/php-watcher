<?php declare(strict_types=1);

namespace tests\Feature\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use tests\Helper\WithFilesystem;

abstract class WatcherTestCase extends TestCase
{
    use WithFilesystem;

    /**
     * @var Process
     */
    private $watcherRunner;

    protected function wait(): void
    {
        usleep(2000000);
    }

    protected function watch(string $scriptToRun, array $options = []): void {
        $this->watcherRunner = (new WatcherRunner())->run($scriptToRun, $options);
    }

    public function assertOutputContains(string $string): void
    {
        $output = $this->watcherRunner->getOutput();
        $this->assertOutputContains($string, $output);
    }

    public function assertOutputDoesntContain(string $string): void
    {
        $output = $this->watcherRunner->getOutput();
        $this->assertOutputContains($string, $output);
    }
}
