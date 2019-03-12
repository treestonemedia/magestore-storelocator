<?php

class Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Timeschedule extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStoreData();
            Mage::getSingleton('adminhtml/session')->setStoreData(null);
        } elseif (Mage::registry('storelocator_data')) {
            $data = Mage::registry('storelocator_data')->getData();
        }

        $timeInterval = array();
        foreach (array(15, 30, 45) as $key => $var) {
            $timeInterval[$key]['value'] = $var;
            $timeInterval[$key]['label'] = Mage::helper('storelocator')->__($var . ' minutes');
        }

        $html_button = '<button style="float:right" onclick="saveApplyForOtherDays()" class="scalable save" type="button" title="Apply for other days" id="id_apply"><span>'.Mage::helper('storelocator')->__('Apply to other days').'</span></button><style>.entry-edit .entry-edit-head h4{width:100%;}</style>';
        foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $key => $day) {
            if ($key == 0)
                $fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('storelocator')->__(ucfirst($day) . $html_button)));
            else
                $fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('storelocator')->__(ucfirst($day))));
            $fieldset->addField($day . '_status', 'select', array(
                'label' => Mage::helper('storelocator')->__('Open'),
                'required' => false,
                'name' => $day . '_status',
                'class' => 'status_day',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('storelocator')->__('Yes'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('storelocator')->__('No'),
                    ),
                )
            ));


            $field = array('name' => $day . '_open',
                'data' => isset($data[$day . '_open']) ? $data[$day . '_open'] : '',
                'type' => 'open'
            );
            $fieldset->addField($day . '_open', 'note', array(
                'label' => Mage::helper('storelocator')->__('Open Time'),
                'name' => $day . '_open',
                'text' => $this->getLayout()->createBlock('storelocator/adminhtml_time')->setData('field', $field)->setTemplate('storelocator/time.phtml')->toHtml(),
            ));
            /*Edit by Son*/
                $field = array('name' => $day . '_open_break',
                'data' => isset($data[$day . '_open_break']) ? $data[$day . '_open_break'] : '',
                'type' => 'open_break'
            );
            $fieldset->addField($day . '_open_break', 'note', array(
                'label' => Mage::helper('storelocator')->__('Start of Break Time'),
                'name' => $day . '_open_break',
                'text' => $this->getLayout()->createBlock('storelocator/adminhtml_time')->setData('field', $field)->setTemplate('storelocator/time.phtml')->toHtml(),
            ));
            
             $field = array('name' => $day . '_close_break',
                'data' => isset($data[$day . '_close_break']) ? $data[$day . '_close_break'] : '',
                'type' => 'close_break'
            );
            $fieldset->addField($day . '_close_break', 'note', array(
                'label' => Mage::helper('storelocator')->__('End of Break Time'),
                'name' => $day . '_close_break',
                'text' => $this->getLayout()->createBlock('storelocator/adminhtml_time')->setData('field', $field)->setTemplate('storelocator/time.phtml')->toHtml(),
            ));
            /*End by Son*/

            $field = array('name' => $day . '_close',
                'data' => isset($data[$day . '_close']) ? $data[$day . '_close'] : '',
                'type' => 'close'
            );
            $fieldset->addField($day . '_close', 'note', array(
                'label' => Mage::helper('storelocator')->__('Close Time'),
                'name' => $day . '_close',
                'text' => $this->getLayout()->createBlock('storelocator/adminhtml_time')->setData('field', $field)->setTemplate('storelocator/time.phtml')->toHtml(),
            ));
         
        }

        if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStoreData());
            Mage::getSingleton('adminhtml/session')->setStoreData(null);
        } elseif (Mage::registry('storelocator_data')) {
            $form->setValues(Mage::registry('storelocator_data')->getData());
        }
        return parent::_prepareForm();
    }

}