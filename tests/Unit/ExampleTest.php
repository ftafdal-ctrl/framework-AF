<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\App;

class ExampleTest extends TestCase
{
    public function testCoreAppVersion(): void
    {
        $version = App::getVersion();
        $this->assertSame('2.1.0', $version);
    }
}
