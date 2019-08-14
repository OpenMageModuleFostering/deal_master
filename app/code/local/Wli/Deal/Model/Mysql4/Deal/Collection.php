<?php
class Wli_Deal_Model_Mysql4_Deal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
public function _construct()
{
    //parent::__construct();
    $this->_init('deal/deal');
}
}
