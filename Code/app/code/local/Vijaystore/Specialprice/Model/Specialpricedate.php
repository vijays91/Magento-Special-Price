<?php
class Vijaystore_Specialprice_Model_Specialpricedate extends Mage_Core_Model_Abstract
{
	const SPECIAL_PRICE_ACTIVE 		= 0;
	const SPECIAL_PRICE_PROCESS 	= 1;
	const SPECIAL_PRICE_INACTIVE 	= 2;
	
	public function checkdate($date, $product_id)
	{
		$collection = Mage::getModel('specialprice/specialprice')->getCollection()
			->addFieldToFilter('status', array('neq' => self::SPECIAL_PRICE_INACTIVE))
			->addFieldToFilter('product_id', $product_id)
			->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
			->addFieldToFilter('special_price_to_date', array('date' => true, 'from' => $date));
		$datecheck = $collection->getData();		
		return count($datecheck);
	}
	
	public function checkdate_with_id($date, $product_id, $special_price_id)
	{
		$collection = Mage::getModel('specialprice/specialprice')->getCollection()
			->addFieldToFilter('status', array('neq' => self::SPECIAL_PRICE_INACTIVE))
			->addFieldToFilter('product_id', $product_id)
			->addFieldToFilter('id', array('neq' => $special_price_id))
			->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
			->addFieldToFilter('special_price_to_date', array('date' => true, 'from' => $date));
		$datecheck = $collection->getData();		
		return count($datecheck);
	}
	
	
	public function insert_special_price_data($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$insert_special_price = Mage::getModel('specialprice/specialprice')->setData($data);		
		return $insert_special_price->save()->getId();
	}
	
	public function select_special_price_data($product_id)
	{
		$get_special_price = Mage::getModel('specialprice/specialprice')->getCollection();
		$get_special_price->addFieldtoFilter('product_id', $product_id);
		$get_special_price->addFieldToFilter('status', array('neq' => self::SPECIAL_PRICE_INACTIVE));
		return $get_special_price->getData();
	}
	
	public function get_last_special_price_data($special_price_id)
	{
		$get_last_special_price = Mage::getModel('specialprice/specialprice')->load($special_price_id);
		return $get_last_special_price->getData();
	}
		
	public function delete_check_special_price_data($special_price_id)
	{
		$get_last_special_price = Mage::getModel('specialprice/specialprice')->load($special_price_id);
		return $get_last_special_price->getData();
	}
	
	public function delete_special_price_data($special_price_id)
	{
		$delete_special_price = Mage::getModel('specialprice/specialprice')->load($special_price_id)->delete();
		return $special_price_id;	
	}
	
	public function remove_special_price($special_price_id, $product_id)
	{
			$product = Mage::getModel('catalog/product')->load($product_id);
			$product->setSpecialPrice('')->setSpecialToDate('')->setSpecialFromDate('');
			$product->save();
			//$delete_id = self::delete_special_price_data($special_price_id);
			return $special_price_id;
	}
	
	public function get_sel_special_price_data($special_price_id)
	{
		$get_last_special_price = Mage::getModel('specialprice/specialprice')->load($special_price_id);
		return $get_last_special_price->getData();
	}
	
	public function upadte_special_price_data($data)
	{
		$special_price_id = $data['update_id'];
		$data['updated_at'] = date('Y-m-d H:i:s');
		$update_special_price = Mage::getModel('specialprice/specialprice')->load($special_price_id)->addData($data);
		$update = $update_special_price->setId($special_price_id)->save();		
		return $special_price_id;
	}
	
	//** Active Special Price
	public function setActive()
	{
		$active 	= self::SPECIAL_PRICE_ACTIVE;
		$process 	= self::SPECIAL_PRICE_PROCESS;
		$inactive 	= self::SPECIAL_PRICE_INACTIVE;

		$date = date('Y-m-d');

		$collection = Mage::getModel('specialprice/specialprice')->getCollection()
		->addFieldToFilter('status', $active)
		->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
		->addFieldToFilter('special_price_to_date', array('date' => true, 'from' => $date));
		$results = $collection->getData();
		
		if(count($results) > 0)
		{	
			foreach($results as $key => $val)
			{
				$id = $val['id'];
				$product_id = $val['product_id'];
				$special_price = $val['special_price'];
				$sp_price_from_date = $val['special_price_from_date'];
				$sp_price_to_date = $val['special_price_to_date'];
				
				if($product_id > 0)
				{
					try
					{
						//** update special price
						//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
						$product = Mage::getModel('catalog/product')->load($product_id);
						$product->setSpecialFromDate($sp_price_from_date);
						$product->setSpecialFromDateIsFormated(true);
						$product->setSpecialToDate($sp_price_to_date);
						$product->setSpecialToDateIsFormated(true);
						$product->setSpecialPrice($special_price);
						$product->save();

						//** Update the Active
						$update_status = Mage::getModel('specialprice/specialprice')->load($id);
						$update_status->setStatus($process);
						$update_status->save();
					}
					catch (Exception $e){
						//echo $e->getMessage();  
					}
				}
			}
		}
	}

	//** In active Special Price
	public function setInActive()
	{
		$active 	= self::SPECIAL_PRICE_ACTIVE;
		$process 	= self::SPECIAL_PRICE_PROCESS;
		$inactive 	= self::SPECIAL_PRICE_INACTIVE;


		$date = date('Y-m-d',strtotime("-1 days"));//date('Y-m-d');

		$collection = Mage::getModel('specialprice/specialprice')->getCollection()
		// ->addFieldToFilter('status', $process)
		// ->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
		// ->addFieldToFilter('special_price_from_date', array('date' => true, 'to' => $date))
		->addFieldToFilter('special_price_to_date', $date);

		$results = $collection->getData();
		
		if(count($results) > 0)
		{
			foreach($results as $key => $val)
			{
				$id = $val['id'];
				$product_id = $val['product_id'];	
				if($product_id > 0)
				{
					try
					{
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
	}
}

?>