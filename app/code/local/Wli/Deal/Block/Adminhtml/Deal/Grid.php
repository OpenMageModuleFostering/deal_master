<?php
class Wli_Deal_Block_Adminhtml_Deal_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct()
{
    parent::__construct();
    $this->setId('dealGrid');
    // This is the primary key of the database
    $this->setDefaultSort('deal_id');
    $this->setDefaultDir('ASC');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
}

protected function _prepareCollection()
{
    $collection = Mage::getModel('deal/deal')->getCollection();
    $this->setCollection($collection);
    return parent::_prepareCollection();
}

protected function _prepareColumns()
{
    $this->addColumn('deal_id', array(
        'header'    => Mage::helper('deal')->__('ID'),
        'align'     =>'right',
        'width'     => '50px',
        'index'     => 'deal_id',
    ));

    $this->addColumn('title', array(
        'header'    => Mage::helper('deal')->__('Product Name'),
        'align'     =>'left',
        'index'     => 'title',
    ));

    $this->addColumn('sku', array(
        'header'    => Mage::helper('deal')->__('SKU'),
        'align'     =>'left',
        'index'     => 'sku',
    ));

    /*
    $this->addColumn('content', array(
        'header'    => Mage::helper('deal')->__('Item Content'),
        'width'     => '150px',
        'index'     => 'content',
    ));
    */

    $this->addColumn('start_date', array(
        'header'    => Mage::helper('deal')->__('Active From'),
        'align'     => 'left',
        'width'     => '120px',
        'type'      => 'date',
        'default'   => '--',
        'index'     => 'start_date',
    ));

    $this->addColumn('end_date', array(
        'header'    => Mage::helper('deal')->__('Active To'),
        'align'     => 'left',
        'width'     => '120px',
        'type'      => 'date',
        'default'   => '--',
        'index'     => 'end_date',
    ));   
    $this->addColumn('regular_price', array(
        'header'    => Mage::helper('deal')->__('Regular Price'),
        'align'     =>'left',
        'index'     => 'regular_price',
    ));
    $this->addColumn('deal_price', array(
        'header'    => Mage::helper('deal')->__('Deal Price'),
        'align'     =>'left',
        'index'     => 'deal_price',
    ));
	$this->addColumn('qty', array(
        'header'    => Mage::helper('deal')->__('Deal Qty'),
        'align'     =>'left',
        'index'     => 'qty',
    ));
	$this->addColumn('sold_qty', array(
        'header'    => Mage::helper('deal')->__('Sold Qty'),
        'align'     =>'left',
        'index'     => 'sold_qty',
    ));


    $this->addColumn('status', array(

        'header'    => Mage::helper('deal')->__('Status'),
        'align'     => 'left',
        'width'     => '80px',
        'index'     => 'status',
        'type'      => 'options',
        'options'   => array(
            1 => 'Active',
            0 => 'Inactive',
        ),
    ));

	$this->addColumn('', array(
        'header'    => Mage::helper('deal')->__('Delete'),
        'align'     =>'left',
	'renderer'  => 'Wli_Deal_Block_Adminhtml_Deal_Renderer_Deletedeal',

    ));

    return parent::_prepareColumns();
}

public function getRowUrl($row)
{
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
}

public function getGridUrl()
{
  return $this->getUrl('*/*/grid', array('_current'=>true));
}


}
