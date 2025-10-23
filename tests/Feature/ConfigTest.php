<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class ConfigTest extends WatcherTestCase
{
    #[Test]
    public function command_line_options_override_config_values(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['directory-to-watch']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $this->watch($fileToWatch, ['--watch', 'tests', '--config', $configFile]);
        $this->wait();

        $this->assertOutputDoesntContain('directory-to-watch');
    }

    #[Test]
    public function it_can_use_config_path_from_command_line_arg(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['directory-to-watch']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $this->watch($fileToWatch, ['--config', $configFile]);
        $this->wait();

        $this->assertOutputContains('watching: directory-to-watch');
    }

    #[Test]
    public function it_uses_values_from_config(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['first', 'second']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $this->watch($fileToWatch, ['--config', $configFile]);
        $this->wait();

        $this->assertOutputContains('watching: first, second');
    }

    #[Test]
    public function command_line_options_override_values_from_config(): void
    {
        $configFile = Filesystem::createConfigFile(['watch' => ['directory-to-watch']]);
        $fileToWatch = Filesystem::createHelloWorldPHPFile();

        $this->watch($fileToWatch, ['--watch', $configFile]);
        $this->wait();

        $this->assertOutputContains("watching: $configFile");
    }
}
