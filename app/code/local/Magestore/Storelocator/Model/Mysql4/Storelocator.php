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
 * Storelocator Resource Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Model_Mysql4_Storelocator extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('storelocator/storelocator', 'storelocator_id');
    }
    public function updateImages() {
        $stores = Mage::getModel('storelocator/storelocator')->getCollection();
        foreach ($stores as  $_store) {
            $images = explode(',', $_store->getImageIcon());
            foreach ($images as $_image) {
                
            }
        }
    }

    public function import($overwrite_store) {
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();
        $fileName = $_FILES['csv_store']['tmp_name'];
        $csvObject = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);
        $number = array('insert' => 0, 'update' => 0);
        $dataStore = array();
        $tags = array();
        $resource = Mage::getSingleton('core/resource');
        $storeTable = $resource->getTableName('storelocator/storelocator');
        $collectionRewrite = Mage::getModel('storelocator/storelocator')->getCollection();
        $urlPathArr = array();
        foreach ($collectionRewrite as $collection) {
            $urlPathArr[] = $collection->getRewriteRequestPath();
        }

        if ($csvData[0] == $csvFields||11) {
            $arrayUpdate = $this->csvGetArrName($csvData);
            $index_row = array();
            try {
                foreach ($csvData as $k => $v) {
                    if ($k == 0) {
                        $index_row = $v;
                        continue;
                    }
                    if (count($v) <= 1 && !strlen($v[0])) {
                        continue;
                    }

                    if (!empty($v[0])) {
                        $data = array();
                        for ($i = 0; $i < count($v); $i++) {
                            if($index_row[$i]!='tag_store')
                                $data[$index_row[$i]] = $v[$i];
                            if($index_row[$i]=='status')
                                $data[$index_row[$i]] = ($v[$i]=='Enabled')? 1 : 2;
                        }
                        if(!isset($data['rewrite_request_path'])||$data['rewrite_request_path']==''){
                            $urlRewrite = $data['name'];
                        }else $urlRewrite = $data['rewrite_request_path'];
                            $urlRewrite = strtolower(trim($urlRewrite));
                            $urlRewrite = Mage::helper('storelocator')->characterSpecial($urlRewrite);
                            $data['rewrite_request_path'] = $urlRewrite;
                        if (in_array($v[0], $arrayUpdate)) {
                            if ($overwrite_store) {
                                $number['update'] ++;
                                $write->update($storeTable, $data, 'name = "' . $data['name'] . '" and address = "'. $data['address'] . '"');
                                $model = Mage::getModel('storelocator/storelocator');
                                $collection = $model->getCollection()
                                        ->addFieldToFilter('name', array('eq' => $data['name']))
                                        ->addFieldToFilter('address', array('eq' => $data['address']))
                                        ->getLastItem();
                                 $flag = false;
                                while (true) {
                                    if (!in_array($urlRewrite, $urlPathArr)) {
                                        break;
                                    }
                                    $urlRewrite .= '-' . $collection->getId();
                                    $flag = true;
                                }
                                 if ($flag) {
                                    $model->setId($collection->getId())
                                    ->setRewriteRequestPath($urlRewrite)
                                    ->setLatitude($data['latitude'])
                                    ->setLongtitude($data['longtitude'])
                                    ->save();
                                }
                                $urlPathArr[] = $urlRewrite;
                                $stores = Mage::app()->getStores();
                                $model->setId($collection->getId());
                                foreach ($stores as $store) {
                                    $model->setStoreId($store->getStoreId())
                                            ->updateUrlKey($urlRewrite);
                                }

                                Mage::helper('storelocator')->deleteTagFormStore($collection->getId());
                                $tags = trim($v[16]);
                                $tag_arr = explode(',', $tags);
                                foreach ($tag_arr as $item) {
                                    $tag = Mage::getModel('storelocator/tag');
                                    $tagItem = trim($item);
                                    if (isset($tagItem)&&$item!='') {
                                        $tag->setData('value', $tagItem);
                                        $tag->setData('storelocator_id', $collection->getId());
                                        $tag->save();
                                    }
                                }
                            }
                            continue;
                        }

                        $dataStore[] = $data;
                        $tags[] = $v[16];
                        $number['insert'] ++;
                        if (count($dataStore) >= 200) {
                            $write->insertMultiple($storeTable, $dataStore);
                            $model = Mage::getModel('storelocator/storelocator');
                            $collection = $model->getCollection()
                                    ->getLastItem();
                            $storeId = $collection->getStorelocatorId() ? $collection->getStorelocatorId() : 1;

                            $tags = array_reverse($tags);
                            foreach ($tags as $tag) {
                                $tag = array_filter(explode(',', $tag));
                                Mage::helper('storelocator')->saveTagToStore($tag, $storeId);
                                $collection = $model->load($storeId);
                                $urlRewrite = $collection->getRewriteRequestPath();
                                $flag = false;
                                while (true) {
                                    if (!in_array($urlRewrite, $urlPathArr)) {
                                        break;
                                    }
                                    $urlRewrite .= '-' . $collection->getId();
                                    $flag = true;
                                }
                                if ($flag) {
                                    $model->setId($storeId);
                                    $model->setRewriteRequestPath($urlRewrite);
                                    $model->save();
                                }
                                $urlPathArr[] = $urlRewrite;

                                $stores = Mage::app()->getStores();
                                $model->setId($storeId);
                                foreach ($stores as $store) {
                                    $model->setStoreId($store->getStoreId())
                                            ->updateUrlKey($urlRewrite);
                                }

                                $storeId--;
                            }
                            $dataStore = array();
                        }
                    }
                }
                if (!empty($dataStore)) {

                    $write->insertMultiple($storeTable, $dataStore);

                    $model = Mage::getModel('storelocator/storelocator');
                    $collection = $model->getCollection()
                            ->getLastItem();
                    $storeId = $collection->getStorelocatorId() ? $collection->getStorelocatorId() : 1;

                    $tags = array_reverse($tags);
                    foreach ($tags as $tag) {

                        $tag = explode(',', $tag);
                        Mage::helper('storelocator')->saveTagToStore($tag, $storeId);

                        $collection = $model->load($storeId);
                        $urlRewrite = $collection->getRewriteRequestPath();
                        $flag = false;
                        while (true) {
                            if (!in_array($urlRewrite, $urlPathArr)) {
                                break;
                            }
                            $urlRewrite .= '-' . $collection->getId();
                            $flag = true;
                        }
                        if ($flag) {
                            $model->setId($storeId);
                            $model->setRewriteRequestPath($urlRewrite);
                            $model->save();
                        }
                        $urlPathArr[] = $urlRewrite;
                        $stores = Mage::app()->getStores();
                        $model->setId($storeId);
                        foreach ($stores as $store) {
                            $model->setStoreId($store->getStoreId())
                                    ->updateUrlKey($urlRewrite);
                        }
                        $storeId--;
                    }
                }
                $listStore = Mage::getModel('storelocator/storelocator')->getCollection();
                foreach ($listStore as $store) {
                    $images = explode(',', $store->getImageName());
                        $status1 = 1;
                        foreach ($images as $img) {
                            if($img!=null)
                            Mage::getModel('storelocator/image')
                                    ->setImageDelete(2)
                                    ->setOptions(1)
                                    ->setName($img)
                                    ->setStatuses($status1)
                                    ->setStorelocatorId($store->getId())
                                    ->save();
                            $status1 = 0;
                    }
                }
                $write->commit();
            } catch (Exception $e) {
                $write->rollback();
                throw $e;
            }
        } else {
            Mage::throwException(Mage::helper('storelocator')->__('Please follow the sample file\'s format to import stores properly.'));
        }
        return $number;
    }

    public function csvGetArrName($csvData) {
        $array = array();
        foreach ($csvData as $k => $v) {
            if ($k == 0) {
                continue;
            }
            $array[] = $v[0];
        }
        $collections = Mage::getModel('storelocator/storelocator')
                ->getCollection()
                ->addFieldToFilter('name', array('in' => $array))
                ->getAllField('name');
        return $collections;
    }

}
