<?php

namespace AmiLabs\DevKit\Template;

use AmiLabs\DevKit\Registry;
use AmiLabs\DevKit\ITemplateDriver;
use AmiLabs\DevKit\Utility\FS;

/**
 * Smarty template engine
 */
class Smarty implements ITemplateDriver {
    /**
     * Smarty template engine
     *
     * @var Smarty
     */
    protected $oSmarty;
    /**
     * Constructor.
     *
     * @param array $aOptions  Template engine options
     */
    public function __construct(array $aOptions = array()){
        $this->oSmarty = new \Smarty;
        // Default Smarty settings
        $aDefaultOptions = array(
             'force_compile'  => FALSE,
             'debugging'      => TRUE,
             'caching'        => TRUE,
             'cache_lifetime' => 120
        );
        $aOptions += $aDefaultOptions;

        $pathApp = Registry::useStorage('CFG')->get('path/app');
        $pathTmp = Registry::useStorage('CFG')->get('path/tmp');
        $pathCompile = $pathTmp . '/smarty_compile';
        $pathCache = $pathTmp . '/smarty_cache';

        // Todo: use hooks on project installation
        FS::mkDir($pathCompile);
        FS::mkDir($pathCache);
        $this->oSmarty->setTemplateDir($pathApp . '/templates');
        $this->oSmarty->setCompileDir($pathCompile);
        $this->oSmarty->setCacheDir($pathCache);

        foreach($aOptions as $optionName => $optionValue){
            $this->oSmarty->{$optionName} = $optionValue;
        }
    }
    /**
     * Returns rendered template content.
     *
     * @param string $name   Template name
     * @param array $aScope  Data scope
     * @return string
     */
    public function get($name, array $aScope = array()){
        $tplName = $name . '.tpl';
        if($this->oSmarty->templateExists($tplName)){
            $this->oSmarty->clearAllAssign();
            foreach($aScope as $variable => $value){
                $this->oSmarty->assign($variable, $value);
            }
            $sContent = $this->oSmarty->fetch($tplName);
        }else{
            // Not found
            $sContent = 'Template "' . $name . '" not found.';
        }
        return $sContent;
    }
}