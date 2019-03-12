<?php

class Magestore_Storelocator_Block_Adminhtml_Specialday_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('specialdayGrid');
        $this->setDefaultSort('storelocator_specialday_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('storelocator/specialday')->getCollection();
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        $condorderto = '';
        if($filter){
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            
            foreach($data as $value=>$key){               
                if($value == 'date'){
                    $condorder = $key;
                }
                if($value == 'specialday_date_to')
                {
                       $condorderto = $key;               
                }
            }
        }
        if($condorder){
            $condorder = Mage::helper('storelocator')->filterDates($condorder,array('from','to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if($from){
                $from = date('Y-m-d',strtotime($from));
                $collection->addFieldToFilter('date',array('gteq'=>$from));
            }
            if($to){
                $to = date('Y-m-d',strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('date',array('lteq'=>$to));
            }
        }
        if($condorderto){
            $condorderto = Mage::helper('storelocator')->filterDates($condorderto,array('from','to'));
            $fromto = $condorderto['from'];
            $toto = $condorderto['to'];
            if($fromto){
                $fromto = date('Y-m-d',strtotime($fromto));
                $collection->addFieldToFilter('specialday_date_to',array('gteq'=>$fromto));
            }
            if($toto){
                $toto = date('Y-m-d',strtotime($toto));
                $toto .= ' 23:59:59';
                $collection->addFieldToFilter('specialday_date_to',array('lteq'=>$toto));
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('storelocator_specialday_id', array(
            'header' => Mage::helper('storelocator')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'storelocator_specialday_id',
        ));
        
        $spencialdayoptions = Mage::helper('storelocator')->getSpecialdayOption();           
            
        $this->addColumn('store_id', array(
            'header' => Mage::helper('storelocator')->__('Store'),
            'align' => 'left',
            'width' => '300',
            'index' => 'store_id',
            'type'      =>  'options',
            'options'   =>  $spencialdayoptions,
            'renderer' => 'Magestore_Storelocator_Block_Adminhtml_Specialday_Renderer_Store',
            'filter_condition_callback' => array($this, 'filterCallback')
        ));

        $this->addColumn('date', array(
            'header' => Mage::helper('storelocator')->__('From Date'),
            'align' => 'left',
            'width' => '200',
            'type' => 'date',
            'format' => 'F',
            'index' => 'date',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('specialday_date_to', array(
            'header' => Mage::helper('storelocator')->__('To Date'),
            'align' => 'left',
            'width' => '200',
            'type' => 'date',
            'format' => 'F',
            'index' => 'specialday_date_to',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));
        
        $this->addColumn('specialday_time_open', array(
            'header' => Mage::helper('storelocator')->__('Open Time'),
            'align' => 'left',
            'index' => 'specialday_time_open',
        ));

        $this->addColumn('specialday_time_close', array(
            'header' => Mage::helper('storelocator')->__('Close Time'),
            'align' => 'left',
            'index' => 'specialday_time_close',
        ));


        $this->addColumn('comment', array(
            'header' => Mage::helper('storelocator')->__('Comment'),
            'width' => '250',
            'index' => 'comment',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('storelocator')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('storelocator')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('storelocator')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('storelocator')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('specialday_id');
        $this->getMassactionBlock()->setFormFieldName('specialday');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('storelocator')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('storelocator')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function filterCallback($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->addFieldToFilter('store_id', array('finset' => $value));
        }
        return $this;
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }

}
