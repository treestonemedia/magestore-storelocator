<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Storelocator_Adminhtml_storelocator_Importcontroller extends Mage_Adminhtml_Controller_Action {

    public function initAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->loadLayout()
                ->_setActiveMenu('storelocator/stores')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
        return $this;
    }

    public function importstoreAction() {
        $this->loadLayout();
        $this->_setActiveMenu('storelocator/storelocator');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $editBlock = $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_import');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/storelocator_storelocator/index') . '\')');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/save', array()));

        $this->_addContent($editBlock)
                ->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_storelocator_import_tabs'));

        $this->renderLayout();
    }

    public function saveAction() {

        if (!empty($_FILES['csv_store']['tmp_name'])) {
            try {
                $number = Mage::getResourceModel('storelocator/storelocator')->import($this->getRequest()->getParam('overwrite_store'));
               Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('You\'ve successfully imported ') . $number['insert'] . Mage::helper('storelocator')->__(' new store(s) and updated ') . $number['update'] . ' ' . Mage::helper('storelocator')->__('store(s)'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Invalid file upload attempt'));
            }
            $this->_redirect('*/storelocator_storelocator/index');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Invalid file upload attempt'));
            $this->_redirect('*/*/importstore');
        }

    }

}

?>