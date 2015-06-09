<?php

namespace AmiLabs\DevKit;

use AmiLabs\DevKit\Registry;

/**
 * Template engine
 */
class Template {
    /**
     * Singleton instance
     *
     * @var \AmiLabs\DevKit\Template
     */
    protected static $oInstance = null;
    /**
     * Singleton implementation.
     *
     * @return \AmiLabs\DevKit\Template
     */
    public static function getInstance(){
        if(is_null(self::$oInstance)){
            self::$oInstance = new Template();
        }
        return self::$oInstance;
    }
    /**
     * Returns rendered template content.
     *
     * @param string $name   Template name
     * @param array $aScope  Data scope
     * @return string
     */
    public function get($name, array $aScope = array()){
        $pathApp = Registry::useStorage('CFG')->get('path/app');
        $aScope += $this->getGlobalScope();
        extract($aScope);
        $fileName = $pathApp . '/templates/' . $name . '.tpl.php';
        if(file_exists($fileName)){
            ob_start();
            include($fileName);
            $sContent = ob_get_contents();
            ob_end_clean();
        }else{
            // Not found
            $sContent = 'Template "' . $name . '" not found.';
        }
        return $sContent;
    }
    /**
     * Template content output.
     *
     * @param string $name   Template name
     * @param array $aScope  Data scope
     */
    public function render($name, array $aScope = array()){
        echo $this->get($name, $aScope);
    }
    /**
     * Returns global data scope.
     *
     * @return array
     */
    protected function getGlobalScope(){
        return array(
        );
    }
    /**
     * Constructor.
     */
    protected function __construct(){
        // do nothing
    }

}