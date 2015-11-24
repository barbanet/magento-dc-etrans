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
 * @copyright  Copyright (c) 2015 Damián Culotta. (http://www.damianculotta.com.ar/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dc_Etrans_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{

    /**
     * @var string
     */
    protected $_code = 'etrans';

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|false|Mage_Core_Model_Abstract|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');

        $rate = $this->getShippingRate($request);

        if ($rate) {
            $result->append($rate);
        }

        return $result;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return false|Mage_Core_Model_Abstract
     */
    protected function getShippingRate(Mage_Shipping_Model_Rate_Request $request)
    {
        $helper = Mage::helper('etrans');
        $address = $helper->formatAddress($request->getDestStreet());
        if ($request->getDestCity()) {
            $city = $request->getDestCity();
        } else {
            $city = '-';
        }

        $data = array(
            'calle' => $address['street'],
            'numero_puerta' => $address['number'],
            'piso' => '',
            'dpto_oficina' => '',
            'bque_torre' => '',
            'cp' => $request->getDestPostcode(),
            'localidad' => $city,
            'partido' => '',
            'provincia' => $this->getRegionName($request->getDestRegionId()),
            'email' => 'guest@example.com',
            'celular' => '',
            'nombre_razon_social' => 'Guest',
            'dni_cuit' => '',
            'telefono' => '',
            'seguro' => $this->getConfigData('insurance'),
            'horario_retiro' => $this->getConfigData('pickup_time'),
            'horario_entrega' => $this->getConfigData('delivery_time')
        );

        $items = $request->getAllItems();
        if ($items) {
            $i = 1;
            foreach ($items as $item) {
                $item_qty = (int)$item->getQty();
                $dimensions = $helper->getProductDimensions($item->getProductId());
                if ($item_qty > 1) {
                    $item_number = 1;
                    while ($item_number <= $item_qty) {
                        $data['bulto_' . $i] = array(
                            'alto' => $dimensions['height'],
                            'ancho' => $dimensions['width'],
                            'profundidad' => $dimensions['depth'],
                            'peso' => $dimensions['weight'],
                            'valor_declarado' => $dimensions['price']
                        );
                        $i++;
                        $item_number++;
                    }
                } else {
                    $data['bulto_' . $i] = array(
                        'alto' => $dimensions['height'],
                        'ancho' => $dimensions['width'],
                        'profundidad' => $dimensions['depth'],
                        'peso' => $dimensions['weight'],
                        'valor_declarado' => $dimensions['price']
                    );
                }
                $i++;
            }
        }

        $etrans = new Dc_Etrans_Client($this->getConfigData('api_key'), $this->getConfigData('api_secret'));

        $response = $etrans->crear_parametros($data);

        if (is_array($response) && !empty($response['response']['response']['Costo']))
        {
            $rate = Mage::getModel('shipping/rate_result_method');
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('standand');
            $rate->setMethodTitle($this->getConfigData('name'));
            $rate->setPrice($response['response']['response']['Costo']);
            $rate->setCost(0);
            return $rate;
        } else {
            return false;
        }

    }

    /**
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return array(
            'standard'
        );
    }

    /**
     * @param $region_id
     * @return mixed
     */
    private function getRegionName($region_id)
    {
        $region = Mage::getModel('directory/region')->load($region_id);
        if ($region->getId()) {
            return $region->getName();
        }
        return $region_id;
    }

}
