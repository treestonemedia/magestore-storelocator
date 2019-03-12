<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Storelocator_Model_System_Config_Mapstyle {

    public function toOptionArray() {
        $options = array(
            array('value' => '0', 'label' => Mage::helper('storelocator')->__('Default')),
            array('value' => '1', 'label' => Mage::helper('storelocator')->__('Shades of Grey')),
            array('value' => '2', 'label' => Mage::helper('storelocator')->__('Blue water')),
            array('value' => '3', 'label' => Mage::helper('storelocator')->__('Pale Dawn')),
            array('value' => '4', 'label' => Mage::helper('storelocator')->__('Blue Essence')),
            array('value' => '5', 'label' => Mage::helper('storelocator')->__('Apple Maps-esque')),
            array('value' => '6', 'label' => Mage::helper('storelocator')->__('Midnight Commander')),
            array('value' => '7', 'label' => Mage::helper('storelocator')->__('Light Monochrome')),
            array('value' => '8', 'label' => Mage::helper('storelocator')->__('Paper')),
            array('value' => '9', 'label' => Mage::helper('storelocator')->__('Retro')),
            array('value' => '10', 'label' => Mage::helper('storelocator')->__('Flat Map')),
            array('value' => '11', 'label' => Mage::helper('storelocator')->__('Greyscale')),
            array('value' => '12', 'label' => Mage::helper('storelocator')->__('becomeadinosaur')),
            array('value' => '13', 'label' => Mage::helper('storelocator')->__('Neutral Blue')),
            array('value' => '14', 'label' => Mage::helper('storelocator')->__('Gowalla')),
            array('value' => '15', 'label' => Mage::helper('storelocator')->__('Shift Worker')),
            array('value' => '16', 'label' => Mage::helper('storelocator')->__('light dream')),
            array('value' => '17', 'label' => Mage::helper('storelocator')->__('MapBox')),
            array('value' => '18', 'label' => Mage::helper('storelocator')->__('RouteXL')),
            array('value' => '19', 'label' => Mage::helper('storelocator')->__('Avocado World')),
            array('value' => '20', 'label' => Mage::helper('storelocator')->__('Lunar Landscape')),
            array('value' => '21', 'label' => Mage::helper('storelocator')->__('Bright & Bubbly')),
            array('value' => '22', 'label' => Mage::helper('storelocator')->__('Unsaturated Browns')),
            array('value' => '23', 'label' => Mage::helper('storelocator')->__('Bentley')),
            array('value' => '24', 'label' => Mage::helper('storelocator')->__('Blue Gray')),
            array('value' => '25', 'label' => Mage::helper('storelocator')->__('Icy Blue')),
            array('value' => '26', 'label' => Mage::helper('storelocator')->__('Clean Cut')),
            array('value' => '27', 'label' => Mage::helper('storelocator')->__('Nature')),
            array('value' => '28', 'label' => Mage::helper('storelocator')->__('Cobalt')),
            array('value' => '29', 'label' => Mage::helper('storelocator')->__('Subtle Grayscale')),
        );
        return $options;
    }

}