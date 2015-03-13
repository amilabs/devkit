<?php

use \AmiLabs\DevKit\Controller;
use \AmiLabs\DevKit\RPC;

class indexController extends Controller {
    /**
     * Test service action.
     *
     * @param \AmiLabs\DevKit\Application $oApp        Application object
     * @param \AmiLabs\DevKit\RequestDriver $oRequest  Request driver
     */
    public function actionTest($oApp, $oRequest){
        $this->getView()->set('result', 'Hello World!');
        $this->getView()->set('request',  $oRequest->getCallParameters());
    }
}