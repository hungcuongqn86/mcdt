function btn_update_handle(p_checkbox_obj,p_url){
	v_value_list = checkbox_value_to_list(p_checkbox_obj,",");
	if (!v_value_list){
		alert("Chua co doi tuong nao duoc chon");
	}else{
		arr_value = v_value_list.split(",");
		if (arr_value.length > 1){
			alert("Chi duoc chon 1 doi tuong")
			return;
		}
		else
			row_onclick(document.getElementById('hdn_record_id'), v_value_list, p_url);
	}
}
function btn_save_handle(p_hdn_tag_obj,p_hdn_value_obj,p_url){
	_save_xml_tag_and_value_list(document.forms[0], p_hdn_tag_obj, p_hdn_value_obj, true);
	if (verify(document.forms[0])){	
		//Hidden luu danh sach the va gia tri tuong ung trong xau XML				
		document.getElementById('hdn_XmlTagValueList').value = p_hdn_tag_obj.value + '|{*^*}|' + p_hdn_value_obj.value;
		//document.getElementsByTagName('form')[0].disabled = true;
		document.getElementsByTagName('form')[0].action = p_url;
		document.getElementsByTagName('form')[0].submit(); 		
	}	
}
function btn_delete_handle(p_checkbox_obj, p_hidden_obj, p_url){	
	_save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'),document.getElementById('hdn_filter_xml_value_list'), true);
	if (!checkbox_value_to_list(p_checkbox_obj,",")){
		alert("chua co doi tuong nao duoc chon");
	}else{
		if(confirm('Ban thuc su muon xoa doi tuong da chon')){			
			p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj,","); //Xac dinh cac phan tu duoc checked va luu vao bien hidden p_hidden_obj
			actionUrl(p_url);
		}
	}
}
function btn_submitorder_handle(p_checkbox_obj, p_hidden_obj, p_url){	
	_save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'),document.getElementById('hdn_filter_xml_value_list'), true);
	if (!checkbox_value_to_list(p_checkbox_obj,",")){
		alert("chua cho doi tuong nao duoc chon");
	}else{
		p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj,","); //Xac dinh cac phan tu duoc checked va luu vao bien hidden p_hidden_obj
		actionUrl(p_url);
	}
}
function 	getLeader(code){
	try{
		if(code.value == 'department' ){
			document.getElementById('unit').style.display = "none"; 
			document.getElementById('department').style.display = "block";
		}
	}catch(e){;}	
	try{
		if(code.value == 'unit' ){
			document.getElementById('department').style.display = "none"; 
			document.getElementById('unit').style.display = "block";
		}
	}catch(e){;}	
}	
function 	getTransition(code){
	try{
		document.getElementById('limit_date22').value ='';
	}catch(e){;}
	try{
		document.getElementById('limit_date23').value ='';
	}catch(e){;}
	try{
		document.getElementById('limit_date24').value ='';
	}catch(e){;}

	try{
		if(code.value == '22' ){
			//Thue
			document.getElementById('work_22').style.display = "block"; 
			document.getElementById('thue_22').setAttribute("optional","");
			document.getElementById('date_22').setAttribute("optional","");
			try{
				//Kho bac
				document.getElementById('work_23').style.display = "none";
				document.getElementById('khobac_23').value ='';
				document.getElementById('khobac_23').setAttribute("optional","true");
				document.getElementById('date_23').value ='';
				document.getElementById('date_23').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Lien thong don vi khac
				document.getElementById('work_24').style.display = "none";
				document.getElementById('lien_thong_24').value ='';
				document.getElementById('lien_thong_24').setAttribute("optional","true");
				document.getElementById('date_24').value ='';
				document.getElementById('date_24').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Thu ly
				document.getElementById('work_20').style.display = "none";
				document.getElementById('thuly_20').value ='';
				document.getElementById('thuly_20').setAttribute("optional","true");
			}catch(e){;}
		}
	}catch(e){;}	
	try{
		if(code.value == '20' ){
			//Thu ly
			document.getElementById('work_20').style.display = "block"; 
			document.getElementById('thuly_20').setAttribute("optional","");
			try{
				//Kho bac
				document.getElementById('work_23').style.display = "none";
				document.getElementById('khobac_23').value ='';
				document.getElementById('khobac_23').setAttribute("optional","true");
				document.getElementById('date_23').value ='';
				document.getElementById('date_23').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Lien thong don vi khac
				document.getElementById('work_24').style.display = "none";
				document.getElementById('lien_thong_24').value ='';
				document.getElementById('lien_thong_24').setAttribute("optional","true");
				document.getElementById('date_24').value ='';
				document.getElementById('date_24').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Thue
				document.getElementById('work_22').style.display = "none";
				document.getElementById('thue_22').value ='';
				document.getElementById('thue_22').setAttribute("optional","true");
				document.getElementById('date_22').value ='';
				document.getElementById('date_22').setAttribute("optional","true");
			}catch(e){;}
		}
	}catch(e){;}	
	try{
		if(code.value == '23' ){
			//Kho bac 
			document.getElementById('work_23').style.display = "block"; 
			document.getElementById('khobac_23').setAttribute("optional","");
			document.getElementById('date_23').setAttribute("optional","");
			try{
				//Thue
				document.getElementById('work_22').style.display = "none";
				document.getElementById('thue_22').value ='';
				document.getElementById('thue_22').setAttribute("optional","true");
				document.getElementById('date_22').value ='';
				document.getElementById('date_22').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Lien thong don vi khac
				document.getElementById('work_24').style.display = "none";
				document.getElementById('lien_thong_24').value ='';
				document.getElementById('lien_thong_24').setAttribute("optional","true");
				document.getElementById('date_24').value ='';
				document.getElementById('date_24').setAttribute("optional","true");
			}catch(e){;}	
			try{
				//Thu ly
				document.getElementById('work_20').style.display = "none";
				document.getElementById('thuly_20').value ='';
				document.getElementById('thuly_20').setAttribute("optional","true");
			}catch(e){;}
		}
	}catch(e){;}
	try{
		if(code.value == '24' ){
			//Lien thong don vi khac
			document.getElementById('work_24').style.display = "block"; 
			document.getElementById('lien_thong_24').setAttribute("optional","");
			document.getElementById('date_24').setAttribute("optional","");
			try{
				//Kho bac
				document.getElementById('work_23').style.display = "none";
				document.getElementById('khobac_23').value ='';
				document.getElementById('khobac_23').setAttribute("optional","true");
				document.getElementById('date_23').value ='';
				document.getElementById('date_23').setAttribute("optional","true");
			}catch(e){;}
			try{	
				//Thue 
				document.getElementById('work_22').style.display = "none";
				document.getElementById('thue_22').value ='';
				document.getElementById('thue_22').setAttribute("optional","true");
				document.getElementById('date_22').value ='';
				document.getElementById('date_22').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Thu ly
				document.getElementById('work_20').style.display = "none";
				document.getElementById('thuly_20').value ='';
				document.getElementById('thuly_20').setAttribute("optional","true");
			}catch(e){;}
		}
	}catch(e){;}
	try{
		if(code.value == '10' || code.value == '41' || code.value == '40'){
			try{
				//Kho bac
				document.getElementById('work_23').style.display = "none";
				document.getElementById('khobac_23').value ='';
				document.getElementById('khobac_23').setAttribute("optional","true");
				document.getElementById('date_23').value ='';
				document.getElementById('date_23').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Thue 
				document.getElementById('work_22').style.display = "none";
				document.getElementById('thue_22').value ='';
				document.getElementById('thue_22').setAttribute("optional","true");
				document.getElementById('date_22').value ='';
				document.getElementById('date_22').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Lien thong don vi khac
				document.getElementById('work_24').style.display = "none";
				document.getElementById('lien_thong_24').value ='';
				document.getElementById('lien_thong_24').setAttribute("optional","true");
				document.getElementById('date_24').value ='';
				document.getElementById('date_24').setAttribute("optional","true");
			}catch(e){;}
			try{
				//Thu ly
				document.getElementById('work_20').style.display = "none";
				document.getElementById('thuly_20').value ='';
				document.getElementById('thuly_20').setAttribute("optional","true");
			}catch(e){;}
		}
	}catch(e){;}				
}
function 	getTax(code){	
	try{
		if(code.value == 'TINH_THUE' ){
			document.getElementById('work_2').style.display = "none";
			document.getElementById('work_1').style.display = "block"; 
		}
	}catch(e){;}
	try{
		if(code.value == 'CB_THUE_TUCHOI' ){
			document.getElementById('work_1').style.display = "none";
			document.getElementById('work_2').style.display = "block"; 
		}
	}catch(e){;}
}
function  check_handle(id){
	id.checked = true;	
}
