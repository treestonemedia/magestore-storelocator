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
 * Storelocator Edit Tabs Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('storelocator_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('storelocator')->__('Store Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tabs
	 */
	protected function _beforeToHtml(){
            $generalTab = new Varien_Object();
            $generalTab->setContent($this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_generalinfo')->toHtml());
            Mage::dispatchEvent('storelocator_general_information_tab_before', 
                    array('tab' => $generalTab,
                        'store_id' => $this->getRequest()->getParam('id')));
            
            $this->addTab('form_section', array(
                    'label'	 => Mage::helper('storelocator')->__('General Information'),
                    'title'	 => Mage::helper('storelocator')->__('General Information'),
                    'content'	 => $generalTab->getContent(),
            ));
              
            $this->addTab('gmap_section', array(
                'label'     => Mage::helper('storelocator')->__('Google Map'),
                'title'     => Mage::helper('storelocator')->__('Google Map'),
               'content'   => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_gmap')->toHtml(),

            ));
//            $this->addTab('product', array(
//                'label' => Mage::helper('storelocator')->__('Products'),
//                'url' => $this->getUrl('*/*/product', array('_current' => true)),
//                'content' => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_product')->toHtml(),
//                'class' => 'ajax',
//            ));
                 
            $this->addTab('timeschedule_section', array(
               'label' => Mage::helper('storelocator')->__('Time Schedule'),
               'title' => Mage::helper('storelocator')->__('Time Schedule'),
               'content' => $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tab_timeschedule')->toHtml(),
           ));
		return parent::_beforeToHtml();
	}
}