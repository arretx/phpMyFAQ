<?php

namespace phpMyFAQ;

use PHPUnit\Framework\TestCase;

class ServicesTest extends TestCase
{
    public function testGetSuggestLink(): void
    {
        // Create a mock Configuration object
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getDefaultUrl')->willReturn('http://example.com/');

        // Create a Services object
        $services = new Services($configuration);
        $services->setCategoryId(1);
        $services->setFaqId(123);
        $services->setLanguage('en');

        // Test getSuggestLink method
        $expected = 'http://example.com/index.php?action=send2friend&cat=1&id=123&artlang=en';
        $this->assertEquals($expected, $services->getSuggestLink());
    }

    public function testGetPdfLink(): void
    {
        // Create a mock Configuration object
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getDefaultUrl')->willReturn('http://example.com/');

        // Create a Services object
        $services = new Services($configuration);
        $services->setCategoryId(1);
        $services->setFaqId(123);
        $services->setLanguage('en');

        // Test getPdfLink method
        $expected = 'http://example.com/pdf.php?cat=1&id=123&artlang=en';
        $this->assertEquals($expected, $services->getPdfLink());
    }

    public function testGetPdfApiLink(): void
    {
        // Create a mock Configuration object
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getDefaultUrl')->willReturn('http://example.com/');

        // Create a Services object
        $services = new Services($configuration);
        $services->setCategoryId(1);
        $services->setFaqId(123);
        $services->setLanguage('en');

        // Test getPdfApiLink method
        $expected = 'http://example.com/pdf.php?cat=1&id=123&artlang=en';
        $this->assertEquals($expected, $services->getPdfApiLink());
    }
}
