<?php

namespace AmiLabs\DevKit;

/**
 * Abstract Cache class.
 */
abstract class Cache {
    /**
     * Cache stored in files.
     */
    const FILE = 1;
    /**
     * Used caches
     *
     * @var array
     */
    protected static $aCaches = array();
    /**
     * Returns a named cache entry.
     *
     * @param string $name  Cache entry name
     * @param int $type     Cache type
     * @return \AmiLabs\DevKit\FileCache
     */
    public static function get($name, $type = Cache::FILE){
        if(!isset(self::$aCaches[$name])){
            switch($type){
                default:
                    self::$aCaches[$name] = new FileCache($name);
            }
        }
        return self::$aCaches[$name];
    }
}
/**
 * Cache driver interface.
 */
interface ICache {
    /**
     * Constructor.
     *
     * @param string $name  Cache entry name
     */
    public function __construct($name);
    /**
     * Check if cached information exists.
     */
    public function exists();
    /**
     * Loads cached data.
     */
    public function load();
    /**
     * Returns cached data.
     */
    // public function get();
    /**
     * Stores cached data.
     *
     * @param mixed $data
     */
    public function save($data);
    /**
     * Clears cached data.
     */
    public function clear();
}
/**
 * File cache driver.
 */
class FileCache implements ICache{
    /**
     * Cached data
     *
     * @var mixed
     */
    // protected $cachedData;

    /**
     * Cache filename
     *
     * @var string
     */
    protected $fileName;

    /**
     * Constructor.
     *
     * @todo Sanitize name
     * @param string $name  Cache entry name
     */
    public function __construct($name){
        $this->fileName = PATH_TMP . '/' . $name . '_cache.tmp';
    }

    /**
     * Returns true if cache file exists and is readable.
     *
     * @return bool
     */
    public function exists(){
        return file_exists($this->fileName) && is_readable($this->fileName);
    }

    /**
     * Loads cached data.
     *
     * @return mixed
     */
    public function load(){
        // $this->cachedData = file_get_contents($this->fileName);
        // return $this->cachedData;
        return file_get_contents($this->fileName);
    }
    /**
     * Returns cached data.
     *
     * @return mixed
     */
    /*
    public function get(){
        if(is_null($this->cachedData)){
            $this->load();
        }
        return $this->cachedData;
    }
    */

    /**
     * Saves cached data.
     *
     * @param mixed $data
     */
    public function save($data){
        file_put_contents($this->fileName, $data);
        chmod($this->fileName, 0777);
    }

    /**
     * Clears cached data.
     */
    public function clear(){
        if($this->exists()){
            unlink($this->fileName);
        }
    }
}