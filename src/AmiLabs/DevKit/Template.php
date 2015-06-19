<?php

namespace AmiLabs\DevKit;

/**
 * Template engine
 */
class Template {
    /**
     * Template engine
     *
     * @var \AmiLabs\DevKit\ITemplate
     */
    protected $oEngine;
    /**
     * Constructor.
     */
    public function __construct($engine = FALSE, array $aOptions = array()){
        if(FALSE === $engine){
            $engine = 'PHP';
        }
        $className =
            (FALSE === strpos($engine, '\\')) ?
                'AmiLabs\\DevKit\\Template\\' . $engine :
                $engine;
        if(!class_exists($className)){
            throw new Exception('Invalid template engine "' . $engine . '"');
        }
        $this->oEngine = new $className($aOptions);
    }
    /**
     * Returns rendered template content.
     *
     * @param string $name   Template name
     * @param array $aScope  Data scope
     * @return string
     */
    public function get($name, array $aScope = array()){
        return $this->oEngine->get($name, $aScope);
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
}

/**
 * Template engine driver interface
 */
interface ITemplateDriver {
    /**
     * Constructor.
     *
     * @param array $aOptions
     */
    public function __construct(array $aOptions);
    /**
     * Returns rendered template.
     *
     * @param string $name   Template name
     * @param array $aScope  Scope of template variables
     */
    public function get($name, array $aScope);
}