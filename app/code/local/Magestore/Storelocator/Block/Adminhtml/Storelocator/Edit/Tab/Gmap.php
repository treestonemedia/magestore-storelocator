<?php

class Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Gmap extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		if (Mage::getSingleton('adminhtml/session')->getStorelocatorData()) {
			$data = Mage::getSingleton('adminhtml/session')->getStorelocatorData();
			Mage::getSingleton('adminhtml/session')->setStorelocatorData(null);
		} elseif (Mage::registry('storelocator_data')) {
			$data = Mage::registry('storelocator_data')->getData();
		}

		$fieldset = $form->addFieldset('storelocator_form', array('legend' => Mage::helper('storelocator')->__('Google Map')));
		$fieldset->addField('zoom_level', 'text', array(
			'label' => Mage::helper('storelocator')->__('Zoom Level'),
			'name' => 'zoom_level',
		));
		$fieldset->addField('latitude', 'text', array(
			'label' => Mage::helper('storelocator')->__('Store Latitude'),
			'name' => 'latitude',
		));
		$fieldset->addField('longtitude', 'text', array(
			'label' => Mage::helper('storelocator')->__('Store Longitude'),
			'name' => 'longtitude',
		));

		if (isset($data['image_icon']) && $data['image_icon']) {
			$data['image_icon'] = 'storelocator/images/icon/' . $data['image_icon'];
		}
		$fieldset->addField('image_icon', 'image', array(
			'label' => Mage::helper('storelocator')->__('Store Icon'),
			'note' => 'Shown on Google Map<br/>Recommended size: 400x600 px. Supported format: jpeg, png, gif',
			'name' => 'image_icon',
		));
		$fieldset->addField('gmap', 'text', array(
			'label' => Mage::helper('storelocator')->__('Store Map'),
			'name' => 'gmap',
		))->setRenderer($this->getLayout()->createBlock('storelocator/adminhtml_gmap'));

		$form->setValues($data);

		return parent::_prepareForm();
	}
}
