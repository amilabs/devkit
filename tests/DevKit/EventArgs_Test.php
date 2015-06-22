<?php

namespace AmiLabs\DevKit\UnitTests;

use PHPUnit_Framework_TestCase;
use AmiLabs\DevKit\EventArgs;

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../../src/AmiLabs/DevKit/EventArgs.php';

/**
 * Unit tests.
 *
 * @package \AmiLabs\DevKit\UnitTests
 */
class EventArgs_Test extends PHPUnit_Framework_TestCase{
   /**
     * @covers \AmiLabs\DevKit\EventArgs::__construct()
     * @covers \AmiLabs\DevKit\EventArgs::set()
     * @covers \AmiLabs\DevKit\EventArgs::exists()
     * @covers \AmiLabs\DevKit\EventArgs::isEmpty()
     * @covers \AmiLabs\DevKit\EventArgs::get()
     * @covers \AmiLabs\DevKit\EventArgs::delete()
     */
    public function testAll(){
        $oArgs = new EventArgs;
        $this->assertFalse(
            $oArgs->exists('key'),
            'Argument cannot exist in an empty arguments object.'
        );
        $this->assertTrue(
            $oArgs->isEmpty('key'),
            'Argument could be empty in an empty arguments object.'
        );
        $this->assertTrue(
            array() === $oArgs->get(),
            'An empty arguments object could contain empty arguments array.'
        );

        $oArgs->set('key', NULL);
        $this->assertTrue(
            $oArgs->exists('key'),
            'Argument having NULL value could exists.'
        );
        $this->assertTrue(
            $oArgs->isEmpty('key'),
            'Argument having NULL value could be empty.'
        );
        $this->assertTrue(
            is_null($oArgs->get('key')),
            'Getting argument having NULL value could return NULL.'
        );

        $value = 123;
        $oArgs->set('key', $value);
        $this->assertTrue(
            $oArgs->exists('key'),
            'Argument having not NULL value could exists.'
        );
        $this->assertFalse(
            $oArgs->isEmpty('key'),
            'Argument having not empty value could not be empty.'
        );
        $this->assertEquals(
            $value,
            $oArgs->get('key'),
            sprintf('Argument having %s value could return same.', $value)
        );
        $oArgs->delete('key');
        $this->assertFalse(
            $oArgs->exists('key'),
            'Deleted argument could not exist'
        );
        $this->assertTrue(
            $oArgs->isEmpty('key'),
            'Deleted argument could be empty'
        );
        $default = ':value:';
        $this->assertEquals(
            $default,
            $oArgs->get('key', $default),
            sprintf('Deleted argument could return passed default value.', $value)
        );

        $aArgs = array(
            'first'  => 1,
            'second' => 2
        );
        $oArgs = new EventArgs($aArgs);
        $this->assertTrue(
            $aArgs === $oArgs->get(),
            'Initial array could be equal to EventArgs::get() result.'
        );
    }
}