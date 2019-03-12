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
 * Storelocator Block
 *
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Block_Storelocator extends Mage_Core_Block_Template {

	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Storelocator_Block_Storelocator
	 */
	public function _prepareLayout() {
		return parent::_prepareLayout();
	}

	public function getGoogleApiKey() {
		//return google Api Key
		return Mage::helper('storelocator')->getConfig('gkey');
	}

	public function getFacebookApiKey() {
		//return Facebook Api key
		return Mage::helper('storelocator')->getConfig('facekey');
	}

	public function cutLink($link) {
		if (strlen($link) > 12) {
			$link = substr($link, 0, 12) . "...";
		}
		return $link;
	}

	public function getListStore1() {
		$storeId = Mage::app()->getStore()->getStoreId();
		$collections = Mage::getModel('storelocator/storelocator')->getCollection()
			->setStoreId($storeId)
			->addFieldToSelect(array('name', 'rewrite_request_path', 'phone', 'country', 'state', 'state_id', 'zipcode', 'latitude', 'longtitude', 'image_icon','product_ids'))
			->addFieldToFilter('status', 1);
                if(Mage::app()->getRequest()->getParam('prouduct_id'))
                        $collections->addFieldToFilter('product_ids',array('finset'=>Mage::app()->getRequest()->getParam('prouduct_id')));
		if (Mage::helper('storelocator')->getConfig('sort_store')) {
			$collections->setOrder('name', 'ASC');
		} else {
			$collections->setOrder('sort', 'DESC');
		}
		return $collections;
	}
        public function countStoreHasProduct($productId) {
            return Mage::getModel('storelocator/storelocator')->getCollection()
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->addFieldToFilter('product_ids',array('finset'=>Mage::app()->getStore()->getStoreId()))
                    ->addFieldToFilter('status', 1)
                    ->getSize();
        }
	public function getListStoreJson() {
		$storeId = Mage::app()->getStore()->getStoreId();
		$collections = Mage::getModel('storelocator/storelocator')->getCollection()
			->setStoreId($storeId)
			->addFieldToSelect(array('name','phone','rewrite_request_path', 'country','address', 'state', 'latitude', 'longtitude', 'image_icon'))
			->addFieldToFilter('status', 1);
		if (Mage::helper('storelocator')->getConfig('sort_store')) {
			$collections->setOrder('name', 'ASC');
		} else {
			$collections->setOrder('sort', 'DESC');
		}

		return Mage::helper('core')->jsonEncode($collections->getData());
	}
	public function getArrayImage() {
		$collections = Mage::getModel('storelocator/image')->getCollection()
			->addFieldToSelect(array('name', 'storeLocator_id'))
			->addFieldToFilter('statuses', 1);
		$array = array();
		foreach ($collections as $img) {
			$array[$img->getData('storeLocator_id')] = $img['name'];
		}
		return $array;
	}
	public function getArrayImageJson() {
		$collections = Mage::getModel('storelocator/image')->getCollection()
			->addFieldToSelect(array('name', 'storeLocator_id'))
			->addFieldToFilter('statuses', 1);
		$array = array();
		foreach ($collections as $img) {
			$array[$img->getData('storeLocator_id')] = $img['name'];
		}
		return Mage::helper('core')->jsonEncode($array);
	}

	public function getListStore() {
		if ($this->hasData('stores')) {
			return $this->getData('stores');
		}

		$state = $this->getRequest()->getParam('state');
		$city = $this->getRequest()->getParam('city');
		$zipcode = $this->getRequest()->getParam('zipcode');
		$country_id = $this->getRequest()->getParam('country');
		$storeId = Mage::app()->getStore()->getStoreId();
		$collections = Mage::getModel('storelocator/storelocator')->getCollection()
			->setStoreId($storeId)
			->addFieldToFilter('status', 1);
		//Filter City
		if (isset($city) && ($city != null)) {
			$city = trim($city);
			$collections->addFieldToFilter('city', array('like' => '%' . $city . '%'));
		}
		//Filter Country
		if ($country_id != "nothing") {
			$country_id = trim($country_id);
			$collections->addFieldToFilter('country', array('like' => '%' . $country_id . '%'));
		}
		if (isset($state) && ($state != null)) {
			$state = trim($state);
			$collections->addFieldToFilter('state', array('like' => '%' . $state . '%'));
		}
		if (isset($zipcode) && ($zipcode != null)) {
			$zipcode = trim($zipcode);
			$collections->addFieldToFilter('zipcode', array('like' => '%' . $zipcode . '%'));
		}
		if ($this->getSortDefaultContry() == 'alphabeta') {
			$collections->setOrder('name', 'ASC');
		} else {
			$collections->setOrder('sort', 'DESC');
		}
		$this->setData('stores', $collections);
		return $collections;
	}

	public function getWorkingTime($store, $format) {
		$week = array(
			'Sun' => 'sunday',
			'Mon' => 'monday',
			'Tue' => 'tuesday',
			'Wed' => 'wednesday',
			'Thur' => 'thursday',
			'Fri' => 'friday',
			'Sat' => 'saturday',
		);

		$html = '';
		foreach ($week as $label => $day) {
			$status = $store->getData($day . '_status');
			$open = $store->getData($day . '_open');
			$open_break = $store->getData($day . '_open_break');
			$close_break = $store->getData($day . '_close_break');
			$close = $store->getData($day . '_close');

			$openFormated = date($format, strtotime($open));
			$openBreakFormated = date($format, strtotime($open_break));
			$closeBreakFormated = date($format, strtotime($close_break));
			$closeFormated = date($format, strtotime($close));

			$html .= '<tr>';
			$html .= "<td style='text-align: center; width: 20%;'>" . $this->__("$label:") . '</td>';

			if ($status == 2) {
				$html .= '<td>' . $this->__('Closed') . '</td>';
			} else {
				if (($open != $close) && ($open != $open_break) && ($close != $close_break) && ($open_break != $close_break)) {
					$html .= "<td style='width: 80%; text-align: center;'>";
					$html .= $openFormated . ' - ' . $openBreakFormated . ' && ' . $closeBreakFormated . ' - ' . $closeFormated . '</td>';
				} else if (($open == $open_break) && ($close_break != $close)) {
					$html .= '<td>' . $closeBreakFormated . ' - ' . $closeFormated . '</td>';
				} else if (($open != $open_break) && ($close_break == $close)) {
					$html .= '<td>' . $openFormated . ' - ' . $openBreakFormated . '</td>';
				} else if (($open != $close) && ($open_break == $close_break)) {
					$html .= '<td>' . $openFormated . ' - ' . $closeFormated . '</td>';
				} else {
					$html .= '<td>' . $this->__('Closed') . '</td>';
				}
			}
			$html .= '</tr>';
		}
		return $html;
	}

	function getTagList() {
		$storeCollection = Mage::getBlockSingleton('storelocator/storelocator')->getListStore();
		if (!$storeCollection->getSize()) {
			return null;
		}
		$storeIds = $storeCollection->getAllIds();
		$tagCollection = Mage::getModel('storelocator/tag')->getCollection()
			->addFieldToFilter('storelocator_id', $storeIds);

		$tagCollection->getSelect()->group('value');
		$list = array();
		foreach ($tagCollection as $tag) {
			$list[] = array(
				'value' => $tag->getValue(),
				'ids' => trim($this->getIdsToTag($tag->getValue()), ','),
			);
		}

		return $list;
	}

	public function getIdsToTag($value) {
		$storeCollection = Mage::getBlockSingleton('storelocator/storelocator')->getListStore();
		$storeIds = $storeCollection->getAllIds();
		$tagCollection = Mage::getModel('storelocator/tag')->getCollection()
			->addFieldToFilter('storelocator_id', $storeIds)
			->addFieldToFilter('value', $value);
		$ids = '';
		foreach ($tagCollection as $tag) {
			$ids .= $tag->getStorelocatorId() . ',';
		}
		return $ids;
	}

	public function getStoreById() {
		$id = $this->getRequest()->getParam('id');
		$storeId = Mage::app()->getStore()->getStoreId();
		$collection = Mage::getModel('storelocator/storelocator')->setStoreId($storeId)->load($id);

		if (!$collection) {
			$this->_redirectUrl('storelocator/index');
		}

		return $collection;
	}

	public function isFbCommentEnable() {
		return Mage::helper('storelocator')->getConfig('allow_face');
	}

	public function addTopLinkStores() {
            if (Mage::helper('storelocator')->getConfig('enable') == 1&&Mage::helper('storelocator')->getConfig('toplink')==1) {
                $toplinkBlock = $this->getParentBlock();
                if ($toplinkBlock) {
                    $toplinkBlock->addLink($this->__('Store Locator'), 'storelocator', 'Store Locator', true, array(), 1);
                }
            }
	}

	public function getListCountry1() {
		$optionCountry = array();

		$storelocatorCollection = Mage::getModel('storelocator/storelocator')
			->getCollection()
			->addFieldToSelect('country');

		$storelocatorCollection->getSelect()->distinct(true);

		$collection = Mage::getResourceModel('directory/country_collection')
			->loadByStore()
			->addFieldToFilter('country_id', array('in' => $storelocatorCollection->getData()));

		if (count($collection)) {
			foreach ($collection as $item) {
				$optionCountry[$item->getId()] = $item->getName();
			}
		}
		return $optionCountry;
	}
	public function getListCountry() {
		return Mage::helper('storelocator')->getOptionCountry();
	}

	public function getImagebyStore($id_storelocator) {

		$collection = Mage::getModel('storelocator/image')->getCollection()
			->addFieldToFilter('storelocator_id', $id_storelocator)
			->addFieldToFilter('image_delete', 2)
			->setOrder('options', 'ASC');
		$url = array();
		foreach ($collection as $item) {
			if ($item->getData('name')) {
				$url[] = Mage::getBaseUrl('media') . 'storelocator/images/' . $item->getData('name');
			}
		}
		return $url;
	}

	public function getSearchConfig() {
		$choose_search = Mage::helper('storelocator')->getConfig('choose_search');
		$search_config = explode(',', $choose_search);
		return $search_config;
	}

	public function getImageIconByStore($id, $name) {
		return Mage::getBaseUrl('media') . 'storelocator/images/icon/resize/' . $name;
	}

	public function getLanguage() {
		return Mage::helper('storelocator')->getConfig('language');
	}

	public function chekRadiusDefault() {
		return Mage::helper('storelocator')->getConfig('search_radius_default');
	}

	public function getRadius() {
		$radius = Mage::helper('storelocator')->getConfig('search_radius');
		$radius = explode(',', $radius);
		return $radius;
	}

	public function getUnitRadius() {
		return Mage::helper('storelocator')->getConfig('distance_unit');
	}

	public function getCoordinatesCurrent() {
		$address = array();
		$country_id = $this->getRequest()->getParam('country');
		$state = $this->getRequest()->getParam('state');
		$city = $this->getRequest()->getParam('city');
		$zipcode = $this->getRequest()->getParam('zipcode');
		$street = $this->getRequest()->getParam('address');
		if ($street) {
			$address['street'] = $street;
			$address['city'] = $city;
			$address['region'] = $state;
			$address['zipcode'] = $zipcode;
			if ($country_id != 'nothing') {
				$address['country'] = $country_id;
			}
			$coordinates = Mage::getModel('storelocator/gmap')
				->getCoordinates($address);
			if ($coordinates) {
				return $coordinates;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public function getLevelRadius() {
		$radius = (int) $this->getRequest()->getParam('radius');
		return $radius;
	}

	public function getLeveRadiusConvert() {
		$radius = (int) $this->getRequest()->getParam('radius');
		return $radius * $this->getUnitDistance();
	}

	public function getPageTitle() {
		return Mage::helper('storelocator')->getConfig('page_title');
	}

	public function getUnitDistance() {
		$unit = Mage::helper('storelocator')->getConfig('distance_unit');

		return ($unit == 'mi') ? 1609.3 : 1000;
	}

	public function getDefaultCountry() {
		return Mage::helper('storelocator')->getConfig('default_country');
	}

	public function getSortDefaultContry() {
		return Mage::helper('storelocator')->getConfig('sort_store');
	}

	public function getLatAndLng($country = null) {
		$geocode_stats = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=$country&sensor=false");

		$output_deals = json_decode($geocode_stats);

		$latLng = $output_deals->results[0]->geometry->location;

		if ($latLng->lat && $latLng->lng) {
			return array($latLng->lat, $latLng->lng);
		}
		return null;
	}

}
