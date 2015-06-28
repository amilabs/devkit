<?php

namespace AmiLabs\DevKit\Logging;

use AmiLabs\DevKit\Events;

/**
 * Data Access Layer trait.
 *
 * @package AmiLabs\DevKit\Logging
 */
trait DataAccessLayer{
    /**
     * @param array  $aOptions
     */
    protected function init(){
        Events::addHandler('log_write', array($this, 'write'));
    }

}
