<?php

namespace AmiLabs\DevKit\Logging\DataAccess;

use PDO;
use PDOStatement;
use AmiLabs\DevKit\DataAccessPDO;
use AmiLabs\DevKit\Logging\IDataAccessLayer;

/**
 * Data Access Layer.
 *
 * @package AmiLabs\DevKit\Logging\DataAccess
 */
class SQLite extends DataAccessPDO implements IDataAccessLayer{
    /**
     * Service name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $aOptions;

    /**
     * @var PDOStatement
     */
    protected $oStmtCreateLink;

    /**
     * @var PDOStatement
     */
    protected $oStmtDeleteLink;

    /**
     * @var PDOStatement
     */
    protected $oStmtLinkByKey;

    /**
     * @var PDOStatement
     */
    protected $oStmtLinkByValue;





    /**
     * @param string $name
     * @param array  $aOptions
     */
    public function __construct($name, array $aOptions){
        $this->name     = $name;
        $this->aOptions = $aOptions;

        $this->connect($aOptions['AmiLabs\\DevKit\\Logging\\DataAccess']);

        $query =
            "INSERT INTO `logging_service_link` " .
            "(`date`, `service`, `key`, `value`) " .
            "VALUES " .
            "(:date, :service, :key, :value)";
        $this->oStmtCreateLink = $this->oDB->prepare($query);

        $query =
            "DELETE FROM `logging_service_link` " .
            "WHERE `key` = :key";
        $this->oStmtDeleteLink = $this->oDB->prepare($query);

        $query =
            "SELECT `value` FROM `logging_service_link` " .
            "WHERE " .
                "`service` = :service AND " .
                "`key` = :key " .
                "LIMIT 1";
        $this->oStmtLinkByKey = $this->oDB->prepare($query);

        $query =
            "SELECT `value` FROM `logging_service_link` " .
            "WHERE " .
                "`service` = :service AND " .
                "`value` = :value " .
                "LIMIT 1";
        $this->oStmtLinkByValue = $this->oDB->prepare($query);
    }

    /**
     * Creates link.
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function createLink($key, $value){
        $aRecord = array(
            'date'  => date('Y-m-d H:i:s'),
            'key'   => $key,
            'value' => $value,
        );
        $this->prepareRecord($aRecord);
        $this->oStmtCreateLink->execute($aRecord);
    }

    /**
     * Deletes link.
     *
     * @param  string $key
     * @return void
     */
    public function deleteLink($key){
        $aRecord = array(
            'key' => $key,
        );
        $this->prepareRecord($aRecord);
        $this->oStmtDeleteLink->execute($aRecord);
    }

    /**
     * Returns link by key.
     *
     * @param  string $key
     * @return mixed  NULL if not found
     */
    public function getLinkByKey($key){
        $aRecord = array(
            'key' => $key,
        );
        $this->prepareRecord($aRecord);
        $this->oStmtLinkByKey->execute($aRecord);
        $aRow = $this->oStmtKey->fetch(PDO::FETCH_ASSOC);

        return $aRow ? $aRow : NULL;
    }

    /**
     * Returns link by value.
     *
     * @param  string $value
     * @return mixed  NULL if not found
     */
    public function getLinkByValue($value){
        $aRecord = array(
            'value' => $value,
        );
        $this->prepareRecord($aRecord);
        $this->oStmtLinkByKey->execute($aRecord);
        $aRow = $this->oStmtKey->fetch(PDO::FETCH_ASSOC);

        return $aRow ? $aRow : NULL;
    }

    /**
     * Writes data to log.
     *
     * @param  string $key
     * @param  array  $aMeta
     * @param  mixed  $data
     * @param  int    $level
     * @return void
     */
    public function write($key, array $aMeta, $data, $level = self::DEBUG){

    }

    /**
     * Returns data from log.
     *
     * @param  string $key
     * @param  int    $level
     * @return array
     */
    public function get($key, $level = self::ALL){

    }

    /**
     * Cleanups data from log.
     *
     * @param  string $key
     * @return void
     */
    public function cleanup($key){

    }
}
