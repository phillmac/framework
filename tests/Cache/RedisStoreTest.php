<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Cache;

use UserFrosting\Cache\RedisStore;

/**
 * @requires extension redis
 */
class RedisStoreTest extends StoreTestCase
{
    /** {@inheritdoc} */
    protected function createStore()
    {
        // Create $cache with default config in CI, and tweaked in Lando
        $config = [];
        if (getenv('LANDO') === 'ON') {
            $config['host'] = 'redis-cache';
        }
        $cacheStore = new RedisStore($config);

        return $cacheStore->instance();
    }

    /**
     * Test redis store.
     */
    public function testRedisStore()
    {
        $cache = $this->createStore();

        // Store "foo" and try to read it
        $cache->forever('foo', 'Redis bar');
        $this->assertEquals('Redis bar', $cache->get('foo'));
    }

    public function testRedisStorePersistence()
    {
        $cache = $this->createStore();

        // Doesn't store anything, just tried to read the last one
        $this->assertEquals('Redis bar', $cache->get('foo'));
    }

    public function testMultipleRedisStore()
    {
        $cache = $this->createStore();

        // Store stuff in first
        $cache->tags('global')->forever('test', '1234');
        $cache->tags('global')->forever('foo', 'bar');
        $cache->tags('global')->forever('cities', ['Montréal', 'Paris', 'NYC']);

        // Store stuff in second
        $cache->tags('user')->forever('test', '1234');
        $cache->tags('user')->forever('foo', 'BARRRRRRRRE');
        $cache->tags('user')->forever('cities', ['Montréal', 'Paris', 'NYC']);

        // Flush first
        $cache->tags('global')->flush();

        // First show be empty, but not the second one
        $this->assertEquals(null, $cache->tags('global')->get('foo'));
        $this->assertEquals('BARRRRRRRRE', $cache->tags('user')->get('foo'));
    }

    public function testTagsFlush()
    {
        $cache = $this->createStore();

        // Start by not using tags
        $cache->put('test', '123', 60);
        $this->assertEquals('123', $cache->get('test'));
        $this->assertTrue($cache->flush());
        $this->assertNull($cache->get('test'));

        // Try again with tags
        $cache->tags('blah')->put('blah', '321', 60);
        $this->assertEquals('321', $cache->tags('blah')->get('blah'));
        $cache->tags('blah')->flush(); // $this->assertTrue($cache->tags('blah')->flush()); <-- Returns null pre 5.7
        $this->assertNull($cache->tags('blah')->get('blah'));
    }
}
