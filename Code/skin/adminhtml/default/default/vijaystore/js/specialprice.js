//** Date-picker Position changing
function display_calender(arg)
{
	if(arg == 1) {
		$$('.calendar').each(function(ele) {
			if(ele.getStyle('display') == "block")
				ele.setStyle({'left':'650px', 'top':'415px', 'zIndex':1000});
		});
	}
	else {
		$$('.calendar').each(function(ele) {
			if(ele.getStyle('display') == "block")
				ele.setStyle({'left':'650px', 'top':'450px', 'zIndex':1000});
		});
	}
	
}

//** Numbers only accept the text box
function isNumberKey(key, txt)
{
	var keycode = (key.which) ? key.which : key.keyCode;	
	if (keycode == 46) {
        if (txt.value.indexOf(".") < 0) 
            return true;
        else
            return false;
    }
	if (keycode > 31 && (keycode < 48 || keycode > 57) && keycode != 47)
		return false;
	else
		return true;
}

//** Compare From/To Dates
function CompareDates(str1, str2) 
{ 
	var dt1  = parseInt(str1.substring(0,2),10); 
	var mon1 = parseInt(str1.substring(3,5),10);
	var yr1  = parseInt(str1.substring(6,10),10); 
	var dt2  = parseInt(str2.substring(0,2),10); 
	var mon2 = parseInt(str2.substring(3,5),10); 
	var yr2  = parseInt(str2.substring(6,10),10); 
	var date1 = new Date(yr1, mon1, dt1); 
	var date2 = new Date(yr2, mon2, dt2); 

	if(date2 < date1)
		return false; 
	return true; 
}
	
//** Reset the popup
function reset_popup_value()
{
	$('vj_special_price').value = "";
	$('vj_special_price_from_date').value = "";
	$('vj_special_price_to_date').value = "";
	$('sp_update').setStyle({'display': 'none'});
	$('sp_save').setStyle({'display': 'block'});
	
	$$('.validation-advice').each(function(ele) {
		ele.setStyle({'display': 'none'});
	});
}

//** Save Function 
function save_special_price()
{
	var vj_special_price = $F('vj_special_price');
	var vj_special_price_from_date = $F('vj_special_price_from_date');
	var vj_special_price_to_date = $F('vj_special_price_to_date');
	var current_product_id = $F('current_product_id');
	var post_url_add = $F('special_price_url_add');
	var current_store_id = $F('current_store_id');
	
	$$('.validation-advice').each(function(ele) {
		ele.setStyle({'display': 'none'});
	});
	if(vj_special_price == ""){
		$("sp1_require").setStyle({'display': 'block'});
		$("vj_special_price").focus();
		return false;
	}
	if(vj_special_price_from_date == ""){
		$("sp2_require").setStyle({'display': 'block'});
		$("vj_special_price_from_date").focus();
		return false;
	}
	if(vj_special_price_to_date == ""){
		$("sp3_require").setStyle({'display': 'block'});
		$("vj_special_price_to_date").focus();
		return false;
	}
	if(!CompareDates(vj_special_price_from_date, vj_special_price_to_date)){
		$("sp4_require").setStyle({'display': 'block'});
		$("vj_special_price_from_date").focus();
		return false;
	}
	
	$("loading-mask").setStyle({'zIndex':1000});
	$("loading-mask").show();
	
	new Ajax.Request(post_url_add, {
		method:'post',
		parameters: {product_id:current_product_id, store_id:current_store_id, special_price:vj_special_price, status:0, special_price_from_date:vj_special_price_from_date, special_price_to_date:vj_special_price_to_date},
		onSuccess: function(transport) {
			var response = transport.responseText || "no response text";
			obj = JSON.parse(response);
			if(obj.success == 201)
			{
				$("sp5_require").setStyle({'display': 'block'});
				$("vj_special_price_from_date").focus();
				return;
			}
			else if(obj.success == 200)
			{

				if (document.getElementById("no_data_special_price")) {
					var no_data_rowindex = document.getElementById("no_data_special_price").rowIndex;
					document.getElementById("special_price_list").deleteRow(no_data_rowindex);					
				}
				//<td>"+obj.insert_id+"</td>
				var new_element ="<tr id=special_price_list_row"+obj.insert_id+"><td>"+obj.special_price_from_date+"</td><td>"+obj.special_price_to_date+"</td><td>"+obj.special_price+"</td><td><a href='javascript:' onclick='return retrieve_special_price("+obj.insert_id+");'>Edit </a> &nbsp;&nbsp;&nbsp; <a href='javascript:' onclick='return delete_special_price("+obj.insert_id+");'>Delete</a></td></tr>";
				$('special_price_list').insert(new_element);
				closeMessagePopup();
			}
		},
		onFailure: function() { alert('Something went wrong...'); }
	});
	
}

//** Retrieve Special Price Function 
function retrieve_special_price(update_id){
	var post_url_sel = $F('special_price_url_sel');
	var current_store_id = $F('current_store_id');
	var current_product_id = $F('current_product_id');
		
	$("loading-mask").setStyle({'zIndex':1000});
	$("loading-mask").show();
		
	new Ajax.Request(post_url_sel, {
		method:'post',
		parameters: {product_id:current_product_id, store_id:current_store_id, id:update_id},
		onSuccess: function(transport) {
			var response = transport.responseText || "no response text";
			obj = JSON.parse(response);
			//**------- show the popup
				special_price_popup();
				$('sp_update').setStyle({'display': 'block'});
				$('sp_save').setStyle({'display': 'none'});
			// show the popup --------------
			
			$('vj_special_price').value = obj.data.special_price;
			$('vj_special_price_from_date').value = obj.data.special_price_from_date;
			$('vj_special_price_to_date').value = obj.data.special_price_to_date;
			$('update_sp_id').value = obj.data.id;

		},
		onFailure: function() { alert('Something went wrong...'); }
	});
}

