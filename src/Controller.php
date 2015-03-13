<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Application;
use \AmiLabs\DevKit\Request;
use \AmiLabs\DevKit\View;

/**
 * Base controller class.
 */
abstract class Controller {
    /**
     * Current layout
     *
     * @var string
     */
    protected $layoutName = 'layout';
    /**
     * Controller template
     *
     * @var string
     */
    protected $templateFile = '';
    /**
     * View object
     *
     * @var \AmiLabs\DevKit\View
     */
    protected $oView;
    /**
     * Access to inner site granted
     *
     * @var bool
     */
    protected $accessGranted = null;
    /**
     * Constructor.
     */
    public function __construct() {
        $this->oView = new View();
        if(!$this->checkAccess()){
            $this->tplFile = 'login';
        }
    }
    /**
     * Returns view object.
     *
     * @return \AmiLabs\DevKit\View
     */
    public function getView(){
        return $this->oView;
    }
    /**
     * Returns layout name.
     *
     * @return string
     */
    public function getLayoutName(){
        return $this->layoutName;
    }
    /**
     * Returns template filename.
     *
     * @param mixed $default
     * @return mixed
     */
    public function getTemplateFile($default = NULL){
        return $this->templateFile ? $this->templateFile : $default;
    }
    /**
     * Checks if user access granted.
     *
     * @todo Move to auth class
     * @return bool
     */
    protected function checkAccess(){
        if(is_null($this->accessGranted)){
            // todo
            $this->accessGranted = false;
        }
        return $this->accessGranted;
    }
    /**
     * Prolongates user access.
     *
     * @todo Move to auth class
     */
    protected function prolongateAccess(){
        if($this->accessGranted){
            // todo
        }
    }
}
