<?php

class Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Generalinfo extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Generalinfo
     */
    protected function _prepareForm() {
        //prepare info form that this want to view

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $dataObj = new Varien_Object(array(
            'store_id' => '',
            'name_in_store' => '',
            'status_in_store' => '',
            'description_in_store' => '',
            'address_in_store' => '',
            'city_in_store' => '',
            'sort_in_store' => ''
        ));

        if (Mage::getSingleton('adminhtml/session')->getStorelocatorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStorelocatorData();
            Mage::getSingleton('adminhtml/session')->setStorelocatorData(null);
        } elseif (Mage::registry('storelocator_data'))
            $data = Mage::registry('storelocator_data')->getData();
        if (isset($data))
            $dataObj->addData($data);
        $data = $dataObj->getData();

        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('storelocator')->__('Use Default');
        $defaultTitle = Mage::helper('storelocator')->__('-- Please Select --');
        $scopeLabel = Mage::helper('storelocator')->__('STORE VIEW');

        $fullAddress = '';
        if ($this->getRequest()->getParam('id') && (!$this->getRequest()->getParam('store') || $this->getRequest()->getParam('store') == '0')) {
            $fullAddress = $data['address'] . $data['city'] . $data['state'] . $data['zipcode'] . $data['country'];
            $fullAddress = str_replace(" ", "", $fullAddress);
        }

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $wysiwygConfig->addData(array(
            'add_variables' => false,
            'plugins' => array(),
            'widget_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
            'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
            'directives_url_quoted' => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
        ));

        //General Info
        $fieldset = $form->addFieldset('storelocator_general_info', array('legend' => Mage::helper('storelocator')->__('General Info')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('storelocator')->__('Store Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name1',
            'disabled' => ($inStore && !$data['name_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="name_default" name="name_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['name_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="name_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('storelocator')->__('Description'),
            'title' => Mage::helper('storelocator')->__('Description'),
            'style' => 'width:500px; height:150px;',
            'wysiwyg' => true,
            'required' => false,
            'config' => $wysiwygConfig,
            'disabled' => ($inStore && !$data['description_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="description_default" name="description_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['description_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="description_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('storelocator')->__('Status'),
            'required' => false,
            'name' => 'status1',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('storelocator')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('storelocator')->__('Disabled'),
                ),
            ),
            'disabled' => ($inStore && !$data['status_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['status_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="status_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('link', 'text', array(
            'label' => Mage::helper('storelocator')->__('Store’s Link'),
            'name' => 'link',
            'note' => 'E.g. The official link or Facebook link of store',
            'required' => false,
            'after_element_html' => '<input type="hidden" id="full-address" name="full_address" value="' . $fullAddress . '">',
        ));

        if ($this->getRequest()->getParam('id')) {
            $data['tags_store'] = Mage::helper('storelocator')->getTags($this->getRequest()->getParam('id'));
        }
        $fieldset->addField('tags_store', 'text', array(
            'label' => Mage::helper('storelocator')->__('Store’s Tag(s)'),
            'name' => 'tags_store',
            'note' => 'Used to filter stores by tag in frontend.<br />
                       E.g. shoes, dresses, bags',
        ));

        $fieldset->addField('sort', 'text', array(
            'label' => Mage::helper('storelocator')->__('Sort Order'),
            'name' => 'sort',
            'required' => false,
            'disabled' => ($inStore && !$data['sort_in_store']),
            'after_element_html' => $inStore ? '<p class="note" id="note_sort"><span>Order of store in list.</span></p></td><td class="use-default">
                <input id="sort_default" name="sort_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['sort_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                    <label for="sort_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                        [' . $scopeLabel . ']' : '<p class="note" id="note_sort"><span>Sort the display order of store in the store list. Store with bigger sort order will be shown first</span></p></td><td class="scope-label">[' . $scopeLabel . ']',
        ));

        $fieldset->addField('image_id', 'note', array(
            'label' => Mage::helper('storelocator')->__('Store Image(s)'),
            'name' => 'images_id',
            'text' => $this->getLayout()->createBlock('storelocator/adminhtml_gallery_content')->setTemplate('storelocator/helper/gallery.phtml')->toHtml(),
            'after_element_html' => '<style>.columns .form-list td.value{width:300px !important}</style>'
        ));


        //Location Info
        $fieldset = $form->addFieldset('storelocator_location_info', array('legend' => Mage::helper('storelocator')->__('Location Info')));

        $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('storelocator')->__('Address'),
            'name' => 'address',
            'required' => true,
            'class' => 'required-entry',
            'disabled' => ($inStore && !$data['address_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="address_default" name="address_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['address_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="address_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('storelocator')->__('City'),
            'name' => 'city',
            'required' => true,
            'class' => 'required-entry',
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="city_default" name="city_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['city_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="city_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('zipcode', 'text', array(
            'label' => Mage::helper('storelocator')->__('Zip Code'),
            'name' => 'zipcode',
        ));

        $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('storelocator')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country',
            'values' => Mage::helper('storelocator')->getOptionCountry(),
        ));

        $fieldset->addField('stateEl', 'note', array(
            'label' => Mage::helper('storelocator')->__('State/Province'),
            'name' => 'stateEl',
            'text' => $this->getLayout()->createBlock('storelocator/adminhtml_region')->setTemplate('storelocator/region.phtml')->toHtml(),
        ));


        //Contact Info
        $fieldset = $form->addFieldset('storelocator_contact_info', array('legend' => Mage::helper('storelocator')->__('Contact Info')));
        $fieldset->addField('phone', 'text', array(
            'label' => Mage::helper('storelocator')->__('Phone Number'),
            'name' => 'phone',
            'required' => false,
        ));
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('storelocator')->__('Email Address'),
            'name' => 'email',
            'required' => false,
        ));

        $fieldset->addField('fax', 'text', array(
            'label' => Mage::helper('storelocator')->__('Fax Number'),
            'name' => 'fax',
            'required' => false,
        ));

        //Meta Info
        $fieldset = $form->addFieldset('storelocator_meta_info', array('legend' => Mage::helper('storelocator')->__('Meta Info')));

        $fieldset->addField('rewrite_request_path', 'text', array(
            'label' => Mage::helper('storelocator')->__('URL Key'),
            'name' => 'rewrite_request_path',
        ));

        $fieldset->addField('meta_title', 'text', array(
            'label' => Mage::helper('storelocator')->__('Meta Title'),
            'name' => 'meta_title',
            'required' => false
        ));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'label' => Mage::helper('storelocator')->__('Meta Keywords'),
            'name' => 'meta_keywords',
            'style' => 'width:595px; height:100px;',
            'required' => false,
        ));

        $fieldset->addField('meta_contents', 'textarea', array(
            'label' => Mage::helper('storelocator')->__('Meta Description'),
            'name' => 'meta_contents',
            'style' => 'width:595px; height:100px;',
            'required' => false,
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }

}
