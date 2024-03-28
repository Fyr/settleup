<?php

class Application_Model_Cache
{
    protected static $_enabled = false;
    protected static $_cache;
    protected static $_prefix = 'pfleet_cache_';

    /**
     * initialize cache
     *
     * @static
     * @param $enabled
     * @param $dir - directory for cache storage
     * @param int $lifetime - cache lifetime
     */
    public static function init($enabled, $dir, $lifetime = 7200)
    {
        self::$_enabled = $enabled;

        if (self::$_enabled) {
            $frontendOptions = [
                'lifetime' => $lifetime,
                'automatic_serialization' => true,
            ];

            $backendOptions = [
                'cache_dir' => $dir,
                'file_name_prefix' => self::$_prefix,
                'hashed_directory_level' => 2,
            ];

            self::$_cache = Zend_Cache::factory(
                'Core',
                'File',
                $frontendOptions,
                $backendOptions
            );
        }
    }

    /**
     * return cache instance
     *
     * @static
     * @return bool
     */
    public static function getInstance()
    {
        if (self::$_enabled == false) {
            return false;
        }

        return self::$_cache;
    }

    /**
     * retrieve object from cache
     *
     * @static
     * @param $keyName
     * @return false|mixed
     */
    public static function load($keyName)
    {
        if (self::$_enabled == false) {
            return false;
        }

        return self::$_cache->load($keyName);
    }

    /**
     * save object to cache
     *
     * @static
     * @param $keyName
     * @param $dataToStore
     * @return bool
     */
    public static function save($keyName, $dataToStore)
    {
        if (self::$_enabled == false) {
            return true;
        }

        return self::$_cache->save($dataToStore, $keyName);
    }

    /**
     * clean cache
     *
     * @static
     * @return mixed
     */
    public static function clean()
    {
        if (self::$_enabled == false) {
            return;
        }

        self::$_cache->clean();
    }
}
