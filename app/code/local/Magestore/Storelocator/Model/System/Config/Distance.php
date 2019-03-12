<?php

class Magestore_Storelocator_Model_System_Config_Distance {

    public function toOptionArray() {
        $options = array(
            array('value' => 'km', 'label' => Mage::helper('storelocator')->__('Kilometers')),
            array('value' => 'mi', 'label' => Mage::helper('storelocator')->__('Miles')),
        );
        return $options;
    }

}