<?php

namespace AmiLabs\DevKit\UnitTests;

use PHPUnit_Framework_TestCase;
use AmiLabs\DevKit\Registry;

require_once realpath(dirname(__FILE__)) . '/../../src/AmiLabs/DevKit/Registry.php';

/**
 * Unit tests.
 *
 * @package \AmiLabs\DevKit\UnitTests
 */
class Registry_Test extends PHPUnit_Framework_TestCase{
    /**
     * Test data
     *
     * @var array
     */
    protected $data = array(
        'A1' => array(
            'B1' => array(
                'C1' => 'C1',
                'C2' => 0,
                'C3' => FALSE,
            ),
            'B2' => 'B2'
        ),
        'A2' => array(
            'B2' => array(
                'C2' => TRUE
            )
        )
    );
    /**
     * Initialization
     */
    public function startUp(){
        Registry::initialize();
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::addStorage()
     */
    public function testAddStorage(){
        // Assert for a new storage
        Registry::addStorage('S1');
        $this->assertEquals(
            TRUE,
            Registry::storageExists('S1'),
            "New storage addition failed"
        );

        // Assert for creating existing storage
        $exception = FALSE;
        try{
            Registry::addStorage('S1');
        }catch(\Exception $exception){}
        $this->assertInstanceOf(
            'RuntimeException',
            $exception,
            "Adding existing storage did not threw any Exception"
        );
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::storageExists()
     */
    public function testStorageExists(){
        $this->assertEquals(TRUE, Registry::storageExists('S1'));
        $this->assertEquals(FALSE, Registry::storageExists('S2'));
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::useStorage()
     */
    public function testUseStorage(){
        $exception = FALSE;
        try{
            $oStorage = Registry::useStorage('S1');
        }catch(\Exception $exception){}
        $this->assertInstanceOf('\AmiLabs\DevKit\Registry', $oStorage);
        $this->assertEquals(FALSE, $exception);
        try{
            $oStorage = Registry::useStorage('S2');
        }catch(\Exception $exception){}
        $this->assertInstanceOf(
            'RuntimeException',
            $exception,
            "Usage of not existing storage did not threw any Exception"
        );
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::get()
     * @covers \AmiLabs\DevKit\Registry::set()
     * @covers \AmiLabs\DevKit\Registry::exists()
     */
    public function testGetSetExists(){
        $oStorage = Registry::useStorage('S1');
        // Root setter
        $exception = FALSE;
        try{
            $oStorage->set(Registry::ROOT, 'A1');
        }catch(\Exception $exception){}
        $this->assertInstanceOf('RuntimeException', $exception);
        $oStorage->set(Registry::ROOT, $this->data);
        // Get full tree
        $this->assertEquals($this->data, $oStorage->get());
        // Existance test
        $this->assertEquals(TRUE, $oStorage->exists('A1'));
        $this->assertEquals(TRUE, $oStorage->exists('A1/B1/C1'));
        $this->assertEquals(FALSE, $oStorage->exists('A1/B1/C4'));
        // Single key test
        $this->assertEquals($this->data['A1'], $oStorage->get('A1'));
        // Nested key test
        $this->assertEquals('C1', $oStorage->get('A1/B1/C1'));
        $this->assertEquals(0, $oStorage->get('A1/B1/C2'));
        $oStorage->set('A1/B1/C2', 1);
        $this->assertEquals(1, $oStorage->get('A1/B1/C2'));
        // Use default value
        $this->assertEquals('foo', $oStorage->get('A1/B1/C4', 'foo'));
        // Append a value
        $oStorage->set('A1/B1/C3', TRUE, Registry::APPEND);
        $this->assertEquals(FALSE, $oStorage->get('A1/B1/C3'));
        // Overwrite
        $oStorage->set('A1/B1/C3', TRUE, Registry::OVERWRITE);
        $this->assertEquals(TRUE, $oStorage->get('A1/B1/C3'));
        // Set nested key with non-array inside
        $exception = FALSE;
        try{
            $oStorage->set('A1/B2/C1', TRUE);
        }catch(\Exception $exception){}
        $this->assertInstanceOf('RuntimeException', $exception);
        $oStorage->set('A1/B1/C3', NULL);
        $this->assertEquals(FALSE, $oStorage->exists('A1/B1/C3'));
        $this->assertEquals('foo', $oStorage->get('A1/B1/C3', 'foo'));
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::remove()
     */
    public function testRemove(){
        $oStorage = Registry::useStorage('S1');
        $res = $oStorage->remove('A1/B2');
        $this->assertEquals(FALSE, $oStorage->remove('A1/B3'));
        $this->assertEquals(TRUE, $oStorage->exists('A1/B2'));
        $this->assertEquals(TRUE, $oStorage->remove('A1/B2'));
        $this->assertEquals(FALSE, $oStorage->exists('A1/B2'));
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::persist()
     * @covers \AmiLabs\DevKit\Registry::isPersistant()
     */
    public function testPersist(){
        $oStorage = Registry::useStorage('S1');
        $this->assertEquals(FALSE, $oStorage->isPersistent('A2/B2'));
        $oStorage->persist('A2/B2');
        $this->assertEquals(TRUE, $oStorage->isPersistent('A2/B2'));
        // Can not remove persistant key
        $this->assertEquals(FALSE, $oStorage->remove('A2/B2'));
        // Can not overwrite
        try{
            $oStorage->set('A2/B2', TRUE);
        }catch(\Exception $exception){}
        $this->assertInstanceOf('RuntimeException', $exception);
    }
    /**
     * @covers \AmiLabs\DevKit\Registry::initialize()
     */
    public function testInitialize(){
        $this->assertEquals(TRUE, Registry::storageExists('S1'));
        Registry::initialize();
        $this->assertEquals(FALSE, Registry::storageExists('S1'));
    }
}