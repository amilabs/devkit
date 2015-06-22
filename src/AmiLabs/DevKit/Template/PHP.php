<?php

namespace AmiLabs\DevKit\Template;

use AmiLabs\DevKit\Registry;
use AmiLabs\DevKit\ITemplateDriver;

/**
 * PHP template engine
 */
class PHP implements ITemplateDriver {
    /**
     * Constructor.
     *
     * @param array $aOptions  Template engine options
     */
    public function __construct(array $aOptions = array()){
        // do nothing
    }
    /**
     * Checks if template file exists.
     *
     * @param string $name  Template name
     * @return bool
     */
    public function exists($name){
        return file_exists($this->getTemplateFilename($name));
    }
    /**
     * Returns rendered template content.
     *
     * @param string $name   Template name
     * @param array $aScope  Data scope
     * @return string
     */
    public function get($name, array $aScope = array()){
        $fileName = $this->getTemplateFilename($name);
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
     * Returns template filename.
     *
     * @param string $name  Template name
     * @return string
     */
    protected function getTemplateFilename($name){
        $pathApp = Registry::useStorage('CFG')->get('path/app');
        extract($aScope);
        return $pathApp . '/templates/' . $name . '.tpl.php';
    }
}