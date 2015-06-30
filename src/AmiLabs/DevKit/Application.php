<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Request;
use \AmiLabs\DevKit\Registry;
use \AmiLabs\DevKit\Router;
use \AmiLabs\DevKit\Template;

/**
 * Application class.
 */
class Application{
    /**
     * Singeton instance
     *
     * @var \AmiLabs\DevKit\Application
     */
    protected static $oInstance = NULL;
    /**
     * Application configuration
     *
     * @var \AmiLabs\DevKit\Registry
     */
    protected $oConfig;
    /**
     * Router
     *
     * @var \AmiLabs\DevKit\Router
     */
    protected $oRouter;
    /**
     * Template engine
     *
     * @var \AmiLabs\DevKit\Template
     */
    protected $oTemplate = null;
    /**
     * Singleton implementation.
     *
     * @return \AmiLabs\DevKit\Application
     */
    public static function getInstance(){
        if(is_null(self::$oInstance)){
            self::$oInstance = new Application();
        }
        return self::$oInstance;
    }
    /**
     * Runs application.
     */
    public function run(){
        $this->runController();
    }
    /**
     * Returns database object.
     *
     * @return \AmiLabs\DevKit\Database
     */
    public function getDatabase(){
        return NULL; // Database::getInstance();
    }
    /**
     * Returns template engine.
     *
     * @return \AmiLabs\DevKit\Template
     */
    public function getTemplate(){
        if(is_null($this->oTemplate)){
            $engine = $this->oConfig->get('Template/Engine', FALSE);
            $aOptions = $this->oConfig->get('Template/Options', array());
            $this->oTemplate = new Template($engine, $aOptions);
        }
        return $this->oTemplate;
    }
    /**
     * Returns template object.
     *
     * @return \AmiLabs\DevKit\Request
     */
    public function getRequest(){
        return Request::getInstance();
    }
    /**
     * Returns application configuration.
     *
     * @return mixed
     */
    public function getConfig(){
        return $this->oConfig;
    }
    /**
     * Runs controller.
     *
     * @return boolean
     */
    protected function runController(){
        $oRequest = $this->getRequest();
        $controller = $this->oRouter->getController();
        $action = $this->oRouter->getAction();
        $className = $controller . 'Controller';
        $methodName = 'action' . ucfirst($action);
        $fileName = $this->oConfig->get('path/app') . '/controllers/' . $className . '.php';
        if(file_exists($fileName)){
            require_once $fileName;
            if(isset($_namespace)){
                $className = $_namespace . $className;
                unset($_namespace);
            }
            if(class_exists($className) && method_exists($className, $methodName)){
                /* @var $oController \AmiLabs\DevKit\Controller */
                $oController = new $className();
                // todo: deprecate params $oApp and $oRequest
                call_user_func(array($oController, $methodName), $this->oRouter->getActionParameters());
                $oTemplate = $this->getTemplate();
                $oView = $oController->getView();
                $layout = $oController->getLayoutName();
                $tplFile = $oController->getTemplateFile($controller . '/' . $action);
                $content = $oTemplate->get($tplFile, $oView->getScope());
                $aLayoutData = array(
                    'root' => '/' . ($oRequest->getSubfolder() ? ($oRequest->getSubfolder() . '/') : ''),
                    'content' => $content,
                    'controller' => $controller,
                    'action' => $action
                );
                $oTemplate->render($layout, $aLayoutData + $oView->getGlobalScope());
                return TRUE;
            }else{
                throw new \Exception('Cannot call "' . $className . '::' . $methodName . '"');
            }
        }else{
            throw new \Exception('File "' . $fileName . '" not found');
        }
        return FALSE;
    }
    /**
     * Constructor.
     */
    protected function __construct(){
        $this->oConfig = Registry::useStorage('CFG');
        $hasRoutes = $this->oConfig->exists('Router/aRoutes');
        $aRoutes = $hasRoutes ? $this->oConfig->get('Router/aRoutes') : NULL;
        $this->oRouter = new Router($aRoutes);
    }
}