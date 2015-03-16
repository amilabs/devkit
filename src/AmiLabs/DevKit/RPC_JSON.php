<?php

namespace AmiLabs\DevKit;

use \JsonRPC\Client;

/**
 * Class for JSON RPC execution.
 */
class RPC_JSON extends RPCServiceClient{
    /**
     * JSON RPC Client object
     *
     * @var \JsonRPC\Client
     */
    protected $oClient;
    /**
     * Constructor.
     *
     * @param array $aConfig  Driver configuration
     */
    public function __construct(array $aConfig){
        $this->oClient = new Client($aConfig['address']);
        if(isset($aConfig['login']) && isset($aConfig['password'])){
            $this->oClient->authentication($aConfig['login'], $aConfig['password']);                
        }
    }
    /**
     * Execute JSON RPC command.
     *
     * @param string $command
     * @param array $aParams
     * @return array
     */
    public function exec($command, array $aParams){
        return $this->oClient->execute($command, $aParams);
    }
}
