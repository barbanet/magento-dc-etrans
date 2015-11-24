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

class Dc_Etrans_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @param $address
     * @return array
     */
    public function formatAddress($address)
    {
        $formatted_address = array();
        if (empty($address)) {
            $formatted_address['street'] = 'Dummy Address';
            $formatted_address['number'] = '-';
        } else {
            $helper = Mage::helper('core/string');
            if ($helper->strlen($address) > 125) {
                $formatted_address['street'] = $helper->truncate($address, 124);
                $formatted_address['number'] = $helper->substr($address, -63);
            } else {
                $formatted_address['street'] = $address;
                $formatted_address['number'] = '-';
            }

        }
        return $formatted_address;
    }

    /**
     * @param $product_id
     * @return array
     */
    public function getProductDimensions($product_id)
    {
        $dimensions = array();
        $dimensions['height'] = '';
        $dimensions['width'] = '';
        $dimensions['depth'] = '';
        $dimensions['weight'] = '';
        $dimensions['price'] = '';

        if (Mage::app()->getStore()->getConfig('carriers/etrans/map_attributes')) {
            $product = Mage::getModel('catalog/product')->load($product_id);
            if ($product->getId()) {
                if (Mage::app()->getStore()->getConfig('carriers/etrans/attribute_height')) {
                    $dimensions['height'] = $product->getData(Mage::app()->getStore()->getConfig('carriers/etrans/attribute_height'));
                }
                if (Mage::app()->getStore()->getConfig('carriers/etrans/attribute_width')) {
                    $dimensions['width'] = $product->getData(Mage::app()->getStore()->getConfig('carriers/etrans/attribute_width'));
                }
                if (Mage::app()->getStore()->getConfig('carriers/etrans/attribute_depth')) {
                    $dimensions['depth'] = $product->getData(Mage::app()->getStore()->getConfig('carriers/etrans/attribute_depth'));
                }
                if (Mage::app()->getStore()->getConfig('carriers/etrans/attribute_weight')) {
                    $dimensions['weight'] = $product->getData(Mage::app()->getStore()->getConfig('carriers/etrans/attribute_weight'));
                }
                if (Mage::app()->getStore()->getConfig('carriers/etrans/attribute_price')) {
                    $dimensions['price'] = $product->getData(Mage::app()->getStore()->getConfig('carriers/etrans/attribute_price'));
                }
            }
        }
        return $dimensions;
    }

}
