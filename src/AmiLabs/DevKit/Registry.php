<?php
/**
 * 
 */
namespace AmiLabs\DevKit;

/**
 * Registry class.
 */
class Registry {
    /**
     * Indicates registry root.
     */
    const ROOT = false;
    /**
     * Overwrite all existing keys.
     */
    const OVERWRITE = 0;
    /**
     * Append new keys only.
     */
    const APPEND = 1;
    /**
     * Set value and persist key.
     */
    const PERSIST = 2;
    /**
     * Registry storage instances
     *
     * @var array
     */
    protected static $aInstances = array();
    /**
     * Current project storage name
     *
     * @var string
     */
    protected $storageName;
    /**
     * Registry data
     *
     * @var array
     */
    protected $aData = array();
    /**
     * List of entities in registry storage which cannot be changed anymore.
     *
     * @see \AmiLabs\DevKit\Registry::persist()
     * @var array 
     */
    protected $aPersistents = array();
    /**
     * Adds new storage with specified name.
     *
     * @param string $name  Storage name
     * @return \AmiLabs\DevKit\Registry
     * @throws Exception
     */
    public static function addStorage($name){
        if(!isset(self::$aInstances[$name])){
            self::$aInstances[$name] = new self();
        }else{
            throw new \Exception('Storage with name "' . $name . '" already added');
        }
        return self::$aInstances[$name];
    }
    /**
     * Returns an existing storage with specified name.
     *
     * @param string $name  Storage name
     * @return \AmiLabs\DevKit\Registry
     * @throws Exception
     */
    public static function useStorage($name){
        if(!isset(self::$aInstances[$name])){
            throw new \Exception('Storage with name "' . $name . '" does not exist');
        }
        return self::$aInstances[$name];
    }
    /**
     * Returns true if specified key exists.
     *
     * @param string $key
     * @return boolean
     */
    public function exists($key){
        return isset($this->aData[$key]);
    }
    /**
     * Returns value of data stored in registry.
     *
     * @param string $key     Data entry name
     * @param mixed $default  Default value if key was not set
     * @return mixed
     */
    public function get($key = self::ROOT, $default = null){
        $aData = $this->aData;
        if(strpos($key, '/') !== FALSE){
            $aKeys = explode('/', $key);
            foreach($aKeys as $subKey){
                if(isset($aData[$subKey])){
                    $aData = $aData[$subKey];
                }else{
                    return $default;
                }
            }
            return $aData;
        }
        return (self::ROOT === $key) ? $aData : ($this->exists($key) ? $aData[$key] : $default);
    }
    /**
     * Stores a value in registry by specified name.
     *
     * @param string $key   Data entry name, self::ROOT write the value over registry root
     * @param mixed $value  Data value, must be of array type if written to the root
     * @param int $mode     Write mode: combination of flags Registry::REWRITE, Registry::APPEND, Registry::PERSIST
     * @see \AmiLabs\DevKit\Registry::REWRITE
     * @see \AmiLabs\DevKit\Registry::APPEND
     * @see \AmiLabs\DevKit\Registry::PERSIST
     */
    public function set($key, $value, $mode = self::OVERWRITE){
        if($this->isPersistant($key)){
            throw new \Exception('Attempt of writting to a read-only registry record');
        }
        if(self::ROOT === $key){
            if(!is_array($value)){
                throw new \Exception('Only array could be written to the registry root');
            }
            if($mode & self::APPEND){
                $this->aData += $value;
            }else{
                $this->aData = array_merge($this->aData, $value);
            }
        }else{
            if(!($mode & self::APPEND)){
                $this->aData[$key] = $value;
            }
        }
        if($mode & self::PERSIST){
            $this->persist($key);
        }
    }
    /**
     * Removes specified entity from storage.
     *
     * @param string $key  Registry key
     * @return boolean
     */
    public function remove($key){
        $result = false;
        if($this->exists($key) && !$this->isPersistant()){
            unset($this->aData[$key]);
            $result = true;
        }
        return $result;
    }
    /**
     * Makes registry entry readonly.
     *
     * @param mixed $key  Registry key, if not set or set to Registry::ROOT
     * @see \AmiLabs\DevKit\Registry::ROOT
     */
    public function persist($key = self::ROOT){
        if(is_array($key)){
            $this->aPersistents += $key;
        }else{
            $this->aPersistents[] = $key;
        }
        $this->aPersistents = array_unique($this->aPersistents);
        if(in_array(self::ROOT, $this->aPersistents)){
            $this->aPersistents = array(self::ROOT);
        }
    }
    /**
     * Returns true if specified entity is read-only.
     *
     * @param mixed $key  Registry key, if not set - checks whole registry
     * @return boolean
     */
    public function isPersistant($key = self::ROOT){
        return in_array(self::ROOT, $this->aPersistents) || in_array($key, $this->aPersistents);
    }
}
