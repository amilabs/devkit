<?php

namespace AmiLabs\DevKit;

require PATH_LIB . '/artemko7v/php-op_return/php-OP_RETURN.php';

/**
 * Transaction helper class.
 */
class TX {
    /**
     * Base58 alphabet
     *
     * @var string
     */
    protected static $alphabet = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    /**
     * Decodes OP_RETURN output from raw transaction hex.
     *
     * @param string $rawTXN  Raw transaction hex
     * @param boolean $asHex  Convert decoded value to hex if true
     * @return string
     */
    public static function getDecodedOpReturn($rawTXN, $asHex = true){
        $aTXNunpacked = coinspark_unpack_raw_txn($rawTXN);
        $res = '';
        foreach($aTXNunpacked['vout'] as $aOut){
            if(isset($aOut['scriptPubKey']) && (strpos($aOut['scriptPubKey'], '6a') === 0)){
                $res = pack('H*', substr($aOut['scriptPubKey'], 4));
            }
        }
        return ($asHex) ? pack('H*', $res) : $res;
    }
    /**
     * Adds OP_RETURN output to raw transaction hex.
     *
     * @param string $rawTXN    Source raw transaction string
     * @param string $metadata  Metadata to iclude in transaction (40 bytes max)
     * @return string
     */
    public static function addOpReturnOutput($rawTXN, $metadata)
	{
		$aTXNunpacked = coinspark_unpack_raw_txn($rawTXN);
		$aTXNunpacked['vout'][] = array(
			'value' => 0,
			'scriptPubKey' => '6a' . reset(unpack('H*', chr(strlen($metadata)) . $metadata)),
		);
		return coinspark_pack_raw_txn($aTXNunpacked);
	}
    /**
     * Adds custom OP_HASH output. Not recommended to use because it eats memory.
     *
     * @param string $rawTXN   Source raw transaction string
     * @param type $hexString  32 bytes hex string (deadbeefcafe0000000000000000000000000001)
     * @return string
     */
     public static function addOpHashOutput($rawTXN, $hexString){
 	$aTXNunpacked = coinspark_unpack_raw_txn($rawTXN);
 	$aTXNunpacked['vout'][]=array(
			'value' => 0.000078,
			'scriptPubKey' => '76a914' . $hexString . '88ac'
        );
		return coinspark_pack_raw_txn($aTXNunpacked);
	}
    /**
     * Decodes raw transaction data into array.
     *
     * @param string $rawTXN
     * @return array
     */
    public static function decodeTransaction($rawTXN){
        return coinspark_unpack_raw_txn($rawTXN);
    }

    /**
     * Store data in multisig output.
     *
     * @param type $rawTXN
     * @param type $hexString
     * @return type
     */
     public static function addMultisigDataOutput($rawTXN, $data){
         $hexString = reset(unpack('H*', $data));
         $dataLength = strlen($hexString);
         if($dataLength <= 190){
               $hexString = str_pad($hexString, 190, '0', STR_PAD_LEFT);
         }else{
            // data is too big
            // more outputs: todo
        }
        $hexPart1 = substr($hexString, 0, 62);
        $hexPart2 = substr($hexString, 62, 64);
        $hexPart3 = substr($hexString, 126, 64);

        $hexString = reset(unpack('H*', chr($dataLength))) . $hexPart1 . '21' . $hexPart2 . '21' . $hexPart3;
        var_dump($hexString);
        die();
		$aTXNunpacked = coinspark_unpack_raw_txn($rawTXN);
        $last = array_pop($aTXNunpacked['vout']);
		$aTXNunpacked['vout'][]=array(
			'value' => 0.000078,
			'scriptPubKey' => '5121' . $hexString . '53ae'
        );
        $aTXNunpacked['vout'][]=$last;
		return coinspark_pack_raw_txn($aTXNunpacked);
	}
    /**
     * Encodes int to base58 string.
     *
     * @param int $int
     * @return string
     */
     public static function encodeBase58($int) {
		$base58_string = "";
		$base = strlen(self::$alphabet);
		while($int >= $base) {
			$div = floor($int / $base);
			$mod = ($int - ($base * $div)); // php's % is broke with >32bit int on 32bit proc
			$base58_string = self::$alphabet{$mod} . $base58_string;
			$int = $div;
		}
		if($int) $base58_string = self::$alphabet{$int} . $base58_string;
        return $base58_string;
    }
    /**
     * Decodes base58 string into int.
     *
     * @param string $base58
     * @return int
     */
     public static function decodeBase58($base58) {
        $int_val = 0;
        for($i=strlen($base58)-1,$j=1,$base=strlen(self::$alphabet);$i>=0;$i--,$j*=$base) {
            $int_val += $j * strpos(self::$alphabet, $base58{$i});
        }
        return $int_val;
     }    
}
