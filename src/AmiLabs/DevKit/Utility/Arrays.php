<?php

namespace AmiLabs\DevKit\Utility;

/**
 * Arrays utility class.
 *
 * @package AmiLabs/DevKit/Utility
 */
class Arrays{
    /**
     * Finds index of row containig passed key and value.
     *
     * @param  array        $aArray
     * @param  string | int $key
     * @param  mixed        $value
     * @param  bool         $strict
     * @return int | FALSE
     */
    public static function findByKeyValue(array $aArray, $key, $value, $strict = FALSE){
        foreach($aArray as $index => $aRow){
            if(
                isset($aRow[$key]) &&
                ($strict ? $aRow[$key] === $value : $aRow[$key] == $value)
            ){
                return $index;
            }
        }

        return FALSE;
    }
}
