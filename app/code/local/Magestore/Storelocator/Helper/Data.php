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
 * Storelocator Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getTablePrefix() {
        $tableName = Mage::getResourceModel('storelocator/storelocator')->getTable('storelocator');

        $prefix = str_replace('storelocator', '', $tableName);

        return $prefix;
    }

    public function getAttributeCode() {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId)
            $storeId = 0;
        $attributeCode = Mage::getStoreConfig('shopbybrand/general/attribute_code', $storeId);
        return $attributeCode ? $attributeCode : 'manufacturer';
    }

    public function saveIcon($flie, $id) {

        $this->createImageIcon($flie, $id);
    }

    /**
     * save images Store
     */
    public function saveImageStore($images, $id, $file, $radio) { //$mod = Mage::getModel('storelocator/image');exit;
        foreach ($images as $item) {
            $mod = Mage::getModel('storelocator/image');
            $file_name = $file['images_id' . $item['options']]['name'];
            $name_image = $this->renameImage($file_name, $id, $item['options']);
            if ($item['delete'] == 0) {
                $last = $mod->getCollection()->getLastItem()->getData('options') + 1;
                $mod->setData('storelocator_id', $id);
                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('name', $name_image);
                    $this->createImage($name_image, $id, $last, $item['options']);
                }
                if ($item['options'] == $radio) {
                    $mod->setData('statuses', 1);
                } else {
                    $mod->setData('statuses', 0);
                }
                $mod->setData('image_delete', 2);
                $mod->setData('options', $last);
                $mod->save();
            } else if ($item['delete'] == 2) {
                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('name', $name_image)->setId($item['id']);
                    $this->createImage($name_image, $id, $item['options'], $item['options']);
                }
                //$mod->setData('link', $item['link'])->setId($item['id']);    
                if ($item['options'] == $radio) {
                    $mod->setData('statuses', 1);
                } else {
                    $mod->setData('statuses', 0);
                }
                $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                $mod->save();
            } else {
                if ($item['id'] != 0) {
                    if (($name_image != "") && isset($name_image) != NULL) {
                        $mod->setData('name', $name_image)->setId($item['id']);
                        $this->createImage($name_image, $id, $item['options'], $item['options']);
                    }
                    if ($item['options'] == $radio) {
                        $mod->setData('statuses', 1);
                    } else {
                        $mod->setData('statuses', 0);
                    }
                    $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                    $mod->save();
                }
            }
        }
        $this->deleteImageStore();
    }

    private function renameImage($image_name, $store_id, $id_img) {

        $name = "";
        if (isset($image_name) && ($image_name != null)) {
            $array_name = explode('.', $image_name);
            $array_name[0] = $store_id . '_' . $id_img;
            $name = $array_name[0] . '.' . end($array_name);
        }
        return $name;
    }

    /**
     * 
     * @param type $url
     * call response return content
     */
    public function getResponseBody($url) {
        if (ini_get('allow_url_fopen') != 1) {
            @ini_set('allow_url_fopen', '1');
        }

        if (ini_get('allow_url_fopen') != 1) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $contents = curl_exec($ch);
            curl_close($ch);
        } else {
            $contents = file_get_contents($url);
        }

        return $contents;
    }

    public function getConfig($nameConfig) {
        return Mage::getStoreConfig('storelocator/general/' . $nameConfig);
    }

    /**
     * return list country in magento
     */
    public function getOptionCountry() {
        $optionCountry = array();
        $collection = Mage::getResourceModel('directory/country_collection')
                ->loadByStore();
        if (count($collection)) {
            foreach ($collection as $item) {
                $optionCountry[] = array('label' => $item->getName(), 'value' => $item->getId());
            }
        }
        return $optionCountry;
    }

    public function tagArray() {
        $tag_array = array();
        $taglist = "";
        $collection = Mage::getModel('storelocator/storelocator')->getCollection();
        //$taglist->getSelect()->group('tag_store');
        foreach ($collection as $tag) {
            $taglist = $taglist . $tag;
        }
        $tag_array = explode(",", $taglist);
    }

    public function getListCountry() {
        $listCountry = array();

        $collection = Mage::getResourceModel('directory/country_collection')
                ->loadByStore();

        if (count($collection)) {
            foreach ($collection as $item) {
                $listCountry[$item->getId()] = $item->getName();
            }
        }

        return $listCountry;
    }

    /**
     * 
     * @param type $name
     * return url to show image Store with big image
     */
    public function getBigImagebyStore($id_store, $base = false) {
        $collection = Mage::getModel('storelocator/image')->getCollection()->addFieldToFilter('storelocator_id', $id_store)->addFieldToFilter('image_delete', 2);
        $url = "";
        foreach ($collection as $item) {
            if ($item->getData('name')) {
                if ($item->getData('statuses') == 1) {
                    $url = Mage::getBaseUrl('media') . 'storelocator/images/' . $item->getData('name');
                    break;
                } elseif(!$base) {
                    $url = Mage::getBaseUrl('media') . 'storelocator/images/' . $item->getData('name');
                }
            }
        }
        return $url;
    }

    /**
     * delete image (back-end)
     */
    public function deleteImageStore() {
        $image_info = Mage::getModel('storelocator/image')->getCollection()->addFilter('image_delete', 1);
        foreach ($image_info as $item) {
            $id = $item->getData('storelocator_id');
            $option = $item->getData('options');
            $image = $item->getData('name');

            $image_path = $this->getImagePath($id, $option) . DS . $image;
            $image_path_cache = $this->getImagePathCache($id, $option) . DS . $image;
            try {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                if (file_exists($image_path_cache)) {
                    unlink($image_path_cache);
                }
            } catch (Exception $e) {
                
            }
        }
    }

    public function getDataImage($id) {
        $collection = Mage::getModel('storelocator/image')->getCollection()->addFilter('storelocator_id', $id);
        return $collection;
    }

    public function getImageUrlJS() {
        $url = Mage::getBaseUrl('media') . 'storelocator/images/';
        return $url;
    }

    public function getImagePath($store_id, $options) {
        $path = Mage::getBaseDir('media') . DS . 'storelocator' . DS . 'images' . DS . $store_id . DS . $options;
        return $path;
    }

    public function getImagePathCache($id, $options) {
        $path = Mage::getBaseDir('media') . DS . 'storelocator' . DS . 'images' . DS . 'cache' . DS . $id . DS . $options;
        return $path;
    }

    public function createImage($image, $id, $last, $options) {
        try {
            /* Starting upload */
            $uploader = new Varien_File_Uploader('images_id' . $options);
            // Any extention would work
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            // We set media as the upload dir
            $path = $this->getImagePath($id, $last);
            $uploader->save($path, $image);
        } catch (Exception $e) {
            
        }
    }

    public function createImageIcon($flie, $id) {
        try {
            /* Starting upload */
            $uploader = new Varien_File_Uploader($flie);
            // Any extention would work
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            
            // We set media as the upload dir
            $path = $this->getPathImageIcon($id);
            $uploader->save($path, $flie['name']);
            $this->reSizeImage($id, $flie['name']);
        } catch (Exception $e) {
            
        }
    }

    public function deleteImageIcon($id, $image) {
        $image_path = Mage::getBaseDir('media') . DS . 'storelocator' . DS . 'images' . DS . 'icon' . DS . $id . DS . $image;
        try {
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        } catch (Exception $e) {
            
        }
    }

    public function getPathImageIcon($id) {
        $path = Mage::getBaseDir('media') . DS . 'storelocator' . DS . 'images' . DS . 'icon';
        return $path;
    }

    public function reSizeImage($id, $nameimage) {
        $_imageUrl = $this->getPathImageIcon($id) . DS . $nameimage;
        $imageResized = $this->getPathImageIcon($id) . DS . 'resize' . DS . $nameimage;
        if (!file_exists($imageResized) && file_exists($_imageUrl)) {
            $imageObj = new Varien_Image($_imageUrl);
             $imageObj->keepTransparency(true);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(FALSE);
            $imageObj->resize(40, 40);
           
            $imageObj->save($imageResized);
        }
    }

    public function saveTagToStore($tags, $storeId) {
        $this->deleteTagFormStore($storeId);

        foreach ($tags as $tag) {
            $modelTag = Mage::getModel('storelocator/tag');
            $modelTag->setData('value', $tag);
            $modelTag->setData('storelocator_id', $storeId);
            $modelTag->save();
        }
    }

    public function deleteTagFormStore($storeId) {
        if ($storeId) {
            $collectionTag = Mage::getModel('storelocator/tag')->getCollection()
                    ->addFieldToFilter('storelocator_id', $storeId);
            foreach ($collectionTag as $tag) {
                $tag->delete();
            }
        }
    }

    public function getTags($storeId) {
        if ($storeId) {
            $collectionTag = Mage::getModel('storelocator/tag')->getCollection()
                    ->addFieldToFilter('storelocator_id', $storeId);
            $tags = '';

            foreach ($collectionTag as $tag) {
                $tags .= $tag->getValue() . ',';
            }
            return substr($tags, 0, -1);
        }
        return '';
    }

    public function getImageNameByStore($storeId) {
        if ($storeId) {

            $collectionImages = Mage::getModel('storelocator/image')->getCollection()
                    ->addFieldToFilter('storelocator_id', $storeId);
            $image_names = "";
            foreach ($collectionImages as $image) {
                $image_names .= $image->getName() . ',';
            }

            return $image_names;
        }
        return '';
    }

    public function deleteImageFormStore($storeId) {
        if ($storeId) {
            $collectionImage = Mage::getModel('storelocator/image')->getCollection()
                    ->addFieldToFilter('storelocator_id', $storeId);
            foreach ($collectionImage as $image) {
                $image->delete();
            }
        }
    }

    public static function getStoreOptions() {
        $options = array();
        $collection = Mage::getModel('storelocator/storelocator')->getCollection()
                ->setOrder('name', 'ASC');
        foreach ($collection as $store) {
            $option = array();
            $option['label'] = $store->getName();
            $option['value'] = $store->getId();
            $options[] = $option;
        }

        return $options;
    }

    public function getSpecialDays($storeId) {
        $dayShow = Mage::getStoreConfig('storelocator/general/show_spencial_days', Mage::app()->getStore()->getStoreId());
        $dateStart = date('Y-m-d');
        $dateEnd = date('Y-m-d', strtotime(date('Y-m-d')) + $dayShow * 24 * 60 * 60);

        $collections = Mage::getModel('storelocator/specialday')
                ->getCollection()
                ->addFieldToFilter('specialday_date_to', array('gteq' => $dateStart))
                ->addFieldToFilter('date', array('lteq' => $dateEnd))
                ->addFieldToFilter('store_id', array('finset' => $storeId));

        $days = array();
        $key = 0;
        $timeDay = 60 * 60 * 24;
        
        foreach ($collections as $collection) {
            $days[$key]['name'] = $collection->getSpecialdayName();
            $days[$key]['time_open'] = $collection->getSpecialdayTimeOpen();
            $days[$key]['time_close'] = $collection->getSpecialdayTimeClose();
            $dateFrom = strtotime($collection->getDate());
            $dateTo = strtotime($collection->getSpecialdayDateTo());
            
            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }
            $key++;
        }
        return $days;
    }

    public function getHolidayDays($storeId) {
        $dayShow = Mage::getStoreConfig('storelocator/general/show_spencial_days', Mage::app()->getStore()->getStoreId());
        $dateStart = date('Y-m-d');
        $dateEnd = date('Y-m-d', strtotime(date('Y-m-d')) + $dayShow * 24 * 60 * 60);

        $collections = Mage::getModel('storelocator/holiday')
                ->getCollection()
                ->addFieldToFilter('holiday_date_to', array('gteq' => $dateStart))
                ->addFieldToFilter('date', array('lteq' => $dateEnd))
                ->addFieldToFilter('store_id', array('finset' => $storeId));

        $days = array();
        $key = 0;
        $timeDay = 60 * 60 * 24;
        
        foreach ($collections as $collection) {
            $days[$key]['name'] = $collection->getHolidayName();
            $dateFrom = strtotime($collection->getDate());
            $dateTo = strtotime($collection->getHolidayDateTo());
            
            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }
            $key++;
        }
        return $days;
    }

    public function getSpecialdayOption() {
        $specialdays = Mage::getModel('storelocator/storelocator')->getCollection();
        $result = array();

        foreach ($specialdays as $specialday) {
            $result[$specialday->getId()] = $specialday->getName();
        }

        return $result;
    }

    public function filterDates($array, $dateFields) {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    public function characterSpecial($character) {
        // $character =  iconv('UTF-8', 'ASCII//TRANSLIT', $character);        
        $input = array("ñ", " ", "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ"
            , "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
            , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
            , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
            , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ", "ê", "ù", "à", '.', '-', "'", "[À-Å]", "Æ", "Ç", "[È-Ë]", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/", "/×/", "/[Ù-Ü]/", "/[Ý-ß]/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/", "/÷/", "/[ù-ü]/", "/[ý-ÿ]/", "?");
        $output = array("n", "-", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
            , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
            , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
            , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
            , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D", "e", "u", "a", '-', '-', "", "A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y", "");

        return str_replace($input, $output, $character);
    }
    
    public function returntext() {
        return 'Featured storelocator box can be placed on different positions on your website by using one of the following options below:</br>
                Note: These options are recommended for developers. You shouldn\'t add them on the storelocator listing page either.';
    }
    
    public function returnblock() {
        return '&nbsp;&nbsp{{block type="storelocator/storelocator" template="storelocator/storesblock.phtml"}}<br>';
    }
    
    public function returntemplate() {
        return "&nbsp;\$this->getLayout()->createBlock('storelocator/storelocator')->setTemplate('storelocator/storesblock.phtml')<br/>&nbsp;&nbsp;->tohtml();";
    }
    
    public function returnlayout() {
        return '&nbsp;&lt;block name="featuredstorelocators" type="storelocator/storelocator" template="storelocator/storesblock.phtml"/&gt<br/>';
    }

}
