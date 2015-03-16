<?php

namespace AmiLabs\DevKit;

use \AmiLabs\DevKit\Request;

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