<?php

namespace AmiLabs\DevKit;

use AmiLabs\DevKit\Registry;

/**
 * Request class.
 */
class Request {
    /**
     * Request scopes
     *
     * @var array
     */
    protected $aScopes = array();
    /**
     * List of scopes sorted by priority.
     *
     * @var type
     */
    protected $aScopesPriority = array(INPUT_POST, INPUT_GET, INPUT_COOKIE);
    /**
     * Base URL
     *
     * @var string
     */
    protected $baseURL;
    /**
     * Subfolder
     *
     * @var string
     */
    protected $subfolder;
    /**
     * Virtual path (using mod_rewrite)
     *
     * @var string
     */
    protected $virtualPath;
    /**
     * Singleton implementation
     *
     * @var \AmiLabs\DevKit\Request
     */
    protected static $oInstance = null;
    /**
     * Returns singleton instance.
     *
     * @return \AmiLabs\DevKit\RequestDriver
     */
    public static function getInstance(){
        if(is_null(self::$oInstance)){
            $className = get_called_class();
            // Todo: move to Application::getRequest
            if(class_exists('Registry' && Registry::storageExists('CFG'))){
                // Use class name from project configuration
                $oConfig = Registry::useStorage('CFG');
                $className = $oConfig->get('Request/className', $className);
            }
            if(class_exists($className)){
                self::$oInstance = new $className();
            }else{
                throw new \Exception("Request driver class " . $className . " not found");
            }
        }
        return self::$oInstance;
    }
    /**
     * Returns request method or "CLI" if script is running in CLI mode.
     *
     * @return string
     */
    public function getMethod(){
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }
    /**
     * Returns all HTTP headers.
     *
     * @return array
     */
    public function getHeaders(){
        return getallheaders();
    }
    /**
     * Returns subfolder of current project (without leading or ending "/").
     *
     * @return string
     */
    public function getSubfolder(){
        return $this->subfolder;
    }
    /**
     * Returns virtual path for current request (without leading or ending "/").
     *
     * @return string
     */
    public function getVirtualPath(){
        return $this->virtualPath;
    }
    /**
     * Returns request variable value.
     *
     * @param  string  $name     Variable name
     * @param  mixed   $default  Default variable value
     * @param  int     $scope    Scope name (INPUT_POST, INPUT_GET, INPUT_COOKIE, INPUT_REQUEST) [optional]
     * @return mixed
     * @throws \Exception
     */
    public function get($name, $default = null, $scope = INPUT_REQUEST){
        if(isset($this->aScopes[$scope])){
            return isset($this->aScopes[$scope][$name]) ? $this->aScopes[$scope][$name] : $default;
        }
        throw new \Exception('Invalid request scope "' . $scope . '"');
    }
    /**
     * Returns request scope.
     *
     * @param  int   $scope  Scope name (INPUT_POST, INPUT_GET, INPUT_COOKIE, INPUT_REQUEST) [optional]
     * @return array
     * @throws \Exception
     */
    public function getScope($scope = INPUT_REQUEST){
        if(isset($this->aScopes[$scope])){
            return $this->aScopes[$scope];
        }
        throw new \Exception('Invalid request scope "' . $scope . '"');
    }
    /**
     * Constructor.
     */
    protected function __construct(){
        $this->aScopes[INPUT_REQUEST] = array();
        foreach($this->aScopesPriority as $scope){
            $this->aScopes[$scope] = filter_input_array($scope);
            if(is_array($this->aScopes[$scope])){
                $this->aScopes[INPUT_REQUEST] += $this->aScopes[$scope];
            }
        }
        $this->parseURL();
    }
    /**
     * Parses URL into subfolder and virtual path.
     */
    protected function parseURL(){
        $path = trim(parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI'), PHP_URL_PATH), '/');
        $script = trim(filter_input(INPUT_SERVER, 'SCRIPT_NAME'), '/');
        $this->subfolder = trim(substr($script, 0, strrpos($script, '/')), '/');
        $this->virtualPath = trim(substr($path, strlen($this->subfolder)), '/');
    }
}
