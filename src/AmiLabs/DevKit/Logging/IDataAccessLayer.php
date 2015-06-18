<?php

namespace AmiLabs\DevKit\Logging;

/**
 * Data Access Layer interface.
 *
 * @package AmiLabs\DevKit\Logging
 */
interface IDataAccessLayer{
    /**#@+
     * Logging level
     */

    const DEBUG   = 'DEBUG';
    const NOTICE  = 'NOTICE';
    const WARNING = 'WARNING';
    const ERROR   = 'ERROR';

    /**#@-*/

    /**
     * Creates link.
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function createLink($key, $value);

    /**
     * Deletes link.
     *
     * @param  string $key
     * @return void
     */
    public function deleteLink($key);

    /**
     * Returns link by key.
     *
     * @param  string $key
     * @return mixed  NULL if not found
     */
    public function getLinkByKey($key);

    /**
     * Returns link by value.
     *
     * @param  string $value
     * @return mixed  NULL if not found
     */
    public function getLinkByValue($value);

    /**
     * Writes data to log.
     *
     * @param  string $key
     * @param  array  $aMeta
     * @param  mixed  $data
     * @param  int    $level
     * @return void
     */
    public function write($key, array $aMeta, $data, $level = self::DEBUG);

    /**
     * Returns data from log.
     *
     * @param  string $key
     * @param  array  $aFilter
     * @return array
     */
    public function get($key, array $aFilter = array());

    /**
     * Cleanups data from log.
     *
     * @param  string $key
     * @return void
     */
    public function cleanup($key);
}
