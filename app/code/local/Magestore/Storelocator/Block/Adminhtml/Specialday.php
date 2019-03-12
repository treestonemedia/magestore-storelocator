<?php
class Magestore_Storelocator_Block_Adminhtml_Specialday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_specialday';
    $this->_blockGroup = 'storelocator';
    $this->_headerText = Mage::helper('storelocator')->__('Special Day Manager');
    $this->_addButtonLabel = Mage::helper('storelocator')->__('Add Special Day');
    parent::__construct();
  }
}