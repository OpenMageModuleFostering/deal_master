<?php
class Wli_Deal_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->renderLayout();
	}
	public function setstatusAction()
	{
		$deal_id = $this->getRequest()->getParam('deal_id');
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('deal/deal');
		$query = "UPDATE {$table} SET status = 0 WHERE deal_id = ".$deal_id;
		$writeConnection->query($query);
	}
}
