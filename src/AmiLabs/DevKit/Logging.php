<?php

namespace AmiLabs\DevKit;

use RuntimeException;
use AmiLabs\DevKit\Logging\IDataAccessLayer;

/**
 * Factory retuning data access layer.
 */
class Logging{
    /**
     * Returns data access layer.
     *
     * @param  array  $aOptions
     * @param  string $layer
     * @return IDataAccessLayer
     * @throws RuntimeException
     */
    public static function getLayer(array $aOptions, $layer = ''){
        $aOptions = $aOptions['AmiLabs\\DevKit\\Logging'];

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
