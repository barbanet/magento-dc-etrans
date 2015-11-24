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

class Dc_Etrans_Poster
{

    const API_BASE_URL = "http://www.etrans.com.ar/api/";

    /**
     * @param $uri
     * @param $method
     * @param $content_type
     * @return resource
     */
    private static function get_connect($uri, $method, $content_type)
    {
        $c= curl_init(self::API_BASE_URL . $uri);

        curl_setopt($c, CURLOPT_USERAGENT, "Dc_Etrans " . Mage::getConfig()->getModuleConfig('Dc_Etrans')->version . " for Magento 1");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($c, CURLOPT_HTTPHEADER, array("Accept: application/json", "Content-Type: " . $content_type));

        return $c;
    }

    /**
     * @param $c
     * @param $data
     * @param $content_type
     */
    private static function set_data(&$c, $data, $content_type)
    {
        if ($content_type == "application/json") {
            if (gettype($data) == "string") {
                json_decode($data, true);
            } else {
                $data = json_encode($data);
            }

            if(function_exists('json_last_error')) {
                $json_error = json_last_error();
                if ($json_error != JSON_ERROR_NONE) {
                    print("JSON Error [{$json_error}] - Data: {$data}");
                }
            }
        }

        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    }

    /**
     * @param $method
     * @param $uri
     * @param $data
     * @param $content_type
     * @return array
     */
    private static function exec($method, $uri, $data, $content_type)
    {
        $c = self::get_connect($uri, $method, $content_type);
        if ($data) {
            self::set_data($c, $data, $content_type);
        }

        $api_result = curl_exec($c);
        $api_http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);

        $response = array(
            "status" => $api_http_code,
            "response" => json_decode($api_result, true)
        );

        if ($response['status'] >= 400) {
            print($response['response']['message'].'<br>'.$response['status']);
        }

        curl_close($c);

        return $response;
    }

    /**
     * @param $uri
     * @param string $content_type
     * @return array
     */
    public static function get($uri, $content_type = "application/json")
    {
        return self::exec("GET", $uri, null, $content_type);
    }

    /**
     * @param $uri
     * @param $data
     * @param string $content_type
     * @return array
     */
    public static function post($uri, $data, $content_type = "application/json")
    {
        return self::exec("POST", $uri, $data, $content_type);
    }

    /**
     * @param $uri
     * @param $data
     * @param string $content_type
     * @return array
     */
    public static function put($uri, $data, $content_type = "application/json")
    {
        return self::exec("PUT", $uri, $data, $content_type);
    }

}
