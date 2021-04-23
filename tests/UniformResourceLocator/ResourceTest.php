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
use UserFrosting\UniformResourceLocator\Resource;
use UserFrosting\UniformResourceLocator\ResourceInterface;
use UserFrosting\UniformResourceLocator\ResourceLocation;
use UserFrosting\UniformResourceLocator\ResourceStream;
use UserFrosting\UniformResourceLocator\ResourceStreamInterface;

/**
 * Tests for ResourceLocator.
 */
class ResourceTest extends TestCase
{
    /** @var ResourceStream */
    protected $stream;

    protected $streamScheme = 'foo';
    protected $streamPrefix = '';
    protected $streamPath = 'foo/';
    protected $streamShared = false;

    /** @var ResourceLocation */
    protected $location;

    protected $locationName = 'bar';
    protected $locationPath = 'bar/';

    public function setUp(): void
    {
        parent::setup();

        $this->stream = new ResourceStream($this->streamScheme, $this->streamPrefix, $this->streamPath, $this->streamShared);
        $this->location = new ResourceLocation($this->locationName, $this->locationPath);
    }

    /**
     * @return \UserFrosting\UniformResourceLocator\Resource
     */
    public function testConstructor(): Resource
    {
        $resource = new Resource($this->stream, $this->location, $this->streamPath.'test.txt', 'basePath/');
        $this->assertInstanceOf(ResourceInterface::class, $resource);

        return $resource;
    }

    /**
     * @depends testConstructor
     *
     * @param \UserFrosting\UniformResourceLocator\Resource $resource
     */
    public function testGetStreamAndGetLocation(Resource $resource): void
    {
        $this->assertEquals($this->stream, $resource->getStream());
        $this->assertEquals($this->location, $resource->getLocation());
    }

    /**
     * @depends testConstructor
     *
     * @param \UserFrosting\UniformResourceLocator\Resource $resource
     */
    public function testGetSetLocatorBasePath(Resource $resource): void
    {
        $this->assertSame('basePath/', $resource->getLocatorBasePath());
        $resource->setLocatorBasePath('pathBase/');
        $this->assertSame('pathBase/', $resource->getLocatorBasePath());
    }

    /**
     * @dataProvider resourcesProvider
     *
     * @param bool   $useLocation
     * @param string $path
     * @param string $basePath
     */
    public function testGetBasePath($useLocation, $path, $basePath): void
    {
        // Can't be done in resourcesProvider, as `setUp` is called after
        if ($useLocation) {
            $location = $this->location;
            $locationPath = $this->locationPath;
        } else {
            $location = null;
            $locationPath = '';
        }

        $resource = new Resource($this->stream, $location, $locationPath.$this->streamPath.$path, $basePath);

        // getBasePath
        $this->assertSame($path, $resource->getBasePath());

        // Test `getUri` as the too are connected
        $this->assertSame($this->streamScheme.'://'.$path, $resource->getUri());

        // Test `getAbsolutePath` and `__toString`
        $basePath = Normalizer::normalizePath($basePath);
        $this->assertSame($basePath.$locationPath.$this->streamPath.$path, $resource->getAbsolutePath());
        $this->assertSame($resource->getAbsolutePath(), (string) $resource);
    }

    /**
     * Data provider for testGetBasePath.
     *
     * Return a list of basepath to test. The rela rel path will be constructed by the
     * test according to the stream used, so we'll asume the rel path are always
     * correct. Also mix and match three provider : path, basePath and useLocation (true/false)
     */
    public function resourcesProvider(): array
    {
        $paths = [
            '',                       // No stream part. Shouldn't happen in real life
            'test.txt',               // No stream part. Shouldn't happen in real life
            'data/test.txt',          // No stream part. Shouldn't happen in real life
            'bar/foo/test.txt',       // foo stream inside the bar location. Location always comes first
            'foo/bar/test.txt',       // We remove the foo, as it's the base stream, but keep the bar, as it's not a location.
            'foo/bar/foo/test.txt',   // We remove the foo, as it's the base stream, but keep the bar, as it's not a location.
            'foo/test.txt',           // `foo/` is removed, because it's the stream part
            'foo/foo/test.txt',       // The first `foo/` is removed, because it's the stream part, the other should be kept
            'bar/test.txt',           // No stream part. Shouldn't happen in real life. Bar should be kept
            'foo/',                   // With out extensions
            'foo',
            'foo/foo/',
            'foo/foo',
        ];

        $basePaths = [
            '',
            '/',
            '\\',
            'C:\\',
            'C:\\BasePath\\',
            'C:\\BasePath',
            'BasePath/',
            '/BasePath/',
            '/BasePath',
        ];

        $data = [];

        foreach ($paths as $path) {
            foreach ($basePaths as $basepath) {
                $data[] = [true, $path, $basepath];
                $data[] = [false, $path, $basepath];
            }
        }

        return $data;
    }

    /**
     * @dataProvider sharedResourceStreamProvider
     *
     * @param string $path
     */
    public function testSharedResourceStream($path): void
    {
        $stream = new ResourceStream('cars', '', $path, true);
        $resource = new Resource($stream, null, $path);

        $this->assertInstanceOf(ResourceInterface::class, $resource);
        $this->assertEquals($path, $resource);
        $this->assertEquals($path, $resource->getPath());
        $this->assertNull($resource->getLocation());
        $this->assertEquals('cars://', $resource->getUri());
        $this->assertInstanceOf(ResourceStreamInterface::class, $resource->getStream());
    }

    /**
     * Data provider for testSharedResourceStream.
     *
     * Test different placement of slashes to make sure getUri and getBasePath
     * returns the correct path
     */
    public function sharedResourceStreamProvider(): array
    {
        return [
            ['Garage/cars'],
            ['Garage/cars/'],
            ['Garage'],
            ['Garage/'],
            ['/Garage/cars'],
            ['/Garage/cars/'],
            ['/'],
            [''],
        ];
    }

    /**
     * @dataProvider FilesProvider
     *
     * @param string $path
     * @param string $expectedBasename
     * @param string $expectedFilename
     * @param string $expectedExtension
     */
    public function testFilePropertiesGetters($path, $expectedBasename, $expectedFilename, $expectedExtension): void
    {
        $resource = new Resource($this->stream, $this->location, $this->locationPath.$this->streamPath.$path);

        $this->assertSame($expectedBasename, $resource->getBasename());
        $this->assertSame($expectedFilename, $resource->getFilename());
        $this->assertSame($expectedExtension, $resource->getExtension());
    }

    /**
     * Data provider for testFilePropertiesGetters.
     *
     * Return a list of relPath to test. The abs path will be constructed by the
     * test according to the stream used, so we'll asume the abs path are always
     * correct. In any case, relPath == basePath (always).
     */
    public function FilesProvider(): array
    {
        return [
            // RelPath, basename, filename, extension
            ['test.txt', 'test.txt', 'test', 'txt'],
            ['/foo/test.txt', 'test.txt', 'test', 'txt'],
            ['C:\\foo\\test.txt', 'test.txt', 'test', 'txt'],
            ['foo/test.txt', 'test.txt', 'test', 'txt'],
            ['/test.txt', 'test.txt', 'test', 'txt'],
            ['lib.inc.php', 'lib.inc.php', 'lib.inc', 'php'],
        ];
    }
}
