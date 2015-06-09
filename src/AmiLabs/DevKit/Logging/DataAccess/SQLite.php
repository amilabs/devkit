<?php

namespace AmiLabs\DevKit\Logging\DataAccess;

use PDO;
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
     * @param string $name
     * @param array  $aOptions
     */
    public function __construct($name, array $aOptions){
        $this->name     = $name;
        $this->aOptions = $aOptions;

        $this->connect($aOptions['AmiLabs\\DevKit\\Logging\\DataAccess']);
    }

    /**
     * Creates link.
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function createLink($key, $value){

    }

    /**
     * Deletes link.
     *
     * @param  string $key
     * @return void
     */
    public function deleteLink($key){

    }

    /**
     * Returns link by key.
     *
     * @param  string $key
     * @return mixed  NULL if not found
     */
    public function getLinkByKey($key){

    }

    /**
     * Returns link by value.
     *
     * @param  string $value
     * @return mixed  NULL if not found
     */
    public function getLinkByValue($key){

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
