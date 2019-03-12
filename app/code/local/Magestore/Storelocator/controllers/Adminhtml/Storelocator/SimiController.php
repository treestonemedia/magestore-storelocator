<?php 
class Magestore_Storelocator_Adminhtml_storelocator_SimiController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
		$url = "https://www.simicart.com/usermanagement/checkout/buyProfessional/?extension=2&utm_source=magestorebuyer&utm_medium=backend&utm_campaign=Magestore Buyer Backend";
		Mage::app()->getResponse()->setRedirect($url)->sendResponse();
		exit();
	}
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storelocator');
    }
}