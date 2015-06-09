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
    public function actionIndex($oApp, $oRequest){
        echo json_encode(array('result' => 'Hello World!'));
	die();
    }
}