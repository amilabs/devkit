<?php
namespace AmiLabs\DevKit;

/**
 * Router class.
 */
class Router {
    /**
     * Project configuration
     *
     * @var \AmiLabs\DevKit\Registry
     */
    protected $oConfig;
    /**
     * Request object
     *
     * @var \AmiLabs\DevKit\Request
     */
    protected $oRequest;
    /**
     * Current route
     *
     * @var array
     */
    protected $aRoutes = array(
            ''        => array(),
            ':action' => array()
    );
    /**
     * Controller name
     *
     * @var string
     */
    protected $controller = 'index';
    /**
     * Action name
     *
     * @var string
     */
    protected $action = 'index';
    /**
     * Action parameters
     *
     * @var array
     */
    protected $aParameters = array();
    /**
     * Constructor.
     */
    public function __construct($aRoutes = NULL){
        if(!is_null($aRoutes) && is_array($aRoutes)){
            $this->aRoutes = $aRoutes;
        }
        $this->oRequest = Request::getInstance();
        $this->findRoute();
    }
    /**
     * Returns controller name.
     *
     * @return string
     */
    public function getController(){
        return $this->controller;
    }
    /**
     * Returns action name.
     *
     * @return string
     */
    public function getAction(){
        return $this->action;
    }
    /**
     * Returns action parameters.
     *
     * @return array
     */
    public function getActionParameters(){
        return $this->aParameters;
    }
    /**
     * Searches for suitable route.
     */
    protected function findRoute(){
        $lastRoute = NULL;
        $vPath = $this->oRequest->getVirtualPath();
        foreach($this->aRoutes as $route => $aRoute){
            if($this->isSuitableRoute($vPath, $route)){
                $lastRoute = $route;
                if(isset($aRoute['last']) &&  $aRoute['last']){
                    break;
                }
            }
        }
        if(!is_null($lastRoute)){
            $this->parseRoute($vPath, $lastRoute);
        }else{
            // todo: route for 404
            throw new \Exception('No route found for "' . $vPath . '"');
        }
    }
    /**
     * Checks if specified route is ok for povided path.
     *
     * @param  string  $path   Path
     * @param  string  $route  Route
     * @return boolean
     */
    protected function isSuitableRoute($path, $route){
        $aPathParts = explode('/', $path);
        $aRouteParts = explode('/', $route);
        $result = TRUE;
        foreach($aRouteParts as $idx => $routePart){
            if(!strlen($routePart) || $routePart[0] !== ':'){
                if(!isset($aPathParts[$idx]) || ($routePart !== $aPathParts[$idx])){
                    $result = FALSE;
                    break;
                }
            }
            if(strlen($routePart) && ($routePart[0] === ':') && isset($aPathParts[$idx]) && !strlen($aPathParts[$idx])){
                $result = FALSE;
                break;
            }
        }
        return $result;
    }
    /**
     * Parses specified route for provided path.
     *
     * @param  string  $path   Path
     * @param  string  $route  Route
     */
    protected function parseRoute($path, $route){
        $aRoute = $this->aRoutes[$route];
        $aPathParts = explode('/', $path);
        $aRouteParts = explode('/', $route);
        foreach($aRouteParts as $idx => $routePart){
            if(strlen($routePart) && ($routePart[0] === ':')){
                if(isset($aPathParts[$idx])){
                    $this->aParameters[substr($routePart, 1)] = $aPathParts[$idx];
                }
            }
        }
        if(isset($aRoute['default'])){
            $this->aParameters += $aRoute['default'];
        }
        if(isset($this->aParameters['controller'])){
            $this->controller = $this->aParameters['controller'];
            unset($this->aParameters['controller']);
        }
        if(isset($this->aParameters['action'])){
            $this->action = $this->aParameters['action'];
            unset($this->aParameters['action']);
        }
    }
}
