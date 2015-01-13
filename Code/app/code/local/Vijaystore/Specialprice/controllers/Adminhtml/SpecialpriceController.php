<?php

class Vijaystore_Specialprice_Adminhtml_SpecialpriceController extends Mage_Adminhtml_Controller_Action
{
	//** Insert Special Price
    public function addsplpriceAction() 
	{
		//$post_add_data['store_id'] = Mage::app()->getStore()->getStoreId();
		$post_add_data = $this->getRequest()->getParams();
		$product_id = $post_add_data['product_id'];
		
		$valid_from_date = Mage::getModel('specialprice/specialpricedate')->checkdate($post_add_data['special_price_from_date'], $product_id);
		$valid_to_date   = Mage::getModel('specialprice/specialpricedate')->checkdate($post_add_data['special_price_to_date'], $product_id);
		
		if($valid_from_date == 0 && $valid_to_date == 0) {
			$insertId = Mage::getModel('specialprice/specialpricedate')->insert_special_price_data($post_add_data);
			$insert_data = Mage::getModel('specialprice/specialpricedate')->get_last_special_price_data($insertId);
			$_data = array('insert_id' => $insertId,
				'success' 					=> 200, 
				'special_price'				=> substr($insert_data['special_price'], 0, -2),
				'special_price_from_date' 	=> date('d-m-Y', strtotime($insert_data['special_price_from_date'])),
				'special_price_to_date' 	=> date('d-m-Y', strtotime($insert_data['special_price_to_date'])),
			);				
			//** call cron-tab
			$active_cron   = Mage::getModel('specialprice/specialpricedate')->setActive();
			$inactive_cron = Mage::getModel('specialprice/specialpricedate')->setInActive();
		}
		else {
			$_data = array('fail' => 201,'success' => 201);
		}
		$this->getResponse()->setBody(json_encode( $_data));
    }
	
	//** Retrive the Special Price
	public function selsplpriceAction() 
	{
		$post_add_data = $this->getRequest()->getParams();
		$special_price_id = $post_add_data['id'];
		$product_id = $post_add_data['product_id'];
		$data = Mage::getModel('specialprice/specialpricedate')->get_sel_special_price_data($special_price_id);		
		$data['special_price_from_date'] = date('d-m-Y', strtotime($data['special_price_from_date']));
		$data['special_price_to_date'] 	 = date('d-m-Y', strtotime($data['special_price_to_date']));		
		$data['special_price'] 	 		 = substr($data['special_price'], 0, -2);
		
		$_data = array('data' => $data,'success' => 200 );
		$this->getResponse()->setBody(json_encode($_data));
	}
	
	//** Update Special Price
	public function uptsplpriceAction() 
	{
		$post_add_data = $this->getRequest()->getParams();
		$product_id = $post_add_data['product_id'];
		
		$valid_from_date = Mage::getModel('specialprice/specialpricedate')->checkdate_with_id($post_add_data['special_price_from_date'], $product_id, $post_add_data['update_id']);
		$valid_to_date   = Mage::getModel('specialprice/specialpricedate')->checkdate_with_id($post_add_data['special_price_to_date'], $product_id, $post_add_data['update_id']);
		
		if($valid_from_date == 0 && $valid_to_date == 0) {
			$insertId = Mage::getModel('specialprice/specialpricedate')->upadte_special_price_data($post_add_data);
			$insert_data = Mage::getModel('specialprice/specialpricedate')->get_last_special_price_data($insertId);
			$_data = array('insert_id' => $insertId,
				'success' 					=> 200, 
				'special_price'				=> substr($insert_data['special_price'], 0, -2),
				'special_price_from_date' 	=> date('d-m-Y', strtotime($insert_data['special_price_from_date'])),
				'special_price_to_date' 	=> date('d-m-Y', strtotime($insert_data['special_price_to_date'])),
				);
			//** call cron-tab
			$active_cron   = Mage::getModel('specialprice/specialpricedate')->setActive();
			$inactive_cron = Mage::getModel('specialprice/specialpricedate')->setInActive();
		}
		else {
			$_data = array('fail' => 201,'success' => 201);
		}
		$this->getResponse()->setBody(json_encode( $_data));
	}
	
	//** Delete Special Price and set null 
	public function sdelsplpriceAction() 
	{
		$post_add_data = $this->getRequest()->getParams();
		$special_price_id = $post_add_data['id'];
		$product_id = $post_add_data['product_id'];
		
		//** remove the special price
		$remove_special_price = Mage::getModel('specialprice/specialpricedate')->remove_special_price($special_price_id, $product_id);
		
		//** delete the data
		$delete_id=Mage::getModel('specialprice/specialpricedate')->delete_special_price_data($special_price_id);
		$_data = array('delete_id' => $delete_id,'success' => 200 );
		$this->getResponse()->setBody(json_encode($_data));
	}
	
	//** Delete Special Price	
	public function delsplpriceAction() 
	{
		$post_add_data = $this->getRequest()->getParams();
		$special_price_id = $post_add_data['id'];
		$process = Vijaystore_Specialprice_Model_Specialpricedate::SPECIAL_PRICE_PROCESS;
		
		$delete_data = Mage::getModel('specialprice/specialpricedate')->delete_check_special_price_data($special_price_id);
		
		if($delete_data['status'] == $process){
			$_data = array('delete_id' => $special_price_id,'success' => 201 );
		}
		else 
		{
			$delete_id=Mage::getModel('specialprice/specialpricedate')->delete_special_price_data($special_price_id);
			$_data = array('delete_id' => $delete_id,'success' => 200 );
		}
		$this->getResponse()->setBody(json_encode( $_data));
	}
	
	public function testAction()
	{
		$inactive_cron = Mage::getModel('specialprice/specialpricedate')->setInActive();
	}

}