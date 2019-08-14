<?php
/**
 * @category   Sugarcode
 * @package    Sugarcode_Customdiscount
 * @author     pradeep.kumarrcs67@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Wli_Deal_Model_Observer
{

	public $totaldiscount ;
	public function setDiscount($observer)
	{

		$quote=$observer->getEvent()->getQuote();
		$quoteid=$quote->getId();
		 
		foreach ($quote->getAllItems() as $item) {
			$productId = $item->getProduct()->getId();
			$quation_id = $item->getId();
   			$quation_productId = $item->getProduct()->getId();
   			$quation_productname = $item->getProduct()->getName();
   			$quation_qty =$item->getQty();
			
			$collection = Mage::getModel('deal/deal')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('product_id',$productId,'AND');
			$dealdata = ($collection->getData());
			
			
			$startdate = strtotime($dealdata[0]['start_date']);
			$enddate = strtotime($dealdata[0]['end_date']);
			$todaydate = strtotime(date('Y-m-d H:i:s'));
			if($dealdata[0]['status'] == 1){ 
				if($dealdata[0]['qty']<$quation_qty)
				{
					$dealqty = ($quation_qty - $dealdata[0]['qty']);
					$item->addMessage(
							'Only '.$dealdata[0]['qty'].' is appicable for deal price, remaining qty will have original price'
					);
					if($dealdata[0]['discount_type']== 0)
					{
						$this->totaldiscount = ($this->totaldiscount+(($dealdata[0]['regular_price']*$dealdata[0]['discount'])/100)*$dealdata[0]['qty']);
					}else{
						$this->totaldiscount = ($this->totaldiscount+$dealdata[0]['discount_amount'])*$dealdata[0]['qty'];
					}
					
				}else if($todaydate<$startdate || $todaydate>$enddate)
				{
					
					$item->addMessage(
							'The requested deal for "'.$quation_productname.'" is expired discount is not applicable for this.'
					);
					$deal_id = $dealdata[0]['deal_id'];
					$resource = Mage::getSingleton('core/resource');
					$writeConnection = $resource->getConnection('core_write');
					$table = $resource->getTableName('deal/deal');
					$query = "UPDATE {$table} SET status = 0 WHERE deal_id = ".$deal_id;
					$writeConnection->query($query);
					
					
				}else
				{
					
// 					$item->addMessage(
// 							'if deal time is expire,Then discount will not appy*, Deal will expire'.$dealdata[0]['end_date']
// 					);
					if($dealdata[0]['discount_type']== 0)
					{
						$this->totaldiscount = ($this->totaldiscount+(($dealdata[0]['regular_price']*$dealdata[0]['discount'])/100)*$quation_qty);
					}else{
						$this->totaldiscount = ($this->totaldiscount+$dealdata[0]['discount_amount'])*$quation_qty;
					}
				}
			}
			
			

			
		}
		$discountAmount=$this->totaldiscount;
		if($quoteid) {
			 
			 

			if($discountAmount>0) {
				$total=$quote->getBaseSubtotal();
				$quote->setSubtotal(0);
				$quote->setBaseSubtotal(0);

				$quote->setSubtotalWithDiscount(0);
				$quote->setBaseSubtotalWithDiscount(0);

				$quote->setGrandTotal(0);
				$quote->setBaseGrandTotal(0);


				$canAddItems = $quote->isVirtual()? ('billing') : ('shipping');
				foreach ($quote->getAllAddresses() as $address) {

					$address->setSubtotal(0);
					$address->setBaseSubtotal(0);

					$address->setGrandTotal(0);
					$address->setBaseGrandTotal(0);

					$address->collectTotals();

					$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
					$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

					$quote->setSubtotalWithDiscount(
							(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
					);
					$quote->setBaseSubtotalWithDiscount(
							(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
					);

					$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
					$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

					$quote ->save();

					$quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
					->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
					->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
					->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
					->save();


					if($address->getAddressType()==$canAddItems) {
						//echo $address->setDiscountAmount; exit;
						$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
						$address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
						$address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
						$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
						if($address->getDiscountDescription()){
							$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
							$address->setDiscountDescription($address->getDiscountDescription().', Custom Discount');
							$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
						}else {
							$address->setDiscountAmount(-($discountAmount));
							$address->setDiscountDescription('Deals Discount');
							$address->setBaseDiscountAmount(-($discountAmount));
						}
						$address->save();
					}//end: if
				} //end: foreach
				//echo $quote->getGrandTotal();

				foreach($quote->getAllItems() as $item){
					//We apply discount amount based on the ratio between the GrandTotal and the RowTotal
					$rat=$item->getPriceInclTax()/$total;
					$ratdisc=$discountAmount*$rat;
					$item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
					$item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();

				}


			}

		}
	}
// 	public function salesQuoteItemQtySetAfter($observer)
// 	{
		
// 		$cartdata = Mage::getSingleton('checkout/session');
		
// 		foreach ($cartdata->getQuote()->getAllItems() as $item) {
// 			$quation_id = $item->getId();
//   			$quation_productId = $item->getProduct()->getId();
//   			$quation_productname = $item->getProduct()->getName();
//   			$quation_qty =$item->getQty();
//   			$collection = Mage::getModel('deal/deal')->getCollection()
//   			   			->addFieldToFilter('status',1)
//   			   			->addFieldToFilter('product_id',$quation_productId,'AND');
//   			  				$dealdata = ($collection->getData());
// //   			  				echo "<pre>";
// //   			  				print_r(get_class_methods($item));
// //   			  				die;
  			  				
  			  				
//   			  				$startdate = strtotime($dealdata[0]['start_date']);
//   			  				$enddate = strtotime($dealdata[0]['end_date']);
//   			  				$todaydate = strtotime(date('Y-m-d H:i:s'));
  			  				
//   			  				if($dealdata[0]['qty']<$quation_qty)
// 							{
// 								$item->addMessage(
// 										'The requested quantity for "'.$quation_productname.'" is not available for deal.'
// 								);
//   							}else if($todaydate>$startdate || $todaydate<$enddate)
//   							{
//   								$item->addMessage(
//   										'The requested deal for "'.$quation_productname.'" is expired discount is not applicable for this.'
//   								);
//   							}
// 		}
// 	}
	

}
