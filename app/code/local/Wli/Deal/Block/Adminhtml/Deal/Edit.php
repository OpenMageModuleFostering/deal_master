<?php
class Wli_Deal_Block_Adminhtml_Deal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
	    parent::__construct();
		   
	    $this->_objectId = 'id';
	    $this->_blockGroup = 'deal';
	    $this->_controller = 'adminhtml_deal';

	    $this->_updateButton('save', 'label', Mage::helper('deal')->__('Save Item'));
	    $this->_updateButton('delete', 'label', Mage::helper('deal')->__('Delete Item'));
	}

	public function getHeaderText()
	{
	    if( Mage::registry('deal_data') && Mage::registry('deal_data')->getId() ) {
		return Mage::helper('deal')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('deal_data')->getTitle()));
	    } else {
		return Mage::helper('deal')->__('Add Item');
	    }
	}
}
