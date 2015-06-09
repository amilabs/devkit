<?php

namespace AmiLabs\DevKit\Utility;

use Composer\Script\Event;

/**
 * Composer event handlers.
 */
class Composer {
    /**
     * Composer action on project install.
     *
     * @param  Event $oEvent
     * @return void
     */
    public static function postInstall(Event $oEvent){
        self::postUpdate($oEvent);
    }

    /**
     * Composer action on project update.
     *
     * @param  Event $oEvent
     * @return void
     */
    public static function postUpdate(Event $oEvent){
        FS::mkDir('log');
        FS::mkDir('tmp');
        FS::mkDir('db');
    }
}
