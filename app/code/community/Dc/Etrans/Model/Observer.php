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

class Dc_Etrans_Model_Observer
{

    /**
     * TODO: method needs refactoring (nesting level = 6)
     *
     * @param $observer
     */
    public function setTrackingCode($observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        $method = $shipment->getOrder()->getShippingCarrier()->getCarrierCode();
        if ($method == 'etrans') {
            try {
                $helper = Mage::helper('etrans');
                $address = $helper->formatAddress(str_replace("\n", ", ", $shipment->getOrder()->getShippingAddress()->getStreetFull()));
                $data = array(
                    'calle' => $address['street'],
                    'numero_puerta' => $address['number'],
                    'piso' => '',
                    'dpto_oficina' => '',
                    'bque_torre' => '',
                    'cp' => $shipment->getOrder()->getShippingAddress()->getPostcode(),
                    'localidad' => $shipment->getOrder()->getShippingAddress()->getCity(),
                    'partido' => '',
                    'provincia' => $shipment->getOrder()->getShippingAddress()->getRegion(),
                    'email' => $shipment->getOrder()->getCustomerEmail(),
                    'celular' => '',
                    'nombre_razon_social' => $shipment->getOrder()->getShippingAddress()->getFirstname() . ' ' . $shipment->getOrder()->getShippingAddress()->getLastname(),// * Obligatorio
                    'dni_cuit' => $shipment->getOrder()->getCustomerTaxvat(),
                    'telefono' => $shipment->getOrder()->getBillingAddress()->getTelephone(),
                    'seguro' => Mage::app()->getStore()->getConfig('carriers/etrans/insurance'),
                    'horario_retiro' => Mage::app()->getStore()->getConfig('carriers/etrans/pickup_time'),
                    'horario_entrega' => Mage::app()->getStore()->getConfig('carriers/etrans/delivery_time')
                );

                $items = $shipment->getAllItems();
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

                $etrans = new Dc_Etrans_Client(
                                            Mage::app()->getStore()->getConfig('carriers/etrans/api_key'),
                                            Mage::app()->getStore()->getConfig('carriers/etrans/api_secret')
                                );
                $response = $etrans->setEnvio($data);

                if (is_array($response) && is_array($response['response']['response']['Envios'])) {
                    $message = Mage::helper('etrans')->__(
                        'Assigned vouchers: %s.',
                        implode(', ', $response['response']['response']['Envios'])
                    );
                    if (!empty($response['response']['response']['Fecha Entrega'])) {
                        $date_format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                        $date = Mage::app()->getLocale()->date($response['response']['response']['Fecha Entrega'], $date_format);
                        $delivery_date = Mage::getSingleton('core/date')->gmtDate('d/m/Y', $date->getTimestamp());
                        $message .= Mage::helper('etrans')->__(' Estimated delivery date: %s.', $delivery_date);
                    }
                    $shipment->addComment($message, false, true);
                } else {
                    throw new Mage_Core_Exception(Mage::helper('etrans')->__('Shipment was not created at Etrans.'));
                }
            } catch (Exception $e) {
                Mage::logException($e);
                throw new Mage_Core_Exception(Mage::helper('etrans')->__('There was an error trying to create the shipment at Etrans.'));
            }
        }
    }

}
