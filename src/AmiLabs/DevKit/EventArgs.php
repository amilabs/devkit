<?php

namespace AmiLabs\DevKit;

/**
 * Event arguments.
 */
class EventArgs{
    /**
     * Arguments.
     *
     * @var array
     */
    protected $aArgs;

    /**
     * @param array $aArgs  Initial arguments
     */
    public function __construct(array $aArgs = array()){
        $this->aArgs = $aArgs;
    }

    /**
     * Sets argument value.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value){
        $this->aArgs[$name] = $value;
    }

    /**
     * Returns TRUE if argument exists, FALSE otherwise.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name){
        $result = array_key_exists($name, $this->aArgs);

        return $result;
    }

    /**
     * Returns TRUE if argument value is empty, FALSE otherwise.
     *
     * @param  string $name
     * @return bool
     * @see    http://php.net/manual/en/function.empty.php
     */
    public function isEmpty($name){
        $result = empty($this->aArgs[$name]);

        return $result;
    }

    /**
     * Returns argument value.
     *
     * @param  string $name     If not passed, all arguments will be returned
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name = NULL, $default = NULL){
        if(is_null($name)){
            $result = $this->aArgs;
        }else{
            $result =
                $this->exists($name)
                    ? $this->aArgs[$name]
                    : $default;
        }

        return $result;
    }

    /**
     * Deletes argument.
     *
     * @param  string $name
     * @return void
     */
    public function delete($name){
        unset($this->aArgs[$name]);
    }
}
