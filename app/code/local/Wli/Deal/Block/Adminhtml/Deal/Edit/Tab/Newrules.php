<?php
class Wli_Deal_Block_Adminhtml_Deal_Edit_Tab_Newrules extends Mage_Adminhtml_Block_Widget_Form
{
protected function _prepareForm()
{
    $form = new Varien_Data_Form();
    $this->setForm($form);

    $fieldset = $form->addFieldset('deal_form', array('legend'=>Mage::helper('deal')->__('New Rules')));
   
    $fieldset->addField('product_id', 'select', array(
        'label'     => Mage::helper('deal')->__('Select Product(s)'),
        'class'     => 'required-entry',
        'required'  => true,
    	'onchange'  => 'getproductdetail()',
        'after_element_html' => '<div id="update"><ul id=productdetail></ul></div> ',
        'name'      => 'product_id',
		'values' =>$productsName,
		'tabindex' => 1,
    ));
    
    $fieldset->addField('discount_type', 'select', array(
    		'label'     => Mage::helper('deal')->__('Type of Discount'),
    		'name'      => 'discount_type',
    		'onchange'  => 'showdiscountelement(this.value)',
    		'tabindex' => 2,
    		'values'    => array(
    				array(
    						'value'     => 1,
    						'label'     => Mage::helper('deal')->__('Fixed Amount'),
    				),
    
    				array(
    						'value'     => 0,
    						'label'     => Mage::helper('deal')->__('In Percentage'),
    				),
    		),
    ));
    
    if(isset($current_deal_discount_type)){
    	 
    	if($current_deal_discount_type == 0) {

    		$fieldset->addField('discount', 'text', array(
    				'label'     => Mage::helper('deal')->__('% Discount'),
    				'onkeyup'  => 'getcalculatedprice(this.value)',
    				'name'      => 'discount',

    		));

    		$fieldset->addField('discount_amount', 'text', array(
    				'label'     => Mage::helper('deal')->__('Discount Amount'),
    					
    				'style'     => 'display:none',
    				'onkeyup'  => 'getcalculatedprice(this.value)',
    				'name'      => 'discount_amount',
    		));


    	}else
    	{


    		$fieldset->addField('discount', 'text', array(
    				'label'     => Mage::helper('deal')->__('% Discount'),
    				'onkeyup'  => 'getcalculatedprice(this.value)',
    				'style'     => 'display:none',
    				'name'      => 'discount',

    		));

    		$fieldset->addField('discount_amount', 'text', array(
    				'label'     => Mage::helper('deal')->__('Discount Amount'),
    					

    				'onkeyup'  => 'getcalculatedprice(this.value)',
    				'name'      => 'discount_amount',
    		));


    	}

    	 
    	 
    }else
    {
    	$fieldset->addField('discount', 'text', array(
    			'label'     => Mage::helper('deal')->__('% Discount'),
    			'onkeyup'  => 'getcalculatedprice(this.value)',
    			'name'      => 'discount',

    	));

    	 
    	$fieldset->addField('discount_amount', 'text', array(
    			'label'     => Mage::helper('deal')->__('Discount Amount'),
    			 
    			'style'     => 'display:none',
    			'onkeyup'  => 'getcalculatedprice(this.value)',
    			'name'      => 'discount_amount',
    	));
    	 
    }
   
    $fieldset->addField('deal_price', 'text', array(
    		'label'     => Mage::helper('deal')->__('Deal Price'),
    		'required'  => true,
    		'name'      => 'deal_price',
    		'readonly'      => 'readonly',
    		'tabindex' => 3,
    
    ));
    
    
    $fieldset->addField('qty', 'text', array(
    		'label'     => Mage::helper('deal')->__('Deal Qty'),
    		'required'  => true,
    		'name'      => 'qty',
    		'tabindex' => 4,
    
    ));
    
    
    $fieldset->addField('start_date', 'date', array(
    		'name' => 'start_date',
    		'label'     => Mage::helper('core')->__('Active From'),
    		'time' => true, // whether time should also be inserted with date
    		'class'     => 'required-entry', // if its mandatory
    		'tabindex' => 1,
    		'image' => $this->getSkinUrl('images/grid-cal.gif'), // will show a small calendar image besides the input field
    		//'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL) // there are many predefined format for date to be inserted
    		'format' => 'yyyy-MM-dd H:mm:ss' // we can also pass custom format as per requirement
    
    ));
    
    $fieldset->addField('end_date', 'date', array(
    		'name' => 'end_date',
    		'label'     => Mage::helper('core')->__('Active From'),
    		'time' => true, // whether time should also be inserted with date
    		'class'     => 'required-entry', // if its mandatory
    		'tabindex' => 1,
    		'image' => $this->getSkinUrl('images/grid-cal.gif'), // will show a small calendar image besides the input field
    		//'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL) // there are many predefined format for date to be inserted
    		'format' => 'yyyy-MM-dd H:mm:ss' // we can also pass custom format as per requirement
    
    ));
    
    $fieldset->addField('featured', 'select', array(
    		'label'     => Mage::helper('deal')->__('Featured Deal'),
    		'name'      => 'featured',
    		'values'    => array(
    				array(
    						'value'     => 1,
    						'label'     => Mage::helper('deal')->__('Yes'),
    				),
    
    				array(
    						'value'     => 0,
    						'label'     => Mage::helper('deal')->__('No'),
    				),
    		),
    ));
    
    $fieldset->addField('status', 'select', array(
        'label'     => Mage::helper('deal')->__('Status'),
        'name'      => 'status',
        'values'    => array(
            array(
                'value'     => 1,
                'label'     => Mage::helper('deal')->__('Active'),
            ),

            array(
                'value'     => 0,
                'label'     => Mage::helper('deal')->__('Inactive'),
            ),
        ),
    ));
   
    $fieldset->addField('content', 'editor', array(
        'name'      => 'content',
        'label'     => Mage::helper('deal')->__('Deal Description'),
        'title'     => Mage::helper('deal')->__('Deal Description'),
        'style'     => 'width:98%; height:400px;',
        'wysiwyg'   => false,
        'required'  => true,
    ));
   
    if ( Mage::getSingleton('adminhtml/session')->getDealData() )
    {
        $form->setValues(Mage::getSingleton('adminhtml/session')->getDealData());
        Mage::getSingleton('adminhtml/session')->setDealData(null);
    } elseif ( Mage::registry('deal_data') ) {
        $form->setValues(Mage::registry('deal_data')->getData());
    }
    return parent::_prepareForm();
}
}	
