<?php

namespace AmiLabs\DevKit;

use RuntimeException;
use AmiLabs\DevKit\Utility\Arrays;

/**
 * Events manager.
 */
class Events{
    /**
     * Low event handler priority
     *
     * @var int
     * @see Events::addHandler()
     */
    const PRIORITY_LOW = 25;

    /**
     * Default event handler priority
     *
     * @var int
     * @see Events::addHandler()
     */
    const PRIORITY_DEFAULT = 50;

    /**
     * High event handler priority
     *
     * @var int
     * @see Events::addHandler()
     */
    const PRIORITY_HIGH = 75;


    /**
     * Event handlers
     *
     * @var array
     */
    private static $aHandlers = array();

    /**
     * Contains ordered by priority flags for each event name
     *
     * @var array
     */
    private static $aOrdered = array();

    /**
     * Contains disabled events.
     *
     * @var array
     */
    private static $aDisabled = array();

    /**
     * Contains fired events names && target module name to avoid recurring firing during its execution
     *
     * @var array
     */
    private static $aFiredEvents = array();

    /**
     * Array of hidden event debugger events
     *
     * @var array
     */
    private static $aDebugEvents = array('on_add_handler', 'on_start_fire_event', 'on_end_fire_event');

    /**
     * Enables {@link Events::$aDebugEvents} events
     *
     * @var bool
     */
    private static $bDebug = false;

    /**
     * Adds event handler.
     *
     * To break event handling handler must set $oArgs[':break:'] to true.<br /><br />
     *
     * Example:
     * <code>
     * // @param  string     $name   Event name
     * // @param  EventArgs  $oArgs  Event arguments
     * // @return array
     * function eventHandler($name, EventArgs $oArgs){
     *     $oArgs->set('varName', 'varValue');
     *     $oArgs->set(':break:', TRUE);
     * }
     * </code>
     *
     * @param  string   $name      Event name
     * @param  callback $handler   Event handler callback
     * @param  int      $priority  Event priority:
     *                             Events::PRIORITY_LOW, Events::PRIORITY_DEFAULT or Events::PRIORITY_HIGH
     * @return void
     */
    public static function addHandler($name, $handler, $priority = self::PRIORITY_DEFAULT){
        $key = '';
        if(is_array($handler)){
            $key .= (is_object($handler[0]) ? get_class($handler[0]) : $handler[0]) . '|' . $handler[1];
        }else{
            $key .= $handler;
        }
        $addHandler = TRUE;
        if(empty(self::$aHandlers[$name])){
            self::$aHandlers[$name] = array();
        }elseif(isset(self::$aHandlers[$name][$key])){
            $addHandler = FALSE;
        }
        if($addHandler){
            self::$aHandlers[$name][$key] = array($priority, $handler);
            self::$aOrdered[$name] = false;
        }
        if(self::$bDebug && !in_array($name, self::$aDebugEvents)){
            $aOn = array('name' => $name, 'handler' => $handler, 'added' => $addHandler);
            self::fire('on_add_handler', $aOn, self::MOD_ANY);
        }
    }

    /**
     * Drops event handler(s).
     *
     * Example:
     * <code>
     * // drop all 'some_event_name' event handlers
     * Events::dropHandler('some_event_name');
     *
     * // drop all 'some_event_name' event handlers processing by $object methods only
     * Events::dropHandler('some_event_name', $object);
     * </code>
     *
     * @param  string $name   Event name
     * @param  mixed $entity  Object|string (Object or class/function name)
     * @return void
     */
    public static function dropHandler($name = '', $entity = NULL){
        // Detect entity type
        if(is_object($entity)){
            $entityType = 'object';
        }elseif(is_string($entity)){
            $entityType = function_exists($entity) ? 'function' : 'class';
        }elseif(is_array($entity)){
            $entityType = 'callback';
        }else{
            $entityType = '';
        }
        $aNames = $name === '' ? array_keys(self::$aHandlers) : array($name);
        foreach($aNames as $name){
            if(empty(self::$aHandlers[$name])){
                // There aren't handlers for specified event name
                continue;
            }
            $aIndices = array_keys(self::$aHandlers[$name]);
            switch($entityType){
                case 'object':
                case 'function':
                    // Clean up methods of specified object / specified functions
                    foreach($aIndices as $index){
                        if(
                            is_array(self::$aHandlers[$name][$index]) &&
                            self::$aHandlers[$name][$index][1][0] === $entity
                        ){
                            self::cleanupHandler($name, $index);
                        }
                    }
                    break;
                case 'class':
                    // Clean up static specified classes methods
                    foreach($aIndices as $index){
                        if(
                            is_array(self::$aHandlers[$name][$index]) &&
                            (
                                self::$aHandlers[$name][$index][1][0] == $entity ||
                                get_class(self::$aHandlers[$name][$index][1][0]) == $entity
                            )
                        ){
                            self::cleanupHandler($name, $index);
                        }
                    }
                    break;
                case 'callback':
                    // Clean up specified callbacks
                    foreach($aIndices as $index){
                        if(
                            is_array(self::$aHandlers[$name][$index]) &&
                            (
                                (
                                    self::$aHandlers[$name][$index][1][0] == $entity[0] ||
                                    (
                                        is_object(self::$aHandlers[$name][$index][1][0]) && is_object($entity[0])
                                            ? get_class(self::$aHandlers[$name][$index][1][0]) == get_class($entity[0])
                                            : self::$aHandlers[$name][$index][1][0] == $entity[0]
                                    )
                                ) &&
                                self::$aHandlers[$name][$index][1][1] == $entity[1]
                            )
                        ){
                            self::cleanupHandler($name, $index);
                        }
                    }
                    break;
                default:
                    // Cleanup all handlers with specified name
                    unset(self::$aHandlers[$name], self::$aOrdered[$name]);
            }
        }
    }

