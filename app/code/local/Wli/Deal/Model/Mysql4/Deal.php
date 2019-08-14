<?php
class Wli_Deal_Model_Mysql4_Deal extends Mage_Core_Model_Mysql4_Abstract
{
public function _construct()
{   
    $this->_init('deal/deal', 'deal_id');
}
}
