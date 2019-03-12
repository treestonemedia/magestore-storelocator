<?php
class Magestore_Storelocator_Block_Adminhtml_Holiday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {   
    $this->_controller = 'adminhtml_holiday';
    $this->_blockGroup = 'storelocator';
    $this->_headerText = Mage::helper('storelocator')->__('Holiday Manager');
    $this->_addButtonLabel = Mage::helper('storelocator')->__('Add Holiday');
    parent::__construct();
  }
}