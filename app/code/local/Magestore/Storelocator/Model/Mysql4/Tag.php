<?php

class Magestore_Storelocator_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('storelocator/tag', 'tag_id');
    }
}