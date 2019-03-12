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
class Magestore_ERPplus_Helper_Data extends Mage_Core_Helper_Abstract {

    const PRODUCT_FEED_URL = 'http://www.magestore.com/pfeed/erp/products.xml';
    const PRODUCT_FEED_UPDATED_TIME_PATH = 'erpplus/product_feed/updated_time';
    const PRODUCT_FEED_CONTENT_PATH = 'erpplus/product_feed/content';
    
    const TRACK_EVENT_URL = 'https://www.magestore.com/index.php/magestorefeed/log/new';
    const TRACH_EVENT_UPDATED_TIME = 'erpplus/track_event/updated_time';

    /**
     *
     * @var array
     */
    private $_ERPmoudles = array('inventoryplus', 
                                'rewardpoints', 
                                'storepickup', 
                                'pdfinvoiceplus', 
                                'onestepcheckout', 
                                'webpos', 
                                'affiliateplus', 
                                'storelocator', 
                                'customercredit',
                                'giftvoucher');

    /**
     *
     * @var array
     */
    private $_unapply_ERPlayout = array('inventoryplus');

    /**
     * 
     * @return string
     */
    public function getCurrentModule() {
        return Mage::registry('current_real_module_name');
    }

    /**
     * 
     * @return string
     */
    public function getCurrentModuleKey($moduleName = null) {
        $moduleName = $moduleName ? $moduleName : $this->getCurrentModule();
        $moduleName = str_replace('Magestore_', '', $moduleName);
        return strtolower($moduleName);
    }

    /**
     * 
     * @return string
     */
    public function getCurrentModuleName() {
        $moduleName = $this->getCurrentModule();
        if (!$moduleName)
            return null;
        $aliasName = $this->getAppConfig($moduleName, 'alias_name');
        $moduleName = $aliasName ? $aliasName : $moduleName;
        $moduleName = str_replace('Magestore_', '', $moduleName);
        return $moduleName;
    }

    /**
     * Check if the module is an app of ERP Plus
     * 
     * @return boolean
     */
    public function isERPmodule($moduleKey = null) {
        
        if($moduleKey) {
            $activeApps = $this->getActiveApps();
            if(isset($activeApps[$moduleKey])){
                return true;
            }
            return false;            
        }
        
        if (in_array($this->getCurrentModuleKey(), $this->_ERPmoudles)) {
            return true;
        }
        if ((bool) $this->getAppConfig($this->getCurrentModule(), 'isERPmodule')) {
            return true;
        }
        /* check plugins */
        /*
          $appInfo = Mage::getConfig()->getModuleConfig($this->getCurrentModule());
          if(isset($appInfo->depends)){
          $depends = array_keys($appInfo->depends->asArray());
          if(count($depends)){
          foreach($depends as $depend){
          if (in_array($this->getCurrentModuleKey($depend), $this->_ERPmoudles)) {
          return true;
          }
          if ((bool) $this->getAppConfig($depend, 'isERPmodule')) {
          return true;
          }
          }
          }
          }
         */
        /* end of checking plugins */
        return false;
    }

    /**
     * Get parent module
     * 
     * @param string $moduleName
     * @return string
     */
    public function getDependModule($moduleName) {
        $appInfo = Mage::getConfig()->getModuleConfig($moduleName);
        if (isset($appInfo->depends)) {
            $depends = array_keys($appInfo->depends->asArray());
            if (count($depends)) {
                foreach ($depends as $depend) {
                    return $depend;
                }
            }
        }
        return null;
    }

    /**
     * Check if apply ERP layout to module
     * 
     * @return boolean
     */
    public function isApplyERPlayout() {
        if (in_array($this->getCurrentModuleKey(), $this->_unapply_ERPlayout)) {
            return false;
        }
        return $this->isERPmodule();
    }

