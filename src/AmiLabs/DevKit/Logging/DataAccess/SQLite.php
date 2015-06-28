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
    protected $service;

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
     * @var PDOStatement
     */
    protected $oStmtWrite;

    /**
     * @var PDOStatement
     */
    protected $oStmtCleanup;

    /**
     * @param array  $aOptions
     */
    public function __construct(array $aOptions){
        $this->service  = $aOptions['serviceName'];
        $this->aOptions = $aOptions;

        $this->connect($aOptions['DataAccess']);

        $query =
            "INSERT INTO `logging_service_link` " .
            "(`date`, `service`, `key`, `value`) " .
            "VALUES " .
            "(:date, :service, :key, :value)";
        $this->oStmtCreateLink = $this->oDB->prepare($query);

        $query =
            "DELETE FROM `logging_service_link` " .
            "WHERE `service` = :service AND `key` = :key";
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

        $query =
            "INSERT INTO `logging_service_data` " .
            "(`uid`, `date`, `service`, `key`, `type`, `data`) " .
            "VALUES " .
            "(:uid, :date, :service, :key, :type, :data)";
        $this->oStmtWrite = $this->oDB->prepare($query);

        $query =
            "DELETE FROM `logging_service_data` " .
            "WHERE " .
                "`service` = :service AND " .
                "`key` = :key ";
        $this->oStmtCleanup = $this->oDB->prepare($query);
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
            'date'    => date('Y-m-d H:i:s'),
            'service' => $this->service,
            'key'     => $key,
            'value'   => $value,
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
            'service' => $this->service,
            'key'     => $key,
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
            'service' => $this->service,
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
            'service' => $this->service,
            'value'   => $value,
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
     * @param  string $type
     * @return void
     */
    public function write($key, array $aMeta, $data, $type = self::DEBUG){
        $aRecord = array(
            'uid'     => uniqid('', TRUE),
            'date'    => date('Y-m-d H:i:s'),
            'service' => $this->service,
            'key'     => $key,
            'type'    => $type,
            'data'    => json_encode($aMeta + array('data' => $data))
        );
        $this->prepareRecord($aRecord);
        $this->oStmtWrite->execute($aRecord);
    }

    /**
     * Returns data from log.
     *
     * @param  string $key
     * @param  array  $aFilter
     * @return array
     */
    public function get($key, array $aFilter = array()){
        $query =
            "SELECT * " .
            "FROM `logging_service_data` " .
            "WHERE " . $this->getFilterSQL($aFilter);
        $oStmt = $this->oDB->prepare($query);
        $index = 0;
        $this->bindFilterValues($oStmt, $aFilter, $index);
        $oStmt->execute();
        $aData = $oStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach(array_keys($aData) as $index){
            $aData[$index]['data'] = json_decode($aData[$index]['data'], TRUE);
        }

        return $aData;
    }

    /**
     * Cleanups data from log.
     *
     * @param  string $key
     * @return void
     */
    public function cleanup($key){
        $aRecord = array(
            'service' => $this->service,
            'key'     => $key,
        );
        $this->prepareRecord($aRecord);
        $this->oStmtCleanup->execute($aRecord);
    }
}
