<?php
class Wli_Deal_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getProduct($id = NULL)
	{
		
	//	echo "<pre>";
		$deal_collection = Mage::getModel('deal/deal')->getCollection();
		$dealsdata = ($deal_collection->getdata());
		
		$product_id_array = array();
		foreach($dealsdata as $deal)
		{
			$product_id_array[$deal['product_id']] = $deal['product_id'];
		}
		
		
		if(isset($id) && $id != NULL)
		{
			$_edited_deal = Mage::getModel('deal/deal')->load($id);
			$_edited_deal = ($_edited_deal->getdata());
			unset($product_id_array[$_edited_deal['product_id']]);
		}
		
		
		
		$collection = Mage::getModel('catalog/product')->getCollection();
		
		if(!empty($product_id_array)){
		$collection->addAttributeToFilter('entity_id',array('nin'=>$product_id_array));
		}
		
		$productArray[]=(array('value'=>'' , 'label' => '-Select Product-'));
		
		foreach($collection as $productData)
		{
			$product = Mage::getModel('catalog/product')->load($productData['entity_id']);
			$productArray[]=(array('value'=>$product['entity_id'] , 'label' => $product['name']));
		}
		
		//print_r($productArray);
		return $productArray;
	}
}