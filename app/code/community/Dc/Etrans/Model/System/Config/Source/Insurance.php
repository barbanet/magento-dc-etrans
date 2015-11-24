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

class Dc_Etrans_Model_System_Config_Source_Insurance
{

    const WITHOUT = 0;
    const OWN     = 1;
    const ETRANS  = 2;

    /**
     * Display formats.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => self::WITHOUT,
                'label' => Mage::helper('etrans')->__('Without Insurance')
            ),
            array(
                'value' => self::OWN,
                'label' => Mage::helper('etrans')->__('Own Insurance')
            ),
            array(
                'value' => self::ETRANS,
                'label' => Mage::helper('etrans')->__('Etrans Insurance')
            )
        );
        return $options;
    }

}
