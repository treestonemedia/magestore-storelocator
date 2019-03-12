<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Magestore_Storelocator_Model_Exporter extends Varien_Object{
    
    protected $_fieldstr = 'name,status,address,city,state,country,zipcode,latitude,longtitude,fax,phone,email,link,zoom_level,image_name,image_icon,tag_store,monday_status,monday_open,monday_close,tuesday_status,tuesday_open,tuesday_close,wednesday_status,wednesday_open,wednesday_close,thursday_status,thursday_open,thursday_close,friday_status,friday_open,friday_close,saturday_status,saturday_open,saturday_close,sunday_status,sunday_open,sunday_close,monday_open_break,monday_close_break,tuesday_open_break,tuesday_close_break,wednesday_open_break,wednesday_close_break,thursday_open_break,thursday_close_break,friday_open_break,friday_close_break,saturday_open_break,saturday_close_break,sunday_open_break,sunday_close_break';
    public function exportStoreLocator(){        
        $stores = Mage::getModel('storelocator/storelocator')->getCollection();
        
        if(!count($stores))
        {
            return false;
        }
        
        foreach ($stores as $store){
            $data[] = $this->getStoreData($store);
        }
        $csv = '';
        $csv .= $this->_fieldstr. "\n";
        foreach ($data as $item){
            $rowstr = implode('","', $item);
            $rowstr = '"'.$rowstr.'"';
            $csv .= $rowstr."\n";
        }
        return $csv;
    }
    public function getXmlStoreLocator(){
        
        $stores = MAge::getModel('storelocator/storelocator')->getCollection();
        $storeCollections = array();
        
        if(!count($stores)){
            return false;
        }
        
        foreach ($stores as $item){
            $data = $this->getStoreData($item);
            $item->setData($data);
            $storeCollections[] = $item;
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($storeCollections as $item) {
            $xml.= $item->toXml();
        }
        $xml.= '</items>';	
		
        return $xml;
    }
    
    public function getStoreData($store)
	{
		$data = $store->getData();
		//prepare location
                $data['tag_store'] = Mage::helper('storelocator')->getTags($store->getId());
                $data['image_name'] = Mage::helper('storelocator')->getImageNameByStore($store->getId());
                if($data['status'] == 1){
                    $data['status'] = 'Enabled';
                }else{
                    $data['status'] = 'Disabled';
                }
		$fields = $this->_getFields();
		
		$export_data = array();
		foreach($fields as $field)
		{
			$value = isset($data[$field]) ? $data[$field] : '';
			$export_data[$field] = $value;
		}
		
		return $export_data;
	}
	
	protected function _getFields()
	{
		if(! $this->getData('fields'))
		{
			$fields = explode(',',$this->_fieldstr);
			$this->setData('fields',$fields);
		}
		
		return $this->getData('fields');
	}
}
?>
