//Ham nay dung de kiem tra em cac ket qua nhap vao co hop le hay khong khi nhap
function check_value_in_form(){
	
}
function set_upcase_value(p_obj){
	v_value = p_obj.value;
	p_obj.value = v_value.toUpperCase();
}
function thongbao(obj){
	if (document.all.investor_reason.style.display == "block"){
		document.all.investor_reason.style.display = "none";
	}else{
		document.all.investor_reason.style.display = "block";
	}
}
//Ham dung de dat cac gi tri mac dinh tren form
function set_input(){
	//alert("Da load xong form");
	show_hide_row();
}
function init_address(p_obj){
	document.forms[0].investor_temporary_address.value = p_obj.value;
	document.forms[0].business_place.value = p_obj.value;
}

function show_row(p_row_id){
	document.getElementById(p_row_id ).style.display = "";
}
function hide_row(p_row_id){
	document.getElementById(p_row_id ).style.display = "none";
}

function show_hide_row(){
	try{
		if (document.forms[0].record_form.value == "DKTD" || document.forms[0].record_form.value=="DKCL"){
			show_row('id_relate_record_code');
			show_row('id_record_license');
			document.getElementById('record_number').setAttribute('optional',false);
		} else{
			//alert('ok1');
			hide_row('id_relate_record_code');
			hide_row('id_record_license');
			document.getElementById('record_number').setAttribute('optional',true);
		}
	}catch(e){;} 
}
function get_minus_document(){	
	//alert('ok');
	var str = "";
	var j = 0;
	//alert(document.forms[0].chk_item_id.length);
	for (var i=0;i<document.forms[0].chk_item_id.length;i++){
		if ((document.forms[0].chk_item_id[i].checked == false) && (document.forms[0].chk_item_id[i].getAttribute("xml_tag_in_db_name") == "include_document")){
			j++;
			str = str + j + "."  + document.forms[0].chk_item_id[i].getAttribute("label") + ";\n"; 
		}
	}	
	document.forms[0].minus_documnent.value = str;
}
//Ham nay dung de xac dinh trang thai la nhap bo sung ho so hay khong?
function show_hide_record(){
	var v_temp = document.forms[0].minus_documnent.value;
	if (!document.forms[0].status_record.checked){
		hide_row('get_minus_documnent');
		hide_row('minus_documnent');		
	}else{		
		show_row('get_minus_documnent');
		show_row('minus_documnent');
		if(test_form_element('txt_appointed_date')){
			document.forms[0].txt_appointed_date.value = "";
		}
	}
}
//Ham nay dung de kiem tra xem NSD chon vao trang thai la thieu HS nhung lai khong nhap cac tai lieu kem theo
function test_status_show_hide_record(){
	var v_temp = document.forms[0].minus_documnent.value;
	var v_test_element = false;
	if(test_form_element("txt_appointed_date")){
		v_test_element = true;
	}
	if (document.forms[0].status_record.checked){
		if(isblank(v_temp) || (v_temp==null) || (v_temp=="")){
			alert('Bạn cần phải nhập các tài liệu kèm theo của hồ sơ.');
			if(v_test_element){			
				document.forms[0].txt_appointed_date.value = "";
				document.forms[0].txt_appointed_date.focus();
			}
			return;
		}			
	}else{
			if(v_test_element){
				var v_appointed_date = document.forms[0].txt_appointed_date.value;
				if(isblank(v_appointed_date) || (v_appointed_date==null) || (v_appointed_date=="")){
					alert('Bạn phải nhập ngày hẹn trả hồ sơ (bạn có thể chọn vào ngày tiếp nhận hệ thống sẽ trả lại ngày hẹn trả kết quả).');			 
					document.forms[0].txt_appointed_date.focus();
					return;
				}else{
						if(!isdate(v_appointed_date)){
							alert('Ngày hẹn trả kết quả không hoepj lệ (bạn có thể chọn vào ngày tiếp nhận hệ thống sẽ trả lại ngày hẹn trả kết quả).');	
							document.forms[0].txt_appointed_date.focus();
							return;
						}
					}
			}
		 }
		if (verify(document.forms[0])){
			document.forms[0].fuseaction.value = "UPDATE_RECORD";
			document.forms[0].btn_update.disabled = true;
			document.forms[0].submit();
		}			 
}
//-----chinh mot xau con trong mot xau
function f_substring(){	
	var str,substr1,substr2,name1,name2,dvct;
	name1 = "0217A";
	name2 = "0227A";	
	dvct = document.forms[0].donvi_tructhuoc.value;
	str = document.forms[0].record_number.value;
	if(!isblank(str) && (str!="") && (str!=null)){
		substr1 = str.substring(5,str.length);
		if(!isblank(dvct) && (dvct!="") && (dvct!=null)){
			if(dvct == "Là chi nhánh"){
				document.forms[0].record_number.value = name1 + substr1;			
			}
			if(dvct == "Là văn phòng đại diện"){
				document.forms[0].record_number.value = name2  + substr1;
			}			
		}
	}
}