    /**
     * 
     * @param string $app (Magestore_Inventoryplus)
     * @param string $field
     * @return string
     */
    public function getAppConfig($app, $field) {
        $appInfo = Mage::getConfig()->getModuleConfig($app);
        if (isset($appInfo->erp)) {
            if (isset($appInfo->erp->$field))
                return (string) $appInfo->erp->$field;
        }
        return null;
    }

    /**
     * 
     * @return array
     */
    public function getActiveApps() {
        $activeApps = array();
        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleInfo) {
            if ($moduleName === 'Mage_Adminhtml') {
                continue;
            }
            if ($moduleName === 'Magestore_Magenotification') {
                continue;
            }
            if (strpos('a' . $moduleName, 'Magestore') == 0) {
                continue;
            }

            $moduleKey = str_replace('magestore_', '', strtolower($moduleName));

            if (!(bool) $this->getAppConfig($moduleName, 'isERPmodule')) {
                if (!in_array($moduleKey, $this->_ERPmoudles)) {
                    continue;
                }
            }

            $activeApps[$moduleKey] = $moduleName;
        }
        return $activeApps;
    }

    /**
     * Update layout of inventory configuration page
     * 
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function updateConfigLayout($controller, $layout) {
        $fullRequest = $controller->getFullActionName();
        $section = $this->getCurrentSectionConfig();
        $applied = false;
        if ($fullRequest != 'adminhtml_system_config_edit')
            return;
        if (in_array($section, $this->_unapply_ERPlayout))
            return;
        if (in_array($section, $this->_ERPmoudles))
            $applied = true;
        if ((bool) $this->getAppConfig('Magestore_' . ucwords($section), 'isERPmodule')) {  
            $applied = true;
        }      
        if ($applied) {
            $layout->getUpdate()->addHandle('adminhtml_erpplus_module_layout');
        }
    }

    /**
     * 
     * @return string
     */
    public function getCurrentSectionConfig() {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'carriers') {
            if (Mage::app()->getRequest()->getParam('storepickup') == 1) {
                $section = 'storepickup';
            }
        }
        return $section;
    }

    /**
     * Get list availabel apps
     * 
     * @return array
     */
    public function getAvailableApps() {
        $apps = array();
        try {
            $appXML = new Varien_Simplexml_Element($this->getProductFeed());
            $apps = $appXML->asArray();
            /* remove CDATA, group by category */
            if (count($apps)) {
                foreach ($apps as $key => $app) {
                    foreach ($app as $attribute => $value) {
                        $apps[$key][$attribute] = str_replace('<![CDATA[', '', str_replace(']]>', '', $value));
                    }
                }
            }
        } catch (Exception $e) {
            $this->getProductFeed(true);
        }
        return $apps;
    }

    /**
     * Get product data from Magestore
     * 
     */
    public function getProductFeed($needUpdate = false) {
        $lastUpdate = Mage::getStoreConfig(self::PRODUCT_FEED_UPDATED_TIME_PATH);
        $content = Mage::getStoreConfig(self::PRODUCT_FEED_CONTENT_PATH);
        if (!$lastUpdate) {
            $needUpdate = true;
        } else {
            $days = (strtotime(now()) - strtotime($lastUpdate)) / 86400;
            if ($days > 1) {
                $needUpdate = true;
            }
        }
        if (!$content) {
            $needUpdate = true;
        }
        if ($needUpdate) {
            try {
                $updateContent = (string) file_get_contents(self::PRODUCT_FEED_URL);
                Mage::getConfig()->saveConfig(self::PRODUCT_FEED_CONTENT_PATH, $updateContent);
                Mage::getConfig()->saveConfig(self::PRODUCT_FEED_UPDATED_TIME_PATH, now());
                $content = $updateContent;
            } catch (Exception $e) {
                
            }
        }
        return $content;
    }
    
    /**
     * Get using version of app
     * 
     * @param string $appName
     * @return string
     */
    public function getAppVersion($appName) {
        $appInfo = Mage::getConfig()->getModuleConfig($appName);
        $version = isset($appInfo->public_version) ? (string) $appInfo->public_version : (string) $appInfo->version;
        return $version;
    }

}
