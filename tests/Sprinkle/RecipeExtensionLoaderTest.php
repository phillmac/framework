<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use DI\Container;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Sprinkle\RecipeExtensionLoader;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Support\Exception\NotFoundException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class RecipeExtensionLoaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Purpose of this test is to create a reusable $loader instance so the
     * same code is not reused too much.
     */
    public function testConstructor(): RecipeExtensionLoader
    {
        $ci = Mockery::mock(Container::class);
        $manager = Mockery::mock(SprinkleManager::class);

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->assertInstanceOf(RecipeExtensionLoader::class, $loader);

        return $loader;
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidate(RecipeExtensionLoader $loader): void
    {
        $this->assertTrue($loader->validateClass(RecipeExtensionLoaderStub::class));
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithInterface(RecipeExtensionLoader $loader): void
    {
        $isValid = $loader->validateClass(SprinkleStub::class, SprinkleRecipe::class);
        $this->assertTrue($isValid);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithSubclass(RecipeExtensionLoader $loader): void
    {
        $isValid = $loader->validateClass(RecipeExtensionLoaderStubExtended::class, RecipeExtensionLoaderStub::class);
        $this->assertTrue($isValid);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadSubclass(RecipeExtensionLoader $loader): void
    {
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(\stdClass::class, RecipeExtensionLoaderStub::class);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateClassNotFound(RecipeExtensionLoader $loader): void
    {
        $this->expectException(NotFoundException::class);
        $loader->validateClass(Bar::class);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadInterface(RecipeExtensionLoader $loader): void
    {
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(SprinkleStub::class, ContainerInterface::class);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testValidate
     */
    public function testGetInstances(): void
    {
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')
            ->with(RecipeExtensionLoaderStub::class)
            ->once()
            ->andReturn(new RecipeExtensionLoaderStub())
            ->getMock();

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo');

        $this->assertIsArray($instances);
        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * Make sure recipeInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetInstancesWithBadRecipeInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getInstances('getFoo', recipeInterface: ContainerInterface::class);
    }

    /**
     * Make sure extensionInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetInstancesWithBadExtensionInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getInstances('getFoo', extensionInterface: SprinkleRecipe::class);
    }
}

class RecipeExtensionLoaderStub
{
}

class RecipeExtensionLoaderStubExtended extends RecipeExtensionLoaderStub
{
}

class SprinkleStub extends TestSprinkle
{
    public static function getFoo(): array
    {
        return [
            RecipeExtensionLoaderStub::class
        ];
    }
}