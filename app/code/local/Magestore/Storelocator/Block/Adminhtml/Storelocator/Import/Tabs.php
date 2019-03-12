<?php

class Magestore_Storelocator_Block_Adminhtml_Storelocator_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importstore_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storelocator')->__('Import Store'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('storelocator')->__('Import Store'),
          'title'     => Mage::helper('storelocator')->__('Import Store'),
          'content'   => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}