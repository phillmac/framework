<?php

/*
 * UserFrosting Uniform Resource Locator (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UniformResourceLocator
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/UniformResourceLocator/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\UniformResourceLocator;

/**
 * Tests for ResourceLocator.
 */
class BuildingLocatorWithBasePathTest extends BuildingLocatorTest
{
    /** @var string */
    protected $basePath = __DIR__.'/Building';
}
