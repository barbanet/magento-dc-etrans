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

class Dc_Etrans_Model_System_Config_Source_Time_Delivery
{

    const MORNING   = 1;
    const AFTERNOON = 2;
    const EVENING   = 3;

    /**
     * Display formats.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => self::MORNING,
                'label' => Mage::helper('etrans')->__('Morning (between 8 AM and 1 PM)')
            ),
            array(
                'value' => self::AFTERNOON,
                'label' => Mage::helper('etrans')->__('Afternoon (between 1 PM and 6 PM)')
            ),
            array(
                'value' => self::EVENING,
                'label' => Mage::helper('etrans')->__('Evening (between 6 PM and 11 PM)')
            )
        );
        return $options;
    }

}
