<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class ConfigTest extends WatcherTestCase
{
    /** @test */
    public function command_line_options_override_config_values(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['directory-to-watch']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $watcher = (new WatcherRunner())->run($fileToWatch, ['--watch', 'tests', '--config', $configFile]);
        $this->wait();

        $output = $watcher->getOutput();
        $this->assertStringNotContainsString('directory-to-watch', $output);
    }

    /** @test */
    public function it_can_use_config_path_from_command_line_arg(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['directory-to-watch']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $watcher = (new WatcherRunner())->run($fileToWatch, ['--config', $configFile]);
        $this->wait();

        $output = $watcher->getOutput();
        $this->assertStringContainsString('directory-to-watch', $output);
    }
}
