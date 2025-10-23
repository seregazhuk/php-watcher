<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Unit;

use PHPUnit\Framework\TestCase;
use seregazhuk\PhpWatcher\Screen\SpinnerFactory;
use seregazhuk\PhpWatcher\Screen\VoidSpinner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

final class SpinnerFactoryTest extends TestCase
{
    /** @test */
    public function it_should_return_void_spinner_if_spinner_is_disabled(): void
    {
        $output = new ConsoleOutput;
        $spinner = SpinnerFactory::create($output, $spinnerDisabled = true);
        $this->assertInstanceOf(VoidSpinner::class, $spinner);
    }

    /** @test */
    public function it_should_return_void_spinner_if_ansi_output_is_not_supported(): void
    {
        $output = new NullOutput;
        $spinner = SpinnerFactory::create($output, $spinnerDisabled = false);
        $this->assertInstanceOf(VoidSpinner::class, $spinner);
    }
}
