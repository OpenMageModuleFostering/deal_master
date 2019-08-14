<?php 
class Wli_Deal_Block_Adminhtml_Deal_Renderer_Deletedeal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    } 
    protected function _getValue(Varien_Object $row)
    {
       	$deleteDealId=$row['deal_id'];
	$link= $this->getUrl('*/*/deletedeal').'?deleteId='.$deleteDealId;
//	echo "<a onclick=confirm('are you sure want to delete this deal ?') href='".$link."'>Delete</a>";
	echo  '<a onclick="return confirm(\'Are you sure to delete this deal?\')" href="'.$link.'">Delete</a>';
    }
}
