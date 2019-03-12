<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Storelocator Index Controller
 *
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_IndexController extends Mage_Core_Controller_Front_Action {

	/**
	 * index action
	 */
	public function indexAction() {
		if (Mage::helper('storelocator')->getConfig('enable') != 1) {
			Mage::getSingleton('core/session')->addError($this->__("Storelocator program is disabled or doesn't exist"));
			$this->getResponse()->setRedirect(Mage::getBaseUrl());
		}
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('storelocator')->getConfig('page_title'));
		$this->renderLayout();
	}
	public function viewAction() {
		if (Mage::helper('storelocator')->getConfig('enable') != 1) {
			Mage::getSingleton('core/session')->addError($this->__("Storelocator program is disabled or doesn't exist"));
			$this->getResponse()->setRedirect(Mage::getBaseUrl());
		}
		$id = $this->getRequest()->getParam('id');
		$storeId = Mage::app()->getStore()->getStoreId();
		$collection = Mage::getModel('storelocator/storelocator')->setStoreId($storeId)->load($id);
		$metaTitle = $collection->getMetaTitle() ? $collection->getMetaTitle() : $collection->getName();
		$metaContents = $collection->getMetaContents() ? $collection->getMetaContents() : $collection->getName();
		$metaKeywords = $collection->getMetaKeywords() ? $collection->getMetaKeywords() : $collection->getName();
		$this->loadLayout();
		$head = $this->getLayout()->getBlock('head');
		$head->setTitle($metaTitle);
		$head->setDescription($metaContents);
		$head->setKeywords($metaKeywords);
		$this->renderLayout();
	}
	function backAction() {
		$url = Mage::app()->getStore()->getUrl('storelocator/index/index');
		$this->_redirectUrl($url);
	}
	public function loadstateAction() {
		$countryCode = $this->getRequest()->getParams('country');
		$states = Mage::getModel('directory/region')
			->getResourceCollection()
			->addCountryFilter($countryCode)
			->addFieldToSelect(array('region_id', 'default_name'))->getData();
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($states));
	}
        public function testAction(){
            $this->loadLayout();
            $this->renderLayout();
        }

}
