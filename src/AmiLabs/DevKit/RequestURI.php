<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Request;

/**
 * Request with parameters ent through URI string driver.
 */
class RequestURI extends RequestDriver implements IRequestDriver {
    /**
     * List of possible routes (todo)
     *
     * @var array
     */
    protected $aRoutes = array();
    /**
     * Constructor.
     */
    public function __construct(){
        $path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        array_walk($parts, array($this, 'stripGetAgrs'));
        $aParts = array();
        $subfolder = '';
        foreach ($path as $key => $val) {
            if(isset($parts[$key]) && ($val === $parts[$key])){
                // var_dump($val);###
                $subfolder .= ('/' . $val);
                unset($parts[$key]);
            } else {
                break;
            }
        }
        $aParts = $parts;
        Registry::useStorage('ENV')->set('subfolder', $subfolder);
        if((count($aParts) > 1) && $aParts[1]){
            $this->controllerName = $aParts[1];
            if(isset($aParts[2]) && $aParts[2]){
                $this->actionName = $aParts[2];
            }
        }
        if(count($aParts) > 3){
            for($i = 3; $i < count($aParts); $i++){
                $this->aData[] = $aParts[$i];
            }
        }
        if((count($aParts) > 0) && $aParts[0]){
            $this->actionName = $aParts[0];
            if(count($aParts) > 1){
                for($i = 1; $i < count($aParts); $i++){
                    $this->aData[] = urldecode($aParts[$i]);
                }
            }
        }
    }
    /**
     * Returns GET scope.
     *
     * @return array
     */
    public function getScopeGET(){
        return $_GET;
    }
    /**
     * Returns POST scope.
     *
     * @return array
     */
    public function getScopePOST(){
        return $_POST;
    }

    /**
     * Strips everything exclude PATH part.
     *
     * @param  string $value
     * @return void
     */
    protected function stripGetAgrs(&$value){
        $value = parse_url($value, PHP_URL_PATH);
    }
}
