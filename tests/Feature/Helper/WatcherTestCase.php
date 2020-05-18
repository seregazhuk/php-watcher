<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

abstract class WatcherTestCase extends TestCase
{
    use WithFilesystem;

    private const WAIT_TIMEOUT_MS = 5000000;

    /**
     * @var Process
     */
    private $watcherRunner;

    protected function wait(): void
    {
        usleep(self::WAIT_TIMEOUT_MS);
    }

    protected function watch(string $scriptToRun, array $options = []): void
    {
        $this->watcherRunner = (new WatcherRunner())->run($scriptToRun, $options);
    }

    public function assertOutputContains(string $string): void
    {
        $output = $this->watcherRunner->getOutput();
        $this->assertStringContainsString($string, $output);
    }

    public function assertOutputDoesntContain(string $string): void
    {
        $output = $this->watcherRunner->getOutput();
        $this->assertStringNotContainsString($string, $output);
    }
}
