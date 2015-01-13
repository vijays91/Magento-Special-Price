<?php
class Vijaystore_Specialprice_Block_Adminhtml_Catalog_Product_Tab 
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{ 
    public function _construct()
    {
        parent::_construct();         
        $this->setTemplate('vijaystore/specialprice/catalog/product/tab.phtml');
    }
     
    public function getTabLabel()
    {
        return $this->__('Special Price');
    }
     
    public function getTabTitle()
    {
        return $this->__('Special Price');
    }
     
    public function canShowTab()
    {
        return true;
    }
     
    public function isHidden()
    {
        return false;
    }
 
	public function getSpecialPriceList($product_id) 
	{
		return Mage::getModel('specialprice/specialpricedate')->select_special_price_data($product_id);		
	}
}

?>