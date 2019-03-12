<?php
class Magestore_Storelocator_Block_Adminhtml_Region extends Mage_Core_Block_Template
{
	public function getStore()
	{
		$collection = null;
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
			$collection = Mage::getModel('storelocator/storelocator')->load($id);
		}
		return $collection;
	}
}
?>