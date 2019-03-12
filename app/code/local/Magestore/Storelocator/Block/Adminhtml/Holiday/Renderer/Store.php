<?php 
class Magestore_Storelocator_Block_Adminhtml_Holiday_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $store = $row->getStoreId();
        $storeIds = explode(",", $store);
        $options = array();
        $store = Mage::getModel('storelocator/storelocator');
            foreach($storeIds as $storeId){
                $store->load($storeId);
                $options[$store->getId()] = $store->getName();
            }
        $result = implode(', ',$options);
        return $result;        
    }
}