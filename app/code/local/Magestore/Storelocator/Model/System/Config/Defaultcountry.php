<?php

class Magestore_Storelocator_Model_System_Config_Defaultcountry {

    public function toOptionArray() {
        $optionCountry = array();
        $optionCountry[] = array('value' => '', 'label' => 'Please select country');
        $collection = Mage::getResourceModel('directory/country_collection')
                ->loadByStore();
        if (count($collection)) {
            foreach ($collection as $item) {
                $optionCountry[] = array('value' => $item->getId(), 'label' => $item->getName());
            }
        }

        return $optionCountry;
    }

}