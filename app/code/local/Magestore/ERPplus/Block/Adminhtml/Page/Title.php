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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_ERPplus
 * @author      Magestore Developer
 */
class Magestore_ERPplus_Block_Adminhtml_Page_Title extends Mage_Adminhtml_Block_Template {
    
    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('erp_plus/page/title.phtml');
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle() {
        $moduleName = $this->helper('erpplus')->getCurrentModuleName();
        if($moduleName == 'Mage'){
            $moduleKey = $this->helper('erpplus')->getCurrentSectionConfig();
            $moduleName = uc_words($moduleKey);
        }
        if(strtolower($moduleName) == 'erpplus'){
            return 'ERP Plus';
        }
        return 'ERP Plus | '.$moduleName;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isInDashboard() {
        if( $this->getRequest()->getActionName() == 'dashboard'
            && $this->getRequest()->getControllerName() == 'erpplus')
            return true;
        return false;
    }
}