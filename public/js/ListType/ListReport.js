// Ham btn_save_list duoc goi khi NSD nhan vao nut "Cap Nhat" tren form cap nhat 1 doi tuong
function btn_save_listreport(p_hdn_tag_obj,p_hdn_value_obj,p_fuseaction){
	_save_xml_tag_and_value_list(document.forms[0], p_hdn_tag_obj, p_hdn_value_obj, true);	
	if (verify(document.forms[0])){
		document.getElementsByTagName('btn_update').disabled = true;
		document.getElementById('hdn_update').value='1';
		document.getElementsByTagName('form')[0].action = '';
		document.getElementsByTagName('form')[0].submit(); 
	}	
}
function btn_del_onclick(p_hdn_tag_obj, p_hdn_value_obj, p_chk_obj, p_hdn_id_list_obj, p_hdn_page_obj, p_fuseaction){
	_save_xml_tag_and_value_list(document.forms(0),p_hdn_tag_obj,p_hdn_value_obj,false);
	p_hdn_page_obj.value =1;
	//alert(checkbox_value_to_list(p_chk_obj,","));
	btn_delete_onclick(p_chk_obj,p_hdn_id_list_obj,p_fuseaction);
}
// Ham nay duoc goi khi NSD nhan vao nut "Truy van du lieu"
function btn_query_data_onclick(p_hdn_tag_obj,p_hdn_value_obj,p_hdn_page_obj,p_fuseaction){
	_save_xml_tag_and_value_list(document.forms(0), p_hdn_tag_obj, p_hdn_value_obj, false);
	//save_list_onclick(document.forms(0),document.forms(0).hdn_filter_xml_tag_list,document.forms(0).hdn_list_xml_value);
	p_hdn_page_obj.value = 1;
	btn_save_onclick(p_fuseaction);
}
// Ham nay duoc goi khi NSD nhan vao radio "du an da giao dat"
function radio_checked(obj_sel){

	document.forms(0).tu_dong_tang_dan.checked=true;
	document.forms(0).nhap_bang_tay.checked=false;	
	alert(document.forms(0).tu_dong_tang_dan.value);
	//alert("OK");
	if(document.forms(0).tu_dong_tang_dan.checked=true){
		document.forms(0).nhap_bang_tay.checked=false;		
	}else{
		document.forms(0).nhap_bang_tay.checked=true;		
	}
	//document.forms(0).submit();
}
function select_unit_checkbox_treeuser(node){
	var checked = 0;
	var v_count = document.all.chk_staff_id.length;
	if (node.xml_tag_in_db_name=="FK_PROCESSOR"){
		return;
	}
	if(node.checked){
		for(i=0; i < v_count; i++){
			if(checked > 0 && document.all.chk_staff_id[i].parent_id != node.value && document.all.chk_staff_id[i].xml_tag_in_db_name==node.xml_tag_in_db_name){
				return;
			}			
			if(document.all.chk_staff_id[i].parent_id == node.value && document.all.chk_staff_id[i].xml_tag_in_db_name==node.xml_tag_in_db_name){
				document.all.chk_staff_id[i].checked = "checked";
				checked ++;
			}
		}
	}else{
		for(i=0; i < v_count; i++){
			if(checked >0 && document.all.chk_staff_id[i].parent_id != node.value && document.all.chk_staff_id[i].xml_tag_in_db_name==node.xml_tag_in_db_name){
				return;
			}						
			if(document.all.chk_staff_id[i].parent_id == node.value && document.all.chk_staff_id[i].xml_tag_in_db_name==node.xml_tag_in_db_name){
				document.all.chk_staff_id[i].checked = "";
				checked ++;
			}
		}		
	}
}
///////////////////////////////////////////////////////////////
/*
Nguoi tao: HUNGVM
Ngay tao: 01/05/2008
Y nghia: Ham dung check tat ca hoac bo check ta ca
Tham so:
	+ f : ducument.all
	+ objname: Ten doi tuong multiple
	+ control: Bien dieu khien
			= 0: Chon tat ca
			= 1: Bo chon tat ca
			= 2: Chuyen cac phan tu duoc chon vao mot bien ret
*/
function radio_select_option(f,objname,control){
	var v_count = eval('f.' + objname + '.length');
	var ret = "";
	for (i=0;i<v_count;i++){
		if(control == 0){
			eval('f.' + objname + '[i].checked = true');
		}
		if(control == 1){
			eval('f.' + objname + '[i].checked = false');
		}
		if(control == 2 && eval('f.' + objname + '[i].checked')){
			v_value = eval('f.' + objname + '[i].value');
			ret = list_append(ret,v_value,',');
		}
	}
	return ret;
}
///////////////////////////////////////////////////


function show_all_file_onclick(p_goto_url,p_fuseaction,obj_des){
	v_url = _DSP_MODAL_DIALOG_URL_PATH;
	v_url = v_url + p_goto_url ;		
	sRtn = showModalDialog(v_url,"","dialogWidth=400pt;dialogHeight=280pt;dialogTop=80pt;status=no;scroll=no;");
	if (!sRtn) 
		sRtn = obj_des.value;
		obj_des.value = sRtn;	
}

function Get_file_name(v_file_name){
	window.returnValue = v_file_name;
	window.close();
}
function getFileNameFromDiv(v_file_name){	
		document.getElementById("txt_xml_file_name").value = v_file_name;
		divwin.close();
}
//*
	/// thuc hien hien thi khong hien thi nut browse..
//*
function show_hide_div(){	
	var bChecked = document.getElementById('C_CLIENT').checked;	
	if( bChecked == true){
		//  hien thi tu clien
		document.getElementById('popupDialog').style.display= 'none';
		document.getElementById('file_form_clien').style.display= '';
		document.getElementById('file_from_server').style.display= 'none';
	}else{
		// hien thi file tu server	
		document.getElementById('popupDialog').style.display= '';		
		document.getElementById('file_from_server').style.display= '';
		document.getElementById('file_form_clien').style.display= 'none';
	}	
}
function btn_delete_onclick(p_checkbox_obj, p_hidden_obj, p_url){	
	_save_xml_tag_and_value_list(document.forms[0], p_checkbox_obj,p_hidden_obj, true);
	if (!checkbox_value_to_list(p_checkbox_obj,",")){
		alert("chua co ho so nao duoc chon");
	}
	else{
		if(confirm('ban thuc su muon xoa doi tuong da chon ?')){
			p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj,",");
			actionUrl(p_url);
		}
	}
}

