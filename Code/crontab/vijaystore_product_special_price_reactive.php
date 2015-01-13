<?php
/*
Set this file in cron-tab
*/

error_reporting(E_ALL | E_STRICT);

//** Enter the Correct Mage.php URL 
require_once('var/www/magento/app/Mage.php');
Mage::app();

$active 	= Vijaystore_Specialprice_Model_Specialpricedate::SPECIAL_PRICE_ACTIVE;
$process 	= Vijaystore_Specialprice_Model_Specialpricedate::SPECIAL_PRICE_PROCESS;
$inactive 	= Vijaystore_Specialprice_Model_Specialpricedate::SPECIAL_PRICE_INACTIVE;


$date = date('Y-m-d',strtotime("-1 days"));//date('Y-m-d');

$collection = Mage::getModel('specialprice/specialprice')->getCollection()
->addFieldToFilter('status', $process)
->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
->addFieldToFilter('special_price_to_date', array('date' => true, 'from' => $date));

$results = $collection->getData();

if(count($results) > 0)
{	
	foreach($results as $key => $val)
	{
		$id = $val['id'];
		$product_id = $val['product_id'];	
		if($product_id > 0)
		{
			try {
				//** update special price
				//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$product = Mage::getModel('catalog/product')->load($product_id);
				$product->setSpecialPrice('')->setSpecialToDate('')->setSpecialFromDate('');
				$product->save();

				//** Update the Active
				$update_status = Mage::getModel('specialprice/specialprice')->load($id);
				$update_status->setStatus($inactive);
				$update_status->save();
			} 
			catch (Exception $e){
				//echo $e->getMessage();  
			}
		}
	}
}
echo "done";

?>