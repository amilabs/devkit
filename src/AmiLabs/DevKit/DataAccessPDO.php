<?php

namespace AmiLabs\DevKit;

use InvalidArgumentException;
use PDO;
use PDOStatement;

/**
 * PDO layer.
 *
 * @package AmiLabs\DevKit
 */
abstract class DataAccessPDO{
    /**
     * @var PDO
     */
    protected $oDB;

    /**
     * Connects to data source.
     *
     * @param  array $aConfig
     * @return void
     */
    protected function connect(array $aConfig){
        $this->oDB = new PDO(
            $aConfig['dsn'],
            $aConfig['user'],
            $aConfig['password'],
            $aConfig['options']
        );
    }

    /**
     * Prepends ':' to array keys.
     *
     * @param  array &$aRecord
     * @return void
     */
    protected function prepareRecord(array &$aRecord){
        $aKeys = array_keys($aRecord);
        foreach($aKeys as $key){
            $aRecord[':' . $key] = $aRecord[$key];
            unset($aRecord[$key]);
        }
    }

    /**
     * Returns filterring SQL part.
     *
     * Example:
     * <code>
     * $aFilter = array(
     *     array(
     *         'field' => 'id',
     *         'value' => array(1, 2, 3)
     *     ),
     *     array(
     *         'field' => 'first_field',
     *         'value' => 'first_value',
     *         'glue'  => 'AND' // by default
     *     ),
     *     array(
     *         'condition' => array(
     *             array(
     *                 'field' => 'second_field',
     *                 'value' => 'second_value',
     *                 'glue'  => 'OR'
     *             ),
     *             array(
     *                 'field' => 'third_field',
     *                 'value' => 'third_value'
     *             ),
     *         )
     *     )
     * );
     * $query =
     *      "SELECT * " .
     *      "FROM `table` " .
     *      "WHERE " . $this->getFilterSQL($aFilter);
     * $oStmt = $this->oDB->prepare($query);
     * $index = 0;
     * $this->bindFilterValues($oStmt, $aFilter, $index);
     * $oStmt->execute();
     * $aRows = $oStmt->fetchAll(PDO::FETCH_ASSOC);
     * </code>
     * $query will contain:
     * <code>
     * SELECT *
     * FROM `table`
     * WHERE
     *     1 AND
     *     `id` IN (?, ?, ?) AND
     *     `first_field` = ? AND
     *     (0 OR `second_field` = ? AND `third_field` = ?)
     * </code>
     *
     * @param  array  $aFilter
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getFilterSQL(array $aFilter){
        $query = '';
        foreach($aFilter as $index => $aField){
            $glue = isset($aField['glue']) ? $aField['glue'] : 'AND';
            if(!$index){
                $query .= ($glue == 'AND' ? 1 : 0) . ' ';
            }
            if(isset($aField['condition'])){
                $query .= $glue . ' (' . $this->getFilterSQL($aField['condition']) . ') ';
                continue;
            }
            foreach(array('field', 'value') as $key){
                if(!isset($aField[$key])){
                    throw new InvalidArgumentException(
                        sprintf("Missing obligatory key '%s'", $key)
                    );
                }
            }
            $field = $aField['field'];
            $value = $aField['value'];
            $op = isset($aField['op']) ? $aField['op'] : '=';
            if(is_array($value)){
                if(sizeof($value)){
                    $query .=
                        $glue . " `" . $field . "` IN (" .
                        str_repeat('?, ', sizeof($value) - 1) . "?) ";
                }else{
                    throw new InvalidArgumentException(
                        sprintf("Passed '%s' field is empty array", $field)
                    );
                }
            }else{
                $query .=
                    sprintf(
                        "%s `%s` %s %s",
                        $glue,
                        $field,
                        $op,
                        '=!' != substr($value, 0, 2) ? '?' : substr($value, 2)
                    );
            }
        }

        return $query;
    }

    /**
     * Binds filter values.
     *
     * @param  PDOStatement $oStmt
     * @param  array        $aFilter
     * @param  int          &$index    Current field index
     * @param  int          $type      PDO parameter type
     * @return void
     * @see    self::getFilterSQL()
     */
    protected function bindFilterValues(
        PDOStatement $oStmt,
        array $aFilter,
        &$index,
        $type = PDO::PARAM_STR
    ){
        foreach($aFilter as $aField){
            if(isset($aField['condition'])){
                $this->bindFilterValues(
                    $oStmt,
                    $aField['condition'],
                    $index,
                    $type
                );
            }else{
                $value = $aField['value'];
                if(is_array($value)){
                    foreach($value as $val){
                        $oStmt->bindValue(++$index, $val);
                    }
                }else{
                    if('=!' != substr($value, 0, 2)){
                        $oStmt->bindValue(++$index, $value);
                    }
                }
            }
        }
    }

    /**
     * Sanitizes field name.
     *
     * @param  string $field
     * @return string
     */
    protected function sanitizeFieldName($field){
        return str_replace(array('`', '"', "'"), '', $field);
    }
}
