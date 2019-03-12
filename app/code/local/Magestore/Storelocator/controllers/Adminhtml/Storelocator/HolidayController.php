<?php

class Magestore_Storelocator_Adminhtml_storelocator_HolidayController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('storelocator/holidays')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Holiday Management'), Mage::helper('adminhtml')->__('Holiday Management'));

        return $this;
    }

    public function indexAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('storelocator/holiday')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('holiday_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('storelocator/holidays');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Holiday Management'), Mage::helper('adminhtml')->__('Holiday Management'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('New Holiday'), Mage::helper('adminhtml')->__('New Holiday'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_holiday_edit'))
                    ->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_holiday_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('storelocator/holiday');
//            if($data['store_id']){    
//                $data['store_id'] = implode(',',$data['store_id']);
//               }
            if ($this->getRequest()->getParam('id')) {
                $holidays = $model->getCollection()
                        ->addFieldToFilter('storelocator_holiday_id', array('nin' => $this->getRequest()->getParam('id')))
//                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('holiday_date_to', $data['holiday_date_to']);
            } else {
                $holidays = $model->getCollection()
//                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('holiday_date_to', $data['holiday_date_to']);
            }
            foreach ($holidays as $_h) {
                if ($_h->getStoreId() == $data['store_id']) {
                        $flag = true;break;
                }
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
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Holiday exists'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storelocator/holiday');

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

    public function massDeleteAction() {
        $storelocatorIds = $this->getRequest()->getParam('holiday');
        if (!is_array($storelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getModel('storelocator/holiday')->load($storelocatorId);
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
        $fileName = 'holiday.csv';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_holiday_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'holiday.xml';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_holiday_grid')
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