//** Delete Special Price Function 
function delete_special_price(delete_id){
	if (confirm("Are you sure want to deleted this record?"))
	{
		var post_url_del = $F('special_price_url_del');
		var current_store_id = $F('current_store_id');
		var current_product_id = $F('current_product_id');
		
		$("loading-mask").setStyle({'zIndex':1000});
		$("loading-mask").show();
		
		new Ajax.Request(post_url_del, {
			method:'post',
			parameters: {product_id:current_product_id, store_id:current_store_id, id:delete_id},
			onSuccess: function(transport) {
				var response = transport.responseText || "no response text";
				obj = JSON.parse(response);
				if(obj.success == 201)
				{
					sure_delete_special_price(obj.delete_id);
				}
				else if(obj.success == 200)
				{
					var del_row_index = document.getElementById("special_price_list_row"+obj.delete_id).rowIndex;
					document.getElementById("special_price_list").deleteRow(del_row_index);
					
					var tbl_row_length = document.getElementById("special_price_list").rows.length;
					if(tbl_row_length == 1)
						$('special_price_list').insert("<tr id='no_data_special_price'><td colspan='4'>No Record Found</td> </tr>");
				}
				
			},
			onFailure: function() { alert('Something went wrong...'); }
		});
	} 
}

//** Delete Special Price and set null in special price/special price dates  Function 
function sure_delete_special_price(delete_id)
{
	if (confirm("Can i remove the special price?"))
	{
		var post_url_sdel = $F('special_price_url_sdel');
		var current_store_id = $F('current_store_id');
		var current_product_id = $F('current_product_id');
		
		$("loading-mask").setStyle({'zIndex':1000});
		$("loading-mask").show();
		
		new Ajax.Request(post_url_sdel, {
			method:'post',
			parameters: {product_id:current_product_id, store_id:current_store_id, id:delete_id},
			onSuccess: function(transport) {
				var response = transport.responseText || "no response text";
				obj = JSON.parse(response);
				if(obj.success == 201)
				{
					sure_delete_special_price(obj.delete_id);
				}
				else if(obj.success == 200)
				{
					var del_row_index = document.getElementById("special_price_list_row"+obj.delete_id).rowIndex;
					document.getElementById("special_price_list").deleteRow(del_row_index);
					
					document.getElementById("special_price").value = "";
					document.getElementById("special_from_date").value = "";
					document.getElementById("special_to_date").value = "";						
					
					var tbl_row_length = document.getElementById("special_price_list").rows.length;
					if(tbl_row_length == 1) {
						$('special_price_list').insert("<tr id='no_data_special_price'><td colspan='4'>No Record Found</td> </tr>");
					}
					$("loading-mask").hide();
				}
				
			},
			onFailure: function() { alert('Something went wrong...'); }
		});
	}
}

//** Update Special Price
function update_special_price()
{
	var vj_special_price = $F('vj_special_price');
	var vj_special_price_from_date = $F('vj_special_price_from_date');
	var vj_special_price_to_date = $F('vj_special_price_to_date');
	var current_product_id = $F('current_product_id');
	var post_url_upt = $F('special_price_url_upt');
	var current_store_id = $F('current_store_id');
	var update_id = $F('update_sp_id');
	
	$$('.validation-advice').each(function(ele) {
		ele.setStyle({'display': 'none'});
	});
	if(vj_special_price == ""){
		$("sp1_require").setStyle({'display': 'block'});
		$("vj_special_price").focus();
		return false;
	}
	if(vj_special_price_from_date == ""){
		$("sp2_require").setStyle({'display': 'block'});
		$("vj_special_price_from_date").focus();
		return false;
	}
	if(vj_special_price_to_date == ""){
		$("sp3_require").setStyle({'display': 'block'});
		$("vj_special_price_to_date").focus();
		return false;
	}
	if(!CompareDates(vj_special_price_from_date, vj_special_price_to_date)){
		$("sp4_require").setStyle({'display': 'block'});
		$("vj_special_price_from_date").focus();
		return false;
	}
	
	$("loading-mask").setStyle({'zIndex':1000});
	$("loading-mask").show();
	
	new Ajax.Request(post_url_upt, {
		method:'post',
		parameters: {product_id:current_product_id, store_id:current_store_id, special_price:vj_special_price, status:0, special_price_from_date:vj_special_price_from_date, special_price_to_date:vj_special_price_to_date, update_id:update_id},
		onSuccess: function(transport) {
			var response = transport.responseText || "no response text";
			obj = JSON.parse(response);
			if(obj.success == 201)
			{
				$("sp5_require").setStyle({'display': 'block'});
				$("vj_special_price_from_date").focus();
				return;
			}
			else if(obj.success == 200)
			{

				if (document.getElementById("no_data_special_price")) {
					var no_data_rowindex = document.getElementById("no_data_special_price").rowIndex;
					document.getElementById("special_price_list").deleteRow(no_data_rowindex);					
				}
				//<td>"+obj.insert_id+"</td>
				var new_element ="<td>"+obj.special_price_from_date+"</td><td>"+obj.special_price_to_date+"</td><td>"+obj.special_price+"</td><td><a href='javascript:' onclick='return retrieve_special_price("+obj.insert_id+");'>Edit </a> &nbsp;&nbsp;&nbsp; <a href='javascript:' onclick='return delete_special_price("+obj.insert_id+");'>Delete</a></td>";
				
				$('special_price_list_row'+obj.insert_id).update(new_element);
				
				closeMessagePopup();
			}
		},
		onFailure: function() { alert('Something went wrong...'); }
	});
}