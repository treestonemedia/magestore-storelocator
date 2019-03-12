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
 * Storelocator Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Model_Storelocator extends Mage_Core_Model_Abstract {

    protected $_store_id = null;
    
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'storelocator';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'storelocator'; 
    
    
    
    public function _construct() {
        parent::_construct();
        if ($storeId = Mage::app()->getRequest()->getParam('store')) {
            $this->setStoreId($storeId);
        }
        $this->_init('storelocator/storelocator');
    }

    // Load StoreId (multi store) to set data multi store
    protected function _beforeSave() {
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $idPath = array('neq' => 'storelocator/' . $store->getStoreId() . '/' . $this->getId());
            while (true) {
                if ((version_compare(Mage::getVersion(), '1.13', '>=')) && (version_compare(Mage::getVersion(), '1.14', '<'))) {
                    $rewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite')->getCollection()
                            ->addFieldToFilter('identifier', $idPath)
                            ->addFieldToFilter('request_path', $this->getRewriteRequestPath())
                            ->addFieldToFilter('store_id', $store->getStoreId())
                            ->getFirstItem();
                } else if ((version_compare(Mage::getVersion(), '1.13', '>='))) {
                    $rewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite')->getCollection()
                            ->addFieldToFilter('identifier', $idPath)
                            ->addFieldToFilter('request_path', $this->getRewriteRequestPath())
                            ->addFieldToFilter('store_id', $store->getStoreId())
                            ->getFirstItem();
                } else {
                    $rewrite = Mage::getModel('core/url_rewrite')->getCollection()
                            ->addFieldToFilter('id_path', $idPath)
                            ->addFieldToFilter('request_path', $this->getRewriteRequestPath())
                            ->addFieldToFilter('store_id', $store->getStoreId())
                            ->getFirstItem();
                }

                if (!$rewrite->getId()) {
                    break;
                }
                $this->setRewriteRequestPath($this->getRewriteRequestPath() . '-' . $this->getId());
            }
        }

        if ($storeId = $this->getStoreId()) {
            $defaultStore = Mage::getModel('storelocator/storelocator')->load($this->getId());
            if ($defaultStore->getId()) {
                $storeAttributes = $this->getStoreAttributes();
                foreach ($storeAttributes as $attribute) {
                    if ($this->getData($attribute . '_default')) {
                        $this->setData($attribute . '_in_store', false);
                    } else {
                        $this->setData($attribute . '_in_store', true);
                        $this->setData($attribute . '_value', $this->getData($attribute));
                    }
                    $this->setData($attribute, $defaultStore->getData($attribute));
                }
            }
        }

        return parent::_beforeSave();
    }

    protected function _afterSave() {
        if ($storeId = $this->getStoreId()) {
            $storeAttributes = $this->getStoreAttributes();

            foreach ($storeAttributes as $attribute) {
                $attributeValue = Mage::getModel('storelocator/storevalue')
                        ->loadAttributeValue($this->getId(), $storeId, $attribute);
                if ($this->getData($attribute . '_in_store')) {
                    try {
                        $attributeValue->setValue($this->getData($attribute . '_value'))->save();
                    } catch (Exception $e) {
                        
                    }
                } elseif ($attributeValue && $attributeValue->getId()) {
                    try {
                        $attributeValue->delete();
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
        return parent::_afterSave();
    }

    public function getStoreId() {
        return $this->_store_id;
    }

    public function setStoreId($id) {
        $this->_store_id = $id;
        return $this;
    }

    public function getStoreAttributes() {
        return array(
            'name',
            'status',
            'sort',
            'description',
            'address',
            'city',
        );
    }

    //info multistore
    public function load($id, $field = null) {
        parent::load($id, $field);
        if ($this->getStoreId()) {
            $this->getMultiStoreValue();
        }

        return $this;
    }

    public function getMultiStoreValue($storeId = null) {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        if (!$storeId) {
            return $this;
        }
        $storeValues = Mage::getModel('storelocator/storevalue')->getCollection()
                ->addFieldToFilter('storelocator_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }
        return $this;
    }

    public function save() {

        if (!$this->getLatitude() || !$this->getLongtitude()) {
            $address['street'] = $this->getAddress();
            $address['city'] = $this->getCity();
            $address['region'] = $this->getState();
            $address['zipcode'] = $this->getZipcode();
            $address['country'] = $this->getCountry();
            $coordinates = Mage::getModel('storelocator/gmap')
                    ->getCoordinates($address);
            if ($coordinates) {
                $this->setLatitude($coordinates['lat']);
                $this->setLongtitude($coordinates['lng']);
            } else {
                $this->setLatitude('0.000');
                $this->setLongtitude('0.000');
            }
        }
        return parent::save();
    }

    public function getCountryName() {
        if ($this->getCountry()){
            if (!$this->hasData('country_name')) {
                if(strlen($this->getCountry())!=2){
                    $this->setData('country_name', $this->getCountry());
                }else{
                    $country = Mage::getModel('directory/country')->loadByCode($this->getCountry());
                    $this->setData('country_name', $country->getName());
                }
            }
        }
        return $this->getData('country_name');
    }

    public function getRegion() {
        if (!$this->getData('region')) {
            $this->setData('region', $this->getState());
        }

        return $this->getData('region');
    }

    public function updateUrlKey($rewriteRequestPath = '') {
        $id = $this->getId();
        $store_id = $this->getStoreId();
        if (!$store_id) {
            $store_id = 0;
        }
        $url_key = $rewriteRequestPath ? $rewriteRequestPath : $this->getData('rewrite_request_path');

        if ((version_compare(Mage::getVersion(), '1.13', '>=')) && (version_compare(Mage::getVersion(), '1.14', '<'))) {
            $urlrewrite = $this->loadByIdpath('storelocator/' . $store_id . '/' . $id, $store_id);
            $urlrewrite->setData('identifier', 'storelocator/' . $store_id . '/' . $id);
            $urlrewrite->setData('entity_type', 1);
            $urlrewrite->setData('is_system', 1);
            $urlrewrite->setData('request_path', $url_key);
            $urlrewrite->setData('target_path', 'storelocator/index/view/id/' . $id);
        } else if ((version_compare(Mage::getVersion(), '1.13', '>='))) {
            $urlrewrite = $this->loadByIdpath('storelocator/' . $store_id . '/' . $id, $store_id);
            $urlrewrite->setData('identifier', 'storelocator/' . $store_id . '/' . $id);
            $urlrewrite->setData('entity_type', 1);
            $urlrewrite->setData('is_system', 1);
            $urlrewrite->setData('request_path', $url_key);
            $urlrewrite->setData('target_path', 'storelocator/index/view/id/' . $id);
            $urlrewrite->setData('store_id', $store_id);
        } else {
            $urlrewrite = $this->loadByIdpath('storelocator/' . $store_id . '/' . $id, $store_id);
            $urlrewrite->setData('id_path', 'storelocator/' . $store_id . '/' . $id);
            $urlrewrite->setData('request_path', $url_key);
            $urlrewrite->setData('target_path', 'storelocator/index/view/id/' . $id);
            $urlrewrite->setData('store_id', $store_id);
        }

        try {
            $urlrewrite->save();
        } catch (Exception $e) {
            
        }
    }

    public function loadByIdpath($idPath, $storeId) {
        if ((version_compare(Mage::getVersion(), '1.13', '>=')) && (version_compare(Mage::getVersion(), '1.14', '<'))) {
            $model = Mage::getModel('enterprise_urlrewrite/url_rewrite')->getCollection()
                    ->addFieldToFilter('identifier', $idPath)
                    ->getFirstItem();
        } else if ((version_compare(Mage::getVersion(), '1.13', '>='))) {
            $model = Mage::getModel('enterprise_urlrewrite/url_rewrite')->getCollection()
                    ->addFieldToFilter('identifier', $idPath)
                    ->addFieldToFilter('store_id', $storeId)
                    ->getFirstItem();
        } else {
            $model = Mage::getModel('core/url_rewrite')->getCollection()
                    ->addFieldToFilter('id_path', $idPath)
                    ->addFieldToFilter('store_id', $storeId)
                    ->getFirstItem();
        }
        return $model;
    }

    public function loadByRequestPath($requestPath, $storeId) {
        if ((version_compare(Mage::getVersion(), '1.13', '>='))) {
            $model = Mage::getModel('enterprise_urlrewrite/url_rewrite');
        } else {
            $model = Mage::getModel('core/url_rewrite');
        }
        $collection = $model->getCollection();
        $collection->addFieldToFilter('request_path', $requestPath);
        if ($storeId && !(version_compare(Mage::getVersion(), '1.13', '>=')) && (version_compare(Mage::getVersion(), '1.14', '<')))
            $collection->addFieldToFilter('store_id', $storeId);
        if ($collection->getSize()) {
            $model = $collection->getFirstItem();
        }
        return $model;
    }

}
