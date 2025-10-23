<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature\Helper;

use Throwable;

trait WithFilesystem
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }

    protected function onNotSuccessfulTest(Throwable $error): never
    {
        Filesystem::clear();
        parent::onNotSuccessfulTest($error);
    }
}
