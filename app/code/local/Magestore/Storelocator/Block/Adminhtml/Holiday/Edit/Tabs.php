<?php

class Magestore_Storelocator_Block_Adminhtml_Holiday_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('holiday_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storelocator')->__('Holiday Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('storelocator')->__('Holiday Information'),
          'title'     => Mage::helper('storelocator')->__('Holiday Information'),
          'content'   => $this->getLayout()->createBlock('storelocator/adminhtml_holiday_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}