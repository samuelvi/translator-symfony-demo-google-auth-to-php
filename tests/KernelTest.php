<?php

declare(strict_types=1);

namespace App\Tests;

use App\Kernel;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    public function testKernelCanBeInstantiated(): void
    {
        $kernel = new Kernel('test', true);

        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($kernel->isDebug());
    }

    public function testKernelBootsSuccessfully(): void
    {
        $kernel = new Kernel('test', true);
        $kernel->boot();

        // After boot, the container should be available
        $this->assertNotNull($kernel->getContainer());

        $kernel->shutdown();
    }

    public function testKernelCanGetProjectDir(): void
    {
        $kernel = new Kernel('test', true);

        $projectDir = $kernel->getProjectDir();

        $this->assertIsString($projectDir);
        $this->assertDirectoryExists($projectDir);
        $this->assertFileExists($projectDir . '/composer.json');
    }
}
