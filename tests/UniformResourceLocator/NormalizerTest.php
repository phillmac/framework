<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\UniformResourceLocator;

use PHPUnit\Framework\TestCase;
use UserFrosting\UniformResourceLocator\Normalizer;

/**
 * Tests for ResourceLocator.
 */
class NormalizerTest extends TestCase
{
    /**
     * @param string      $uri
     * @param string|bool $path Expected result
     * @dataProvider normalizeProvider
     */
    public function testNormalize(string $uri, $path): void
    {
        $this->assertEquals($path, Normalizer::normalize($uri));
    }

    /**
     * Data provider for testNormalize.
     */
    public function normalizeProvider(): array
    {
        return [
            ['', ''],
            ['./', ''],
            ['././/./', ''],
            ['././/../', false],
            ['/', '/'],
            ['//', '/'],
            ['///', '/'],
            ['/././', '/'],
            ['foo', 'foo'],
            ['/foo', '/foo'],
            ['//foo', '/foo'],
            ['/foo/', '/foo/'],
            ['//foo//', '/foo/'],
            ['path/to/file.txt', 'path/to/file.txt'],
            ['path/to/../file.txt', 'path/file.txt'],
            ['path/to/../../file.txt', 'file.txt'],
            ['path/to/../../../file.txt', false],
            ['/path/to/file.txt', '/path/to/file.txt'],
            ['/path/to/../file.txt', '/path/file.txt'],
            ['/path/to/../../file.txt', '/file.txt'],
            ['/path/to/../../../file.txt', false],
            ['c:\\', 'c:/'],
            ['c:\\bar\\foo', 'c:/bar/foo'],
            ['c:\\bar/foo', 'c:/bar/foo'],
            ['c:\\path\\to\file.txt', 'c:/path/to/file.txt'],
            ['c:\\path\\to\../file.txt', 'c:/path/file.txt'],
            ['c:\\path\\to\../../file.txt', 'c:/file.txt'],
            ['c:\\path\\to\../../../file.txt', false],
            ['\\path\\to\file.txt', '/path/to/file.txt'],
            ['\\path/to\file.txt', '/path/to/file.txt'],
            ['stream://path/to/file.txt', 'stream://path/to/file.txt'],
            ['stream://path/to/../file.txt', 'stream://path/file.txt'],
            ['stream://path/to/../../file.txt', 'stream://file.txt'],
            ['stream://path/to/../../../file.txt', false],
        ];
    }

    public function testNormalizeReturnFalseOnSuppressedException(): void
    {
        $this->assertFalse(Normalizer::normalize(123));
    }

    public function testNormalizeThrowExceptionOnBadUri(): void
    {
        $this->expectException(\BadMethodCallException::class);
        Normalizer::normalize(123, true);
    }

    public function testNormalizeThrowExceptionOnBadUriPart(): void
    {
        $this->expectException(\BadMethodCallException::class);
        Normalizer::normalize('path/to/../../../file.txt', true);
    }

    /**
     * @param string      $uri
     * @param string|bool $path Expected result
     * @dataProvider normalizePathProvider
     */
    public function testNormalizePath(string $uri, $path): void
    {
        $this->assertEquals($path, Normalizer::normalizePath($uri));
    }

    /**
     * Data provider for testNormalize.
     */
    public function normalizePathProvider(): array
    {
        return [
            ['', ''],
            ['./', ''],
            ['/', '/'],
            ['//', '/'],
            ['///', '/'],
            ['foo', 'foo/'],
            ['/foo', '/foo/'],
            ['//foo', '/foo/'],
            ['/foo/', '/foo/'],
            ['//foo//', '/foo/'],
            ['c:\\', 'c:/'],
            ['c:\\bar\\foo', 'c:/bar/foo/'],
            ['c:\\bar/foo', 'c:/bar/foo/'],
        ];
    }
}
