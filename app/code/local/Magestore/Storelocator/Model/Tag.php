<?php

class Magestore_Storelocator_Model_Tag extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('storelocator/tag');
    }

}