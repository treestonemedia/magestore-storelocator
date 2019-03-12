<?php

class Magestore_Storelocator_Adminhtml_storelocator_SpecialdayController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('storelocator/specialday')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Special Day Management'), Mage::helper('adminhtml')->__('Special Day Management'));

        return $this;
    }

    public function indexAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('storelocator/specialday')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('specialday_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('storelocator/specialday');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Specialday Manager'), Mage::helper('adminhtml')->__('Specialday Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('New Special Day'), Mage::helper('adminhtml')->__('New Special Day'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_specialday_edit'))
                    ->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_specialday_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storelocator/specialday');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('storelocator/specialday');
//            if ($data['store_id']) {
//                $data['store_id'] = implode(',', $data['store_id']);
//            }
            if ($this->getRequest()->getParam('id')) {
                $specialdays = $model->getCollection()
                        ->addFieldToFilter('storelocator_specialday_id', array('nin' => $this->getRequest()->getParam('id')))
//                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('specialday_date_to', $data['specialday_date_to']);
            } else {
                $specialdays = $model->getCollection()
//                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('specialday_date_to', $data['specialday_date_to']);
            }
            foreach ($specialdays as $_h) {
                if ($_h->getStoreId() == $data['store_id']) {
                        $flag = true;break;
                }
            }
            if ($data['specialday_time_open']) {
                $data['specialday_time_open'] = implode(':', $data['specialday_time_open']);
            }
            if ($data['specialday_time_close']) {
                $data['specialday_time_close'] = implode(':', $data['specialday_time_close']);
            }
            if (empty($flag)) {
                $model->addData($data)
                        ->setId($this->getRequest()->getParam('id'));
                try {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('Item was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Existing Special Day'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $storelocatorIds = $this->getRequest()->getParam('specialday');
        if (!is_array($storelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getModel('storelocator/specialday')->load($storelocatorId);
                    $storelocator->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($storelocatorIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'specialday.csv';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_specialday_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'specialday.xml';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_specialday_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storelocator');
    }
}
