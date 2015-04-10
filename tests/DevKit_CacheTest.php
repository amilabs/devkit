<?php

use \AmiLabs\DevKit\Cache;

define('PATH_TMP', rtrim(sys_get_temp_dir(), '/'));

require_once __DIR__ . '/../src/AmiLabs/DevKit/Cache.php';

class DevKit_CacheTest extends PHPUnit_Framework_TestCase{

    protected $cacheFile;

    public function __construct(){
        parent::____construct();
        $this->cacheFile = md5(time());
        // Remove previously created cache files if exist
        $oCache = Cache::get($this->cacheFile);
        @unlink($this->cacheFile);
    }

    public function testCache(){
        $oCache = Cache::get($this->cacheFile);
        $this->assertEquals('AmiLabs\DevKit\FileCache', get_class($oCache));
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    public function testExistsSaveClear(){
        $oCache = Cache::get($this->cacheFile);
        $this->assertEquals(FALSE, $oCache->exists());
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
        $oCache->save('test data');
        $this->assertEquals(TRUE, $oCache->exists());
        $this->assertEquals(TRUE, file_exists($oCache->getFilename()));
        $oCache->clear();
        $this->assertEquals(FALSE, $oCache->exists());
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    public function testSave(){
        $oCache = Cache::get($this->cacheFile);
        $data = array('test' => array('a1' => array('b1', 'b2', 'b3'), 'a2' => 0.0001, 'a3' => TRUE));
        $serialized = serialize($data);
        $oCache->save($data);
        $this->assertEquals(TRUE, file_exists($oCache->getFilename()));
        $readed = file_get_contents($oCache->getFilename());
        $this->assertEquals($serialized, $readed);
        $oCache->clear();
        $this->assertEquals(FALSE, file_exists($oCache->getFilename()));
    }

    public function testLoad(){
        $oCache = Cache::get($this->cacheFile);
        $data = array('test' => array('a1' => array('b1', 'b2', 'b3'), 'a2' => 0.0001, 'a3' => TRUE));
        $serialized = serialize($data);
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

    public function testClearIfOlderThan(){

    }
}