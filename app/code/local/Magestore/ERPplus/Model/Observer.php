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
 * @category    Magestore
 * @package     Magestore_Erp
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_ERPplus_Model_Observer {
    
    /**
     * Apply ERP layout to all modules of ERP system
     * 
     * @param type $observer
     */
    public function controller_action_layout_load_before($observer) {
        $controller = $observer->getEvent()->getAction();
        $layout = $observer->getEvent()->getLayout();        
        $class = get_class($controller);
        $realModuleName = substr(
                $class, 0, strpos(strtolower($class), '_adminhtml_' . strtolower($controller->getRequest()->getControllerName() . 'Controller'))
        );
        /* Replace by module that current module depends to */
        if($parentModule = Mage::helper('erpplus')->getDependModule($realModuleName)){
            $realModuleName = $parentModule;
        }
        
        if(!Mage::registry('current_real_module_name')){
            Mage::register('current_real_module_name', $realModuleName);
        }

        /* apply erp layout to modules in ERP system */
        if(Mage::helper('erpplus')->isApplyERPlayout()){
            $layout->getUpdate()->addHandle('adminhtml_erpplus_module_layout');
        }
        
        /* apply erp layout to configuration pages */
        Mage::helper('erpplus')->updateConfigLayout($controller, $layout);        
    }
    
    /**
     * Change title of Inventoryplus
     * 
     * @param type $observer
     */
    public function inventoryplus_before_show_title($observer) {
        $title = $observer->getEvent()->getTitle();
        $text = '<h3><a href="javascript:void(0);" onclick="showDashboardMenu();">'
                . '<span><i class="fa fa-th"></i> ERP Plus | Inventory Management </span></a></h3>';
        $text .=  '<div id="erp_menu_dashboard">'
                . Mage::app()->getLayout()->createBlock('erpplus/adminhtml_dashboard')
                            ->setTemplate('erp_plus/page/dashboard-menu.phtml')
                            ->toHtml()
                . '</div>';
        $title->setText($text);
    }

}