    /**
     * Disable handler.
     *
     * @param  string $name  Event name
     * @return void
     */
    public static function disableHandler($name){
        self::$aDisabled[$name] = TRUE;
    }

    /**
     * Enable handler.
     *
     * @param  string $name  Event name
     * @return void
     */
    public static function enableHandler($name){
        unset(self::$aDisabled[$name]);
    }

    /**
     * Returns TRUE if there are any handlers for specified event.
     *
     * @param  string $name  Event name
     * @return bool
     */
    public static function hasHandlers($name){
        $result = isset(self::$aHandlers[$name]);

        return $result;
    }

    /**
     * Returns handlers for passed event or all.
     *
     * @param  string $name  Event name
     * @return array
     */
    public static function getHandlers($name = NULL){
        $aResult = is_null($name) ? self::$aHandlers : self::$aHandlers[$name];

        return $aResult;
    }

    /**
     * Fires event.
     *
     * @param  string    $name   Event name
     * @param  EventArgs $oArgs  Event arguments
     * @return void
     * @throws RuntimeException
     */
    public static function fire($name, EventArgs $oArgs){
        if(self::$bDebug && !in_array($name, self::$aDebugEvents)){
            $uid = uniqid('', TRUE);
            $oOnArgs = new EventArgs(
                array(
                    'uid'   => $uid,
                    'name'  => $name,
                    'oArgs' => $oArgs,
                )
            );
            self::fire('on_start_fire_event', $oOnArgs, self::MOD_ANY);
            unset($oOnArgs);
        }

        if(isset(self::$aHandlers[$name]) && empty(self::$aDisabled[$name])){
            if(isset(self::$aFiredEvents[$name])){
                throw new RuntimeException(
                    sprintf(
                        "Event '%s' is fired already",
                        $name
                    )
                );
            }else{
                self::$aFiredEvents[$name] = TRUE;
                if(!self::$aOrdered[$name]){
                    Arrays::sortMultiArrayPreserveKeys(
                        self::$aHandlers[$name],
                        0,
                        SORT_NUMERIC,
                        SORT_DESC
                    );
                    self::$aOrdered[$name] = TRUE;
                }
                foreach(array_keys(self::$aHandlers[$name]) as $index){
                    if(empty(self::$aHandlers[$name][$index][1])){
                        // Targeted event, not for this target
                        continue;
                    }
                    // Call handler
                    $callback = self::$aHandlers[$name][$index][1];
                    if(!is_callable($callback)){
                        unset(self::$aFiredEvents[$name]);
                        if(is_array($callback)){
                            $callback =
                                (
                                    is_object($callback[0])
                                        ? get_class($callback[0]) . '->'
                                        : $callback[0] . '::'
                                ) .
                                $callback[1];
                        }else{
                            $callback = sprintf("function %s", $callback);
                        }

                        throw new RuntimeException(
                            sprintf(
                                "Invalid event handler %s added to process '%s' event.",
                                $callback,
                                $name
                            )
                        );
                    }
                    call_user_func(
                        self::$aHandlers[$name][$index][1],
                        $name,
                        $oArgs
                    );
                    if(!$oArgs->isEmpty(':break:')){
                        break;
                    }
                }
                unset(self::$aFiredEvents[$name]);
            }
        }

        if(self::$bDebug && !in_array($name, self::$aDebugEvents)){
            $oOnArgs = new EventArgs(
                array(
                    'uid'   => $uid,
                    'name'  => $name,
                    'oArgs' => $oArgs,
                )
            );
            self::fire('on_end_fire_event', $oOnArgs, self::MOD_ANY);
        }
    }

    /**
     * Setup internal debugging.
     *
     * @param  bool $bDebug  Enable/disable flag
     * @return void
     * @todo   Separate debug implementation from production implementation?
     * @amidev
     */
    public static function setDebug($bDebug){
        self::$bDebug = (bool)$bDebug;
    }

    /**
     * Cleanups handler.
     *
     * @param  string $name   Event name
     * @param  int    $index  Index
     * @return void
     * @see    Events::dropHandler()
     */
    private static function cleanupHandler($name, $index){
        unset(self::$aHandlers[$name][$index]);
        if(sizeof(self::$aHandlers[$name])){
            ksort(self::$aHandlers[$name]);
            self::$aOrdered[$name] = false;
        }else{
            unset(self::$aHandlers[$name], self::$aOrdered[$name]);
        }
    }

    /**
     * Static class, object creating is forbidden.
     */
    private function __construct(){
    }

    /**
     * Static class, cloning is forbidden.
     */
    private function __clone(){
    }
}
