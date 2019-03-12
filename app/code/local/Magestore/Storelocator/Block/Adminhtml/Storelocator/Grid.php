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
 * Storelocator Grid Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_Block_Adminhtml_Storelocator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('storelocatorGrid');
		$this->setDefaultSort('storelocator_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Grid
	 */
	protected function _prepareCollection(){
                $storeId = $this->getRequest()->getParam('store');
		$collection = Mage::getModel('storelocator/storelocator')->getCollection()->setStoreId($storeId);;
		$this->setCollection($collection);
		return parent::_prepareCollection();
            //get collection from database view on grid;
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('storelocator_id', array(
			'header'	=> Mage::helper('storelocator')->__('ID'),
			'align'	 =>'right',
			'width'	 => '30px',
			'index'	 => 'storelocator_id',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('storelocator')->__('Store Name'),
			'align'	 =>'left',
			'index'	 => 'name',
                        'width'  => '150px'
		));

		$this->addColumn('address', array(
			'header'	=> Mage::helper('storelocator')->__('Address'),
			'width'	 => '250px',
			'index'	 => 'address',
		));
                
                $this->addColumn('city', array(
			'header'	=> Mage::helper('storelocator')->__('City'),
			'width'	 => '130px',
			'index'	 => 'city',
		));
                
                $this->addColumn('state', array(
                        'header'    => Mage::helper('storelocator')->__('State/Province'),
                        'width'     => '130px',
                        'index'     => 'state',
                ));
                
                $this->addColumn('country', array(
                        'header'    => Mage::helper('storelocator')->__('Country'),
                        'width'     => '130px',
                        'index'     => 'country',
                        'type'		=> 'options',
                        'options'   => Mage::helper('storelocator')->getListCountry(),
                ));
                $this->addColumn('zipcode', array(
                        'header'	=> Mage::helper('storelocator')->__('Zip Code'),
                        'width'	 => '130px',
                        'index'	 => 'zipcode',
                ));
                
                $this->addColumn('latitude', array(
                        'header'	=> Mage::helper('storelocator')->__('Latitude'),
                        'width'	 => '130px',
                        'index'	 => 'latitude',
                ));
                $this->addColumn('longtitude', array(
                        'header'	=> Mage::helper('storelocator')->__('Longitude'),
                        'width'	 => '130px',
                        'index'	 => 'longtitude',
                ));
                
		$this->addColumn('status', array(
			'header'	=> Mage::helper('storelocator')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
		));
                $storeId =$this->getRequest()->getParam('store',0);            
		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('storelocator')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('storelocator')->__('Edit'),
						'url'       => array('base'=> '*/*/edit/store/'.$storeId),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('storelocator')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('storelocator')->__('XML'));
		return parent::_prepareColumns();
	}
	
	/**
	 * prepare mass action for this grid
	 *
	 * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Grid
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('storelocator_id');
		$this->getMassactionBlock()->setFormFieldName('storelocator');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('storelocator')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('storelocator')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('storelocator/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('storelocator')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('storelocator')->__('Status'),
					'values'=> $statuses
				))
		));
                $this->getMassactionBlock()->addItem('renew', array(
			'label'		=> Mage::helper('storelocator')->__('Renew Location'),
			'url'		=> $this->getUrl('*/*/massReNewLocation'),
		));
		return $this;
	}
	
	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getRowUrl($row){
		 return $this->getUrl('*/*/edit', array('id' => $row->getId(),'store'=>$this->getRequest()->getParam('store')));
	}
}