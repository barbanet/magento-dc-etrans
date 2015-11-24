<?php
/**
 * Dc_Etrans
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Dc
 * @package    Dc_Etrans
 * @copyright  Copyright (c) 2009-2015 Damián Culotta. (http://www.damianculotta.com.ar/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dc_Etrans_Client
{

    const VERSION = "0.1";

    /**
     * @var
     */
    private $params;

    /**
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param $datos
     * @return mixed
     */
    public function crear_parametros($datos)
    {
        $this->codifica_parametros($datos);
        $rCrear_parametros = Dc_Etrans_Poster::post("/getval/",$this->params);
        return $rCrear_parametros;

    }

    /**
     * @param $datos
     * @return mixed
     */
    public function setEnvio($datos)
    {
        $this->codifica_parametros($datos);
        $rSetEnvio = Dc_Etrans_Poster::post("/setenv/",$this->params);
        return $rSetEnvio;
    }

    /**
     * @param $params
     */
    private function codifica_parametros($params)
    {
        $algorithm = MCRYPT_CAST_256;;
        $mode = MCRYPT_MODE_CBC;
        $cert = base64_decode('xoQzb8HsYeNJIM8LSbN1BQ==');
        $json_params = json_encode($params);
        $data_usuario = json_encode(array('key'=> $this->key,'secret'=> $this->secret ));
        $encrypted_params = mcrypt_encrypt($algorithm, $this->secret, $json_params, $mode, $cert);
        $encrypted_key_secret = mcrypt_encrypt($algorithm, $this->key, $data_usuario, $mode, $cert);
        $plain_params = base64_encode($encrypted_params);
        $plain_key =base64_encode($encrypted_key_secret);
        $this->params = array($this->key,$plain_params, $plain_key);
    }

}
