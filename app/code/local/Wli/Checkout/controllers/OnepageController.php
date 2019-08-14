<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Wli_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
  public function saveOrderAction()
    {
      
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*');
            return;
        }

        if ($this->_expireAjax()) {
            return;
        }
   
        $result = array();
        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
 
            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

        //  echo "here";die;
            $result = array();
		$i = 0;
                  $cart = Mage::getModel('checkout/cart')->getQuote();
		foreach ($cart->getAllItems() as $item)
		{
			$result[$i]['id'] = $item->getProduct()->getId();
			$result[$i]['qty'] = $item->getQty();
			$i++;
		}
             //   echo "<pre>";
             //   print_r($result);
		$counter= 0;
		foreach ($result as $cartitem) {
			
			$collection = 	Mage::getModel('deal/deal')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('product_id',$cartitem['id'],'AND');
			
			$dealdata[$counter]   = ($collection->getData());
			
                        $dealdata[$counter][0]['qty']."-".$cartitem['qty'];
                        $newqty =  $dealdata[$counter][0]['qty']-$cartitem['qty'];
                        if($cartitem['qty'] > $dealdata[$counter][0]['qty'])
			{
				$newqty = 0;
			}
                       // echo "<br>".$newqty."=".$cartitem['id'];
                        
                        $connection = Mage::getSingleton('core/resource')  
			->getConnection('core_write');
			$table_name=Mage::getSingleton('core/resource')->getTableName('deal');  
			$connection->beginTransaction();  
			$fields = array();  
			$fields['qty'] = $newqty;  
			$where = $connection->quoteInto('product_id =?', $cartitem['id']);  
			$connection->update($table_name, $fields, $where);  
			$connection->commit();
                        $counter++;
			
		}
                
                
            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
