<?php

class Magestore_Storelocator_Adminhtml_storelocator_GuideController extends Mage_Adminhtml_Controller_action
{

    public function indexAction() {            
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Store Locator Guide'));
        $this->renderLayout();
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storelocator');
    }
}