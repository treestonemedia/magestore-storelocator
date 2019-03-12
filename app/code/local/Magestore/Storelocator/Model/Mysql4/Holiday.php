<?php

class Magestore_Storelocator_Model_Mysql4_Holiday extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the storepickup_id refers to the key field in your database table.
        $this->_init('storelocator/holiday', 'storelocator_holiday_id');
    }
}