<?php

namespace AmiLabs\DevKit;

use RuntimeException;

/**
 * Layer factory.
 */
abstract class LayerFactory{
    /**
     * Set value in child class
     *
     * @var string
     */
    protected static $namespace;

    /**
     * Set value in child class
     *
     * @var string
     */
    protected static $class;

    /**
     * Returns layer.
     *
     * @param  string $layer
     * @return mixed
     * @throws RuntimeException
     */
    public static function getLayer($layer){
        $interface = sprintf(
            "%s\\I%sLayer",
            static::$namespace,
            static::$class
        );
        $class = sprintf(
            "%s\\%s\\%s",
            static::$namespace,
            static::$class,
            $layer
        );
        $oLayer = new $class;
        if(!$oLayer instanceof $interface){
            throw new RuntimeException(
                sprintf(
                    'Class %s does not implement %s',
                    $class,
                    $interface
                )
            );
        }

        return $oLayer;
    }
}
