<?php

use \AmiLabs\DevKit\Cache;

define('PATH_TMP', rtrim(sys_get_temp_dir(), '/'));

require_once __DIR__ . '/../../src/AmiLabs/DevKit/Cache.php';
require_once __DIR__ . '/../../src/AmiLabs/DevKit/Utils.php';

class Cache_Test extends PHPUnit_Framework_TestCase{
    /**
     * Random cache id
     *
     * @var string
     */
    protected $cacheFile;

    /**
     * Test data to cache
     *
     * @var array
     */
    protected $data = array('test' => array('a1' => array('b1', 'b2', 'b3'), 'a2' => 0.0001, 'a3' => TRUE));

    /**
     * Constructor. Prepares cache.
     */
    public function __construct(){
        parent::__construct();
        $this->cacheFile = md5(time());
        // Remove previously created cache files if exist
        $oCache = Cache::get($this->cacheFile);
        @unlink($this->cacheFile);
    }

    /**
     * @covers \AmiLabs\DevKit\Cache::get
     */
    public function testSanitize(){
        $oCache = Cache::get("x../x//../" . chr(0) . chr(7) . chr(255) . "x" . chr(13));
        $this->assertEquals("xxx_cache.tmp", basename($oCache->getFilename()));
    }

    /**
     * @covers \AmiLabs\DevKit\Cache::get
     */
    public function testCache(){
        $oCache = Cache::get($this->cacheFile);
        $this->assertEquals('AmiLabs\DevKit\FileCache', get_class($oCache));
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    /**
     * @covers \AmiLabs\DevKit\FileCache::exists
     * @covers \AmiLabs\DevKit\FileCache::save
     * @covers \AmiLabs\DevKit\FileCache::clear
     */
    public function testExistsSaveClear(){
        $oCache = Cache::get($this->cacheFile);
        $this->assertEquals(FALSE, $oCache->exists());
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
        $oCache->save($this->data);
        $this->assertEquals(TRUE, $oCache->exists());
        $this->assertEquals(TRUE, file_exists($oCache->getFilename()));
        $oCache->clear();
        $this->assertEquals(FALSE, $oCache->exists());
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    /**
     * @covers \AmiLabs\DevKit\FileCache::save
     */
    public function testSave(){
        $oCache = Cache::get($this->cacheFile);
        $serialized = serialize($this->data);
        $oCache->save($this->data);
        $this->assertEquals(TRUE, file_exists($oCache->getFilename()));
        $readed = file_get_contents($oCache->getFilename());
        $this->assertEquals($serialized, $readed);
        $oCache->clear();
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    /**
     * @covers \AmiLabs\DevKit\FileCache::load
     */
    public function testLoad(){
        $oCache = Cache::get($this->cacheFile);
        $serialized = serialize($this->data);
        file_put_contents($oCache->getFilename(), $serialized);
        chmod($oCache->getFilename(), 0777);
        $data = $oCache->load();
        $this->assertEquals(TRUE, is_array($data));
        $this->assertEquals(TRUE, isset($data['test']));
        $this->assertEquals(TRUE, isset($data['test']['a1']));
        $this->assertEquals('b3', $data['test']['a1'][2]);
        $this->assertEquals(0.0001, $data['test']['a2']);
        $this->assertEquals(TRUE, $data['test']['a3']);
        $oCache->clear();
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    /**
     * @covers \AmiLabs\DevKit\FileCache::clearIfOlderThan
     */
    public function testClearIfOlderThan(){
        $oCache = Cache::get($this->cacheFile);
        $oCache->save($this->data);
        sleep(1);
        $oCache->clearIfOlderThan(3);
        $this->assertEquals(TRUE, file_exists($oCache->getFilename()));
        sleep(4);
        $oCache->clearIfOlderThan(3);
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
        $oCache->clear();
    }
}