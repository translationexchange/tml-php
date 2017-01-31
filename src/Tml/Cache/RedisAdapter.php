<?php

/**
 * Copyright (c) 2017 Translation Exchange, Inc. https://translationexchange.com
 *
 *  _______                  _       _   _             ______          _
 * |__   __|                | |     | | (_)           |  ____|        | |
 *    | |_ __ __ _ _ __  ___| | __ _| |_ _  ___  _ __ | |__  __  _____| |__   __ _ _ __   __ _  ___
 *    | | '__/ _` | '_ \/ __| |/ _` | __| |/ _ \| '_ \|  __| \ \/ / __| '_ \ / _` | '_ \ / _` |/ _ \
 *    | | | | (_| | | | \__ \ | (_| | |_| | (_) | | | | |____ >  < (__| | | | (_| | | | | (_| |  __/
 *    |_|_|  \__,_|_| |_|___/_|\__,_|\__|_|\___/|_| |_|______/_/\_\___|_| |_|\__,_|_| |_|\__, |\___|
 *                                                                                        __/ |
 *                                                                                       |___/
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Tml\Cache;

use Redis;
use Tml\Config;

/**
 * Class RedisAdapter
 *
 * Cache adapter based on Redis
 *
 * @package Tml\Cache
 */
class RedisAdapter extends Base {

    /**
     * Stores the Redis object
     *
     * @var \Memcache
     */
    protected $cache;

    /**
     * Creates Redis adapter
     */
    public function __construct() {
        $this->cache = new Redis;
        if (Config::instance()->configValue("socket")) {
            $this->cache->connect(Config::instance()->configValue("socket"));
        } else {
            $this->cache->connect(
                Config::instance()->configValue("cache.host", 'localhost'),
                Config::instance()->configValue("cache.port", 6379)
            );
        }
    }

    /**
     * Returns adapter key
     *
     * @return string
     */
    public function key() {
        return "redis";
    }

    /**
     * Fetches data from Redis
     *
     * @param $key
     * @param null $default
     * @return bool|null|string
     */
    public function fetch($key, $default = null) {
        $value = $this->cache->get($key);
        if ($value) {
            $this->info("Cache hit " . $key);
            return $value;
        }

        $this->info("Cache miss " . $key);

        if ($default == null)
            return null;

        if (is_callable($default)) {
            $value = $default();
        } else {
            $value = $default;
        }

        $this->store($key, $value);

        return $value;
    }

    /**
     * Stores data in Redis
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function store($key, $value) {
        $this->info("Cache store " . $key);
        // $this->info($value);

        return $this->cache->set(
            $key,
            $this->stripExtensions($value)
        );
    }

    /**
     * Checks if key exists in Redis
     * 
     * @param $key
     * @return bool
     */
    public function exists($key) {
        $this->info("Cache exists " . $key);
        return $this->cache->exists($key);
    }

    /**
     * Deletes data from Memcache
     *
     * @param $key
     * @return bool
     */
    public function delete($key) {
        $this->info("Cache delete " . $key);
        return $this->cache->delete($key);
    }
}
