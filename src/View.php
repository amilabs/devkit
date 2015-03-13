<?php

namespace AmiLabs\DevKit;

/**
 * Simple View realization.
 */
class View {
    /**
     * View data (variables that are used in a template files or other output)
     *
     * @var array
     */
    protected $aData = array();
    /**
     * Global variables.
     *
     * @var array
     */
    protected static $aGlobalData = array();
    /**
     * Returns view variable.
     *
     * @param string $name    Variable name
     * @param mixed $default  Default value to return if variable was not set
     * @return mixed
     */
    public function get($name, $default = null){
        $aScope = $this->getScope();
        return isset($aScope[$name]) ? $aScope[$name] : $default;
    }
    /**
     * Sets view variable.
     *
     * @param string $var     Variable name
     * @param mixed $value    Variable value
     * @param bool $isGlobal  Variable will be set in global scope if set to true
     * @return View
     */
    public function set($var, $value = null, $isGlobal = false){
        if($isGlobal){
            self::$aGlobalData[$var] = $value;
        }else{
            $this->aData[$var] = $value;
        }
        return $this;
    }
    /**
     * Sets view variables.
     *
     * @param array $aVars    Array of view variables
     * @param bool $isGlobal  Variables will be set in global scope if set to true
     * @return View
     */
    public function setScope(array $aVars, $isGlobal = false){
        if($isGlobal){
            self::$aGlobalData = array_merge_recursive(self::$aGlobalData, $var);
        }else{
            $this->aData = array_merge_recursive($this->aData, $var);
        }
        return $this;
    }
    /**
     * Returns view scope.
     *
     * @return array
     */
    public function getScope(){
        return $this->aData + $this->getGlobalScope();
    }
    /**
     * Returns view global scope.
     *
     * @return array
     */
    public function getGlobalScope(){
        return self::$aGlobalData;
    }
}