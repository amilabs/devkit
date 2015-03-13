<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Request;
use \AmiLabs\DevKit\Template;
use \AmiLabs\DevKit\Application;

/**
 * Application as Counterparty Service class.
 */
class ApplicationServiceCounterparty extends Application{
    /**
     * Singleton implementation.
     *
     * @return \AmiLabs\DevKit\Application
     */
    public static function getInstance(){
        if(is_null(self::$oInstance)){
            self::$oInstance = new ApplicationServiceCounterparty();
        }
        return self::$oInstance;
    }
    /**
     * Returns template object.
     *
     * @return \AmiLabs\DevKit\RequestJSON
     */
    public function getRequest(){
        return Request::getInstance('json');
    }
    /**
     * Runs controller.
     *
     * @return boolean
     */
    protected function runController(){
        $oRequest = $this->getRequest();
        $controller = $oRequest->getControllerName();
        $action = $oRequest->getActionName();
        $className = $controller . 'Controller';
        /**
         * action + Name
         */
        $methodName = 'action' . ucfirst($action);
        $fileName = PATH_APP . '/controllers/' . $className . '.php';
        if(file_exists($fileName)){
            require_once $fileName;
            if(class_exists($className) && method_exists($className, $methodName)){
                /* @var $oController \AmiLabs\DevKit\Controller */
                $oController = new $className;
                call_user_func(array($oController, $methodName), $this, $oRequest);
                $oView = $oController->getView();

                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                echo json_encode($oView->getScope());
                return true;
            }else{
                throw new \Exception('Cannot call "' . $className . '::' . $methodName . '"');
            }
        }else{
            throw new \Exception('File "' . $fileName . '" not found');
        }
        return false;
    }
}