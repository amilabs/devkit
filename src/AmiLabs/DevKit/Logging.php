<?php

namespace AmiLabs\DevKit;

use AmiLabs\DevKit\Logging\IDataAccessLayer;

/**
 * Factory retuning data access layer.
 */
class Logging{
    /**
     * Returns data access layer.
     *
     * @param  string $layer
     * @return IDataAccessLayer
     */
    public static function getLayer($layer = ''){
        $aOptions = Registry::useStorage('CFG')->get('AmiLabs\\DevKit\\Logging');
        $class =
            '' === $layer
            ? 'AmiLabs\\DevKit\\Logging\\DataAccess\\' . $aOptions['DataAccess']['layer']
            : $layer;
        $oLayer = new $class($aOptions);

        if(!$oLayer instanceof IDataAccessLayer){
            throw new RuntimeException(
                sprintf(
                    'Class %s does not implement AmiLabs\\DevKit\\Logging\\IDataAccessLayer',
                    $class
                )
            );
        }

        return $oLayer;
    }
}
