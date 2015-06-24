<?php

namespace AmiLabs\DevKit\UnitTests;

use Exception;
use PHPUnit_Framework_TestCase;
use AmiLabs\DevKit\Event;
use AmiLabs\DevKit\EventArgs;

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../../src/AmiLabs/DevKit/EventArgs.php';
require_once dirname(__FILE__) . '/../../src/AmiLabs/DevKit/Events.php';
require_once dirname(__FILE__) . '/../../src/AmiLabs/DevKit/Utility/Arrays.php';

/**
 * Unit tests.
 *
 * @package \AmiLabs\DevKit\UnitTests
 */
class Events_Test extends PHPUnit_Framework_TestCase{
    /**
     * @covers \AmiLabs\DevKit\Events::addHandler()
     * @covers \AmiLabs\DevKit\Events::fire()
     * @covers \AmiLabs\DevKit\Events::enableHandler()
     * @covers \AmiLabs\DevKit\Events::disableHandler()
     * @covers \AmiLabs\DevKit\Events::dropHandler()
     */
    public function testSimpleHandler(){
        $aArgs = array(
            'argument1' => 'value1',
            'argument2' => 'value2',
        );
        $oArgs = new EventArgs($aArgs);
        Events::addHandler('simpleEvent', array($this, 'handleSimpleEvent'));
        Events::fire('wrongSimpleEvent', $oArgs);
        $this->assertEquals(
            $aArgs,
            $oArgs->get(),
            "Arguments must be equal after wrong event firing."
        );

        Events::fire('simpleEvent', $oArgs);
        $this->assertEquals(
            'value3',
            $oArgs->get('argument2'),
            "'argument2' must be equal to 'value3'."
        );

        $oArgs->set('argument2', 'value2');
        Events::disableHandler('simpleEvent');
        Events::fire('simpleEvent', $oArgs);
        $this->assertEquals(
            $aArgs,
            $oArgs->get(),
            "Arguments must be equal after disabled event firing."
        );

        Events::enableHandler('simpleEvent');
        Events::fire('simpleEvent', $oArgs);
        $this->assertEquals(
            'value3',
            $oArgs->get('argument2'),
            "'argument2' must be equal to 'value3'."
        );

        $oArgs = new EventArgs($aArgs);
        Events::dropHandler('simpleEvent');
        $this->assertEquals(
            $aArgs,
            $oArgs->get(),
            "Arguments must be equal after dropped event firing."
        );
    }

    /**
     * @covers \AmiLabs\DevKit\Events::addHandler()
     * @covers \AmiLabs\DevKit\Events::fire()
     */
    public function testInvalidHandler(){
        $oArgs = new EventArgs;
        Events::addHandler('event', 'invalidHandler');
        try{
            Events::fire('event', $oArgs);
        }catch(Exception $oException){
            Events::dropHandler('event');

            $this->assertEquals(
                'RuntimeException',
                get_class($oException),
                sprintf(
                    "Invalid %s instead of RuntimeException cought.",
                    get_class($oException)
                )
            );
            $this->assertTrue(
                0 === strpos($oException->getMessage(), "Invalid event handler"),
                sprintf(
                    "Invalid RuntimeException message '%s'",
                    $oException->getMessage()
                )
            );

            return;
        }
        Events::dropHandler('event');

        throw new Exception("RuntimeException wasn't caught on invalid handler processing");
    }

    /**
     * @covers \AmiLabs\DevKit\Events::addHandler()
     * @covers \AmiLabs\DevKit\Events::fire()
     */
    public function testPriority(){
        $aHandlers = array(
            'handlePriorityDefault' => Events::PRIORITY_DEFAULT,
            'handlePriorityLow'     => Events::PRIORITY_LOW,
            'handlePriorityHigh'    => Events::PRIORITY_HIGH,
        );
        $oArgs = new EventArgs(array('aCallOrder' => array()));
        foreach($aHandlers as $methodName => $priority){
            Events::addHandler('event', array($this, $methodName), $priority);
        }
        Events::fire('event', $oArgs);

        $aExpected = array(
            'handlePriorityHigh',
            'handlePriorityDefault',
            'handlePriorityLow',
        );
        $this->checkCallOrder($aExpected, $oArgs);

        Events::dropHandler('event');
    }

    /**
     * @covers \AmiLabs\DevKit\Events::addHandler()
     * @covers \AmiLabs\DevKit\Events::fire()
     */
    public function testBreak(){
        $aHandlers = array(
            'handleFirst',
            'handleSecodBreaker',
            'handleThird',
        );
        $oArgs = new EventArgs(array('aCallOrder' => array()));
        foreach($aHandlers as $methodName){
            Events::addHandler('event', array($this, $methodName));
        }
        Events::fire('event', $oArgs);

        $aExpected = array(
            'handleFirst',
            'handleSecodBreaker',
        );
        $this->checkCallOrder($aExpected, $oArgs);

        Events::dropHandler('event');
    }

    public function handleSimpleEvent($name, EventArgs $oArgs){
        $this->assertEquals(
            'simpleEvent',
            $name,
            "Event name must be equal after to 'simpleEvent'."
        );
        $oArgs->set('argument2', 'value3');
    }

    public function handlePriorityDefault($name, EventArgs $oArgs){
        $aCallOrder = $oArgs->get('aCallOrder');
        $aCallOrder[] = __METHOD__;
        $oArgs->set('aCallOrder', $aCallOrder);
    }

    public function handlePriorityLow($name, EventArgs $oArgs){
        $aCallOrder = $oArgs->get('aCallOrder');
        $aCallOrder[] = __METHOD__;
        $oArgs->set('aCallOrder', $aCallOrder);
    }

    public function handlePriorityHigh($name, EventArgs $oArgs){
        $aCallOrder = $oArgs->get('aCallOrder');
        $aCallOrder[] = __METHOD__;
        $oArgs->set('aCallOrder', $aCallOrder);
    }

    public function handleFirst($name, EventArgs $oArgs){
        $aCallOrder = $oArgs->get('aCallOrder');
        $aCallOrder[] = __METHOD__;
        $oArgs->set('aCallOrder', $aCallOrder);
    }

    public function handleSecodBreaker($name, EventArgs $oArgs){
        $aCallOrder = $oArgs->get('aCallOrder');
        $aCallOrder[] = __METHOD__;
        $oArgs->set('aCallOrder', $aCallOrder);
        $oArgs->set(':break:', TRUE);
    }

    public function handleThird($name, EventArgs $oArgs){
        throw new Exception("Handler couldn't be called after break.");
    }

    protected function checkCallOrder(array $aExpected, EventArgs $oArgs){
        $aCallOrder = array_map(array($this, 'getMethodName'), $oArgs->get('aCallOrder'));
        $aDiff = array_diff_assoc($aExpected, $aCallOrder);
        $this->assertEquals(
            array(),
            $aDiff,
            sprintf(
                "Expected hanlers call order differs from actual:\n%s",
                var_export($aDiff, TRUE)
            )
        );
        $aDiff = array_diff_assoc($aCallOrder, $aExpected);
        $this->assertEquals(
            array(),
            $aDiff,
            sprintf(
                "Actual hanlers call order differs from expected:\n%s",
                var_export($aDiff, TRUE)
            )
        );
    }

    protected function getMethodName($value){
        $result = preg_replace('/^.+\:\:/', '', $value);

        return $result;
    }
}
