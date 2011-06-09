<?php

class Docgen_Cache {
    private static $class_info_cache = null;

    /**
     * A Docgen_Cache object intended to hold class template
     * information.
     *
     * This serves to both speed up the application and avoid any
     * hooks being fired more times than they need to be.
     */
    public static function getClassInfoCache() {
        if (is_null(self::$class_info_cache)) {
            self::$class_info_cache = new Docgen_Cache();
        }

        return self::$class_info_cache;
    }

    private $cache = array();

    /**
     * Add a key/value pair to the cache. Keys can be anything that
     * will act as a key in an associative array and values can be anything.
     *
     * @param mixed $key The key you want to save your information under.
     * @param mixed $value The information you want to save.
     */
    public function add($key, $value) {
        $this->cache[$key] = $value;
    }

    /**
     * Gets a previously saved value by its key. If there is nothing stored
     * under the key supplied, null is returned.
     *
     * @param mixed $key The key to search for.
     * @return mixed The value stored under the specified key or null if
     * the key does not exist.
     */
    public function get($key) {
        if ($this->exists($key)) {
            return $this->cache[$key];
        } else {
            return null;
        }
    }

    /**
     * Returns true if a key exists in the cache, false otherwise.
     */
    public function exists($key) {
        return isset($this->cache[$key]);
    }
}
