<?php
class Wli_Deal_Adminhtml_DealController extends Mage_Adminhtml_Controller_Action
{

protected function _initAction()
{
    $this->loadLayout()
        ->_setActiveMenu('deal/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
    return $this;
}   

public function indexAction() {
    $this->_initAction();       
    $this->_addContent($this->getLayout()->createBlock('deal/adminhtml_deal'));
    $this->renderLayout();
}
public function getProductAction()
{
	
	 $_products = Mage::getModel('catalog/product')->getCollection()
	 ->addAttributeToFilter('name', array('like' => '%'.$this->getRequest()->getParam('keyword').'%'))
	 ->load();
	 $productdata = $_products->getdata();
	 $returnstring .= "[";
 	 foreach($productdata as $products)
 	 {
 	 	$returnstring .= '{';
 	 	$returnstring .= '"caption":"'.addslashes($products['name']).'"';
 	 	$returnstring .= ',';
 	 	$returnstring .= '"value":"'.addslashes($products['entity_id']).'"';
 	 	$returnstring .= ',';
 	 	$returnstring .= '"sku":"'.addslashes($products['sku']).'"';
 	 	$returnstring .= '}';
 	 	$returnstring .= ',';
 	  	
 	 }
 	 $returnstring = substr_replace($returnstring, '', -1);
 	 echo $returnstring .= "]";
	}
public function getProductDetailAction()
	{
	
		$_productId = $this->getRequest()->getParam('productid');
		if($_productId){
		$_product = Mage::getModel('catalog/product')->load($_productId);
		$_sku = $_product->getSku();
		$_actualPrice = $_product->getPrice();
		$_specialPrice = $_product->getFinalPrice();
		$_qty = number_format($_product->getStockItem()->getQty(),2); 
		$returnstring .= "<ul id='productdetail'>";
	 	$returnstring .= '<li><b>SKU</b>: <span id="psku">'.addslashes($_sku).'</span><li>';
	 	$returnstring .= '<li><b>Price</b>: <span id="pactalprice">'.addslashes($_actualPrice).'</span><li>';
	 	$returnstring .= '<li><b>Special Price</b>: <span id="specialprice">'.addslashes($_specialPrice).'</span><li>';
	 	$returnstring .= '<li><b>Qty</b>: <span id="pqty">'.($_qty).'</span><li>';
 		 $returnstring .= "</ul>";
 	 	echo $returnstring;
		}
	}	
public function getProductDataAction($id)
{
	$_productId = $id;
	if($_productId){
		$_product = Mage::getModel('catalog/product')->load($_productId);
		return $_product;
	}
}

public function editAction()
{
    $dealId     = $this->getRequest()->getParam('id');
    $dealModel  = Mage::getModel('deal/deal')->load($dealId);
    //echo "<pre>";
    $dealdata = $dealModel->getdata();
    Mage::register('current_deal_discount_type',$dealdata['discount_type']);
    if ($dealModel->getId() || $dealId == 0) {
    	

        Mage::register('deal_data', $dealModel);

        $this->loadLayout();
        $this->_setActiveMenu('deal/items');
       
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
       
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
       
        $this->_addContent($this->getLayout()->createBlock('deal/adminhtml_deal_edit'))
             ->_addLeft($this->getLayout()->createBlock('deal/adminhtml_deal_edit_tabs'));
           
        $this->renderLayout();
    } else {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('deal')->__('Item does not exist'));
        $this->_redirect('*/*/');
    }
}

public function newAction()
{
    $this->_forward('edit');
}

public function saveAction()
{
    if ( $this->getRequest()->getPost() ) {
        try {
            $postData = $this->getRequest()->getPost();
            $dealModel = Mage::getModel('deal/deal');
            //echo "<pre>";
           // print_r($postData);
            $productdata = $this->getProductDataAction($postData['product_id']);
            $regular_price = $productdata->getFinalPrice();
             $createddate = date('Y-m-d H:i:s');
            
            $dealModel->setId($this->getRequest()->getParam('id'))
                ->setTitle($productdata->getName())
                ->setProductId($postData['product_id'])
                ->setSku($productdata->getSku())
                ->setContent($postData['content'])
                ->setStatus($postData['status'])
                ->setRegularPrice($regular_price)
                ->setQty($postData['qty'])
                ->setStartDate($postData['start_date'])
                ->setEndDate($postData['end_date'])
                ->setFeatured($postData['featured'])
                ->setCreatedTtime($createddate)
            	->setDealPrice($postData['deal_price'])
            	->setStyle($postData['style'])
            	->setDiscountAmount($postData['discount_amount'])
            	->setDiscount($postData['discount'])
            	->setDiscountType($postData['discount_type'])
            	
                
                
                
                
                ->save();
           
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setDealData(false);

            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setDealData($this->getRequest()->getPost());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }
    $this->_redirect('*/*/');
}

public function deleteAction()
{
    if( $this->getRequest()->getParam('id') > 0 ) {
        try {
            $dealModel = Mage::getModel('deal/deal');
           
            $dealModel->setId($this->getRequest()->getParam('id'))
                ->delete();
               
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }
    $this->_redirect('*/*/');
}
/**
 * Product grid for AJAX request.
 * Sort and filter result for example.
 */
public function gridAction()
{
    $this->loadLayout();
    $this->getResponse()->setBody(
           $this->getLayout()->createBlock('deal/adminhtml_deal_grid')->toHtml()
    );
}

public function deletedealAction()
{
	$DeleteId=$this->getRequest()->getParam('deleteId');
	$deleteData = Mage::getModel('deal/deal');
        $deleteData->load($DeleteId);
	$deleteData->delete();	
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
	 $this->_redirect('*/*/');
}

}
