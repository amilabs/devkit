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

    /**
     * Sort an array by column preserving row keys.
     *
     * @param  array &$aArray       Array
     * @param  int|string $sortCol  Sort column
     * @param  int $sort            Sort type: SORT_STRING|SORT_REGULAR|SORT_NUMERIC
     * @param  int $direction       Sort direction: SORT_ASC|SORT_DESC
     * @return void
     */
    public static function sortMultiArrayPreserveKeys(
        array &$aArray,
        $sortCol,
        $sort = SORT_STRING,
        $direction = SORT_ASC
    ){
        if(!sizeof($aArray)){
            return;
        }
        $aIndex = array();
        $i = 0;
        foreach($aArray as $key => $aRow){
            $aIndex['pos'][$i]  = $key;
            $aIndex['name'][$i] = $aRow[$sortCol];
            ++$i;
        }
        array_multisort($aIndex['name'], $sort, $direction, $aIndex['pos']);
        $aRes = array();
        for($j = 0; $j < $i; $j++){
            $aRes[$aIndex['pos'][$j]] = $aArray[$aIndex['pos'][$j]];
        }
        $aArray = $aRes;
    }
}
