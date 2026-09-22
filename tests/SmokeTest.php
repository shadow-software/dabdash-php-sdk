<?php

declare(strict_types=1);

namespace ShadowSoftware\DabDash\Tests;

use PHPUnit\Framework\TestCase;
use ShadowSoftware\DabDash\Configuration;

final class SmokeTest extends TestCase
{
    public function test_configuration_autoloads(): void
    {
        $this->assertTrue(class_exists(Configuration::class));

        $config = Configuration::getDefaultConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertIsString($config->getHost());
    }
}
