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

class Dc_Etrans_Model_System_Config_Source_Catalog_Product_Attributes extends Mage_Core_Model_Abstract
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->setOrder('frontend_label', 'asc');
        $options = array();
        foreach ($collection as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $options[] = array(
                   'label' => $attribute->getFrontendLabel() . ' (' . $attribute->getName() . ')',
                   'value' => $attribute->getName()
                );
            }
        }
        return $options;
    }

}
