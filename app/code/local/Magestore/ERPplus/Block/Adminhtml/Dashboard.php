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
class Magestore_ERPplus_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template {
    
    /*
     * Get list of active apps
     * 
     * @return array
     */
    public function getApps() {
        if(!$this->hasData('active_apps')){
            $apps = $this->helper('erpplus')->getActiveApps();
            $this->setData('active_apps', $apps);
        }
        return $this->getData('active_apps');
    }
    
    /**
     * Get default page of app
     * 
     * @param string $app
     * @return string
     */
    public function getDefaultPage($app) {
        if($defaultPage = $this->_helper()->getAppConfig($app, 'default_page')){
            return $this->getUrl($defaultPage);
        }
        $appkey = str_replace('magestore_', '', strtolower($app));
        $menuPath = $appkey;
        if($appkey == 'webpos')
            $menuPath = 'sales/children/webpos';
        $menu = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu/' . $menuPath . '/children');
        if ($menu) {
            foreach ($menu->children() as $menuItem) {
                return $this->getUrl($menuItem->action);
            }
        }
    }
    
    /**
     * Get app icon
     * 
     * @param type $app
     * @return type
     */
    public function getAppIcon($app) {
        $icon = $this->_helper()->getAppConfig($app, 'icon');
        $icon = $icon ? $icon : str_replace('magestore_', '', strtolower($app));
        if (!file_exists($this->_getIconPath($icon.'.png'))) {
            $icon = 'default';
        }
        $path = $this->getSkinUrl('css/magestore/erp_plus/images/apps/' . $icon . '.png');
        return $path;
    }

    /**
     * 
     * @param string $icon
     * @return string
     */
    protected function _getIconPath($icon) {
        $baseDir = Mage::getBaseDir('skin') . DS . 'adminhtml' . DS . 'default' . DS . 'default' . DS . 'css' . DS . 'magestore' . DS . 'erp_plus' . DS . 'images' . DS . 'apps';
        $path = $baseDir . DS . $icon;
        return $path;
    }

    public function getAppCaption($app) {
        $aliasName = $this->_helper()->getAppConfig($app, 'alias_name');
        $caption = $aliasName ? $aliasName : str_replace('Magestore_', '', $app);
        return $caption;
    }
    
    /**
     * Get app version
     * 
     * @param string $appName
     * @return string
     */
    public function getAppVersion($appName){
        return $this->helper('erpplus')->getAppVersion($appName);
    }

    protected function _helper() {
        return $this->helper('erpplus');
    }
    
    /**
     * Get list of inactive apps
     * 
     * @return array
     */
    public function getAvailableApps(){
        $availApps = $this->helper('erpplus')->getAvailableApps();
        $activeApps = $this->getApps();
        $categories = array();
        if(count($availApps)){
            foreach($availApps as $key => $availApp){
                if(in_array($availApp['key'], array_keys($activeApps))){
                    $availApps[$key]['installed'] = true;
                } else {
                    $availApps[$key]['installed'] = false;
                }
                $categories[$availApp['group_id']] = $availApp['group_name'];
            }
        }
        ksort($categories);
        $this->setData('categories', $categories);
        return $availApps;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getIsShowList() {
        if($this->getRequest()->getParam('showlist')){
            return true;
        }
        return false;
    }
    
    public function isTrackShowEvent() {
        if(Mage::getStoreConfig(Magestore_ERPplus_Helper_Data::TRACH_EVENT_UPDATED_TIME)){
            return false;
        } else {
            Mage::getConfig()->saveConfig(Magestore_ERPplus_Helper_Data::TRACH_EVENT_UPDATED_TIME, now());
            return true;
        }
    }
    
    public function getTrackEventUrl() {
        return Magestore_ERPplus_Helper_Data::TRACK_EVENT_URL;
    }

}
