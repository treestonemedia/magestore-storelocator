<?php

class Magestore_Storelocator_Model_Mysql4_Specialday extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {           
        $this->_init('storelocator/specialday', 'storelocator_specialday_id');
    }
}