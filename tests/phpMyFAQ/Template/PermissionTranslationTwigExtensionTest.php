<?php

namespace phpMyFAQ\Template;

use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class PermissionTranslationTwigExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->extension = new PermissionTranslationTwigExtension();
    }

    public function testGetFilters()
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(1, $filters);

        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
        $this->assertEquals('permission', $filters[0]->getName());
    }
}
