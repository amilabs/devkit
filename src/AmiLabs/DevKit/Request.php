<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Registry;

require_once PATH_LIB . '/json-rpc/Client.php';
use JsonRPC\Client;

/**
 * Request class.
 */
class Request {
    /**
     * Singleton implementation
     *
     * @var \AmiLabs\DevKit\RequestDriver
     */
    protected static $oDriver = null;
    /**
     * Returns singleton instance.
     *
     * @param string $type  Request type (uri, json, cli)
     * @return \AmiLabs\DevKit\RequestDriver
     */
    public static function getInstance($type = 'uri'){
        if(is_null(self::$oDriver)){
            switch($type){
                case 'json':
                    self::$oDriver = new RequestJSON();
                    break;
                case 'cli':
                case 'uri':
                default:
                    self::$oDriver = new RequestURI();
            }
        }
        return self::$oDriver;
    }
}
/**
 * Request driver interface.
 */
interface IRequestDriver {
    /**
     * Returns scope variable.
     */
    public function get($name, $default = null, $scope = INPUT_GET);
    /**
     * Returns GET Scope.
     */
    public function getScopeGET();
    /**
     * Returns POST Scope.
     */
    public function getScopePOST();
    /**
     * Returns Call Parameters.
     *
     * @param int $index  Parameter index
     */
    public function getCallParameters($index = false);
    /**
     * Returns controller name.
     */
    public function getControllerName();
    /**
     * Returns action name.
     */
    public function getActionName();
}
/**
 * Abstract request driver class.
 */
abstract class RequestDriver {
    /**
     * Controller parsed from uri
     *
     * @var string
     */
    protected $controllerName = 'index';
    /**
     * Action parsed from uri
     *
     * @var string
     */
    protected $actionName = 'index';
    /**
     * Script call parameters parsed from uri
     *
     * @var array
     */
    protected $aData = array();
    /**
     * Returns scope variable.
     *
     * @param string $name    Variable name
     * @param mixed $default  Default variable value if not set in the scope
     * $param int $scope      Scope (INPUT_GET, INPUT_POST)
     * @return mixed
     */
    public function get($name, $default = null, $scope = INPUT_GET){
        $aData = array();
        switch($scope){
            case INPUT_GET:
                $aData = $this->getScopeGET();
                break;
            case INPUT_POST:
                $aData = $this->getScopePOST();
                break;
        }
        return (isset($aData[$name])) ? $aData[$name] : $default;
    }
    /**
     * Returns GET scope.
     *
     * @return array
     */
    public function getScopeGET(){
        return array();
    }
    /**
     * Returns POST scope.
     *
     * @return array
     */
    public function getScopePOST(){
        return array();
    }
    /**
     * Returns script call parameters scope.
     *
     * @param int   $index    Parameter index
     * @param mixed $default  Default parameter value
     * @return array
     */
    public function getCallParameters($index = false, $default = null){
        if($index === false){
            return $this->aData;
        }
        return isset($this->aData[$index]) ? $this->aData[$index] : $default;
    }
    /**
     * Returns controller name.
     *
     * @return string
     */
    public function getControllerName(){
        return $this->controllerName;
    }
    /**
     * Returns action name.
     *
     * @return string
     */
    public function getActionName(){
        return $this->actionName;
    }
}
/**
 * Request with parameters ent through URI string driver.
 */
class RequestURI extends RequestDriver implements IRequestDriver {
    /**
     * Constructor.
     */
    public function __construct(){
        $path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $parts  = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $aParts = array();
        $subfolder = '';
        foreach ($path as $key => $val) {
            if($val === $parts[$key]){
                $subfolder .= ('/' . $val);
                unset($parts[$key]);
            } else {
                break;
            }
        }
        foreach($parts as $part){
            $aParts[] = $part;
        }
        Registry::useStorage('ENV')->set('subfolder', $subfolder);
        /*
         * for /controller/action/ :
         * 
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
        }*/
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
}

/**
 * Request with parameters ent through URI string driver.
 */
class RequestJSON extends RequestDriver implements IRequestDriver {

    protected $method = 'GET';

    /**
     * Constructor.
     * 
     * @todo Check if method is POST
     */
    public function __construct(){
        $this->method = $_SERVER['REQUEST_METHOD'];
        $aData = @json_decode(file_get_contents('php://input'), true);
        $this->aData = isset($aData['params']) ? $aData['params'] : array();
        $this->actionName = isset($aData['method']) ? $aData['method'] : 'error';
    }
    /**
     * Returns GET scope.
     *
     * @return array
     */
    public function getScopeGET(){
        return array();
    }
    /**
     * Returns POST scope.
     *
     * @return array
     */
    public function getScopePOST(){
        return array();
    }
}