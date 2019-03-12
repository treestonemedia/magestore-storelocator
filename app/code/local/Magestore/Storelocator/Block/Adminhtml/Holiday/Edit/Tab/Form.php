<?php

class Magestore_Storelocator_Block_Adminhtml_Holiday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('holiday_form', array('legend' => Mage::helper('storelocator')->__('Holiday Information')));
        $image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';
        
        $fieldset->addField('holiday_name', 'text', array(
            'label' => Mage::helper('storelocator')->__('Name'),
            'required' => true,
            'name' => 'holiday_name'
        ));
        
        $fieldset->addField('store_id', 'multiselect', array(
            'label' => Mage::helper('storelocator')->__('Store'),
            'class' => 'required-entry',
            'required' => true,
            'name' => '',
            'values' => Mage::helper('storelocator')->getStoreOptions(),
            'note'  => Mage::helper('storelocator')->__('<script>$("store_id").removeAttribute("name");$("store_id").observe("change",function(){$("store_id1").value = $F("store_id").join()})</script>Select stores applied holiday(s).'),
        ));
        $fieldset->addField('store_id1', 'hidden', array(
            'name' => 'store_id',
            'note'  => Mage::helper('storelocator')->__('<script>document.observe("dom:loaded",function(){$("store_id1").value = $F("store_id").join()});</script>'),
        ));

        $fieldset->addField('date', 'date', array(
            'label' => Mage::helper('storelocator')->__('From Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'date',
        ));

        $fieldset->addField('holiday_date_to', 'date', array(
            'label' => Mage::helper('storelocator')->__('To Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'holiday_date_to',
        ));

        $fieldset->addField('comment', 'textarea', array(
            'name' => 'comment',
            'label' => Mage::helper('storelocator')->__('Comment'),
            'title' => Mage::helper('storelocator')->__('Comment'),
            
            //'style' => 'width:500px; height:100px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getHolidayData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getHolidayData());
            Mage::getSingleton('adminhtml/session')->setHolidayData(null);
        } elseif (Mage::registry('holiday_data')) {
            $form->setValues(Mage::registry('holiday_data')->getData());
        }
        return parent::_prepareForm();
    }

}
