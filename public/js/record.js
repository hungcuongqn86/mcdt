function btn_save_record(p_hdn_tag_obj,p_hdn_value_obj,p_url,UrlAjax){
	 //DisableButton(btn_ghivaquaylai);
	//Dung jquery Ajax kiem tra trung ma ho so
	arrUrl = UrlAjax.split('/');
	//Kiem tra neu la edit thi ko kiem tra trung ma ho so
	if(document.getElementById('hdn_object_id').value != ''){
		_save_xml_tag_and_value_list(document.forms[0], p_hdn_tag_obj, p_hdn_value_obj, true);
		if (verify(document.forms[0])){	
			//Hidden luu danh sach the va gia tri tuong ung trong xau XML			
			document.getElementById('hdn_XmlTagValueList').value = p_hdn_tag_obj.value + '|{*^*}|' + p_hdn_value_obj.value;	
			document.getElementById('hdn_is_update').value='1';
			document.getElementsByTagName('form')[0].action = p_url;
			document.getElementsByTagName('form')[0].submit(); 
			//document.getElementById('button').disabled = 'true';
		}	
	}else
	    $("#ajax").load(arrUrl[0] + '/' + arrUrl[1] + '/' + arrUrl[2] + '/' + arrUrl[3] + "/public/ajax/checkRecordCode.php", 
					  {RecordId: document.getElementById('C_CODE').value}
	    			,function callback(){
						  if($("#ajax").text() != ""){
								alert("Ma ho so nay da ton tai");
								return false;
							}else{
								_save_xml_tag_and_value_list(document.forms[0], p_hdn_tag_obj, p_hdn_value_obj, true);
								if (verify(document.forms[0])){	
									//Hidden luu danh sach the va gia tri tuong ung trong xau XML			
									document.getElementById('hdn_XmlTagValueList').value = p_hdn_tag_obj.value + '|{*^*}|' + p_hdn_value_obj.value;	
									document.getElementById('hdn_is_update').value='1';
									document.getElementsByTagName('form')[0].action = p_url;
									document.getElementsByTagName('form')[0].submit(); 
								}	
							}
		});	
}


function selectrowradio(obj){
	$('td').parent().removeClass('selected');
	$(obj).parent().parent().addClass('selected');
}

//Ham btn_sent_onclick duoc goi khi NSD bam vao nut 'gui'
function btn_updatemovehandle_onclick(radio_obj,p_url){
	if(!test_date(document.getElementById('C_WORK_DATE').value)){
		alert('Ngày chuyển không đúng định dạng ngày/tháng/năm');
		return false;
	}
	isvalue = 0;
	for(i = 0; i < radio_obj.length; i++){
		if(radio_obj[i].checked){
			isvalue = 1;
			break;
		}
	}
	if(isvalue == 0){
		if(document.getElementsByName('objectHandle')[0].checked)
			alert('Chưa ch�?n phòng ban thụ lý hồ sơ');
		if(document.getElementsByName('objectHandle')[1].checked)
			alert('Chưa ch�?n cán bộ thụ lý hồ sơ');
		return false;
	}else{
		if(document.getElementsByName('objectHandle')[1].checked && HandleIdList == ''){
			alert('Chưa ch�?n cán bộ thụ lý hồ sơ');
			return false;
		}
		//Kiem tra neu chi co 1 phong ban thu ly,1 can bo thu ly va NSD click chuyen phong ban thi se cap nhat cho can bo thu ly
		if(HandleIdList.length ==1 && HandleIdList != '' && DepartmentIdList.length == 1 && DepartmentIdList != ''  && document.getElementsByName('objectHandle')[0].checked){
			document.getElementsByName('objectHandle')[1].checked = true;
			radio_obj[0].checked = true;
		}
		document.getElementsByTagName('form')[0].action = p_url;
		document.getElementsByTagName('form')[0].submit();
	}
}

function set_hidden_movehandle(obj, radio_obj, value){
	for(i = 0; i< radio_obj.length; i++){
		if(radio_obj[i].value == value && radio_obj[i].disabled == false){
			radio_obj[i].checked = true;
			$('td').parent().removeClass('selected');
			$(obj).parent().addClass('selected');
		}else{
			radio_obj[i].checked = false;
		}		
	}
}
function btn_getreceive_onclick(p_checkbox_obj, p_url){
	record_id_list = checkbox_value_to_list(p_checkbox_obj,",");
	if (!record_id_list){
		alert("Chưa có đối tượng nào được ch�?n");
	}else{
		document.getElementById('hdn_object_id_list').value = record_id_list;
		actionUrl(p_url);	
	}
}
//Ham btn_sent_onclick duoc goi khi NSD bam vao nut 'gui'
function btn_updatereceive_onclick(p_url){
	if(!test_date(document.getElementById('C_WORK_DATE').value)){
		alert('Ngày thực hiện không đúng định dạng ngày/tháng/năm');
		return false;
	}
	if(document.getElementById('C_IDEA').value == ''){
		alert('Phải xác định thông tin ý kiến!');
		document.getElementById('C_IDEA').focus();
		return false;
	}
	appointed_date(document.getElementById('C_WORK_DATE').value,document.getElementById('hdn_number_process_date'),document.getElementById('hdn_appointed_date'));
	document.getElementsByTagName('form')[0].action = p_url;
	document.getElementsByTagName('form')[0].submit();
}
//Ham btn_printRecordTransitionList_onclick duoc goi khi NSD bam vao nut In tren danh sach ho so lien thong cho nhan
function btn_printRecordTransitionList_onclick(sRecordTypeId, fullTextSearch, p_url){
	p_url = p_url + '?sRecordTypeId=' + sRecordTypeId + '&fullTextSearch=' + fullTextSearch;
	sRtn = showModalDialog(p_url,"","dialogWidth=1px;dialogHeight=1px;status=no;scroll=no;dialogCenter=yes");			
    if (sRtn!=""){
		window.open(sRtn);
    }	
}
function btn_save_result(p_hdn_tag_obj,p_hdn_value_obj,p_url){
	_save_xml_tag_and_value_list(document.forms[0], p_hdn_tag_obj, p_hdn_value_obj, true);
	if (verify(document.forms[0])){	
		//Hidden luu danh sach the va gia tri tuong ung trong xau XML				
		document.getElementById('hdn_XmlTagValueList').value = p_hdn_tag_obj.value + '|{*^*}|' + p_hdn_value_obj.value;
		//document.getElementsByTagName('form')[0].disabled = true;
		document.getElementsByTagName('form')[0].action = p_url;
		document.getElementsByTagName('form')[0].submit(); 		
	}	
}
//Kiem tra kieu so va ki tu duoc phep su dung
function isEdit(keycode,str)
{
	if ((keycode>=48 && keycode <=57) || (keycode>=96 && keycode <=105) || (keycode == 46) || (keycode == 8) || (keycode == 17 ) || (keycode == 37 ) || (keycode == 39 ) || (keycode == 9 ) || (keycode == 16 ) || (keycode == 13 ) || (keycode == 35 ) || (keycode == 36 )){
		return true;
	}else{
		alert("Chỉ sử dụng số từ 0 đến 9!")
		str.value = '';
		return false;
	}	
}

// Tu dong them dau phay
function AddComma(str,e){
	var keycode;
	var delimitor;
	//keycode=window.event.keyCode
	keycode = (window.event)?event.keyCode:e.which; 
	//alert(keycode);
	isEdit(keycode,str);
	//alert(keycode);
	var lengthValue = str.value.length;
	if( lengthValue >= 4 ){
		delimitor = str.value.split(",");
		str.value = '';
		for(i=0;i<delimitor.length;i++){
			str.value = str.value + delimitor[i];
		}
		delimitor = str.value;
		str.value = '';
		for(i=0;i< delimitor.length;i++){
			str.value = str.value + delimitor[i];
			if(delimitor.length > 3 && (delimitor.length - 1 > i) ){
				if( (delimitor.length % 3 == 0) && (i % 3 == 2)){
					str.value = str.value + ',';
				}else if((delimitor.length % 3 == 1) && (i % 3 == 0)){
					str.value = str.value + ',';
				}else if((delimitor.length % 3 == 2) && (i % 3 == 1)) {
					str.value = str.value + ',';
				}
			}	
		}
		//str.value = str.value.substring(0,str.value.length -3) + ',' + str.value.substring(str.value.length -3,str.value.length);
	}	
}
function btn_send_result(p_checkbox_obj, p_hidden_obj, p_url){	
	_save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'),document.getElementById('hdn_filter_xml_value_list'), true);
	if (!checkbox_value_to_list(p_checkbox_obj,",")){
		alert("chua co doi tuong nao duoc chon");
	}else{
		p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj,","); //Xac dinh cac phan tu duoc checked va luu vao bien hidden p_hidden_obj
		actionUrl(p_url);
	}
}
function writeTime(s)
{
	var mydate;
	if (s)
		mydate = new Date(s);
	else
		mydate = new Date();
	
	var year = mydate.getYear()
	if (year < 1000)
		year += 1900
	var month = mydate.getMonth() + 1
	if (month < 10)
		month = "0" + month
	var day = mydate.getDate()
	if (day < 10)
		day = "0" + day

	var dayw = mydate.getDay()
	
	var hrNow = mydate.getHours()
	
	if (hrNow == 0) {
		hour = 12;
		ap = " am";
	} else if(hrNow <= 11) {
		ap = " am";
		hour = hrNow;
	} else if(hrNow == 12) {
		ap = " pm";
		hour = 12;
	} else if (hrNow >= 13) {
		hour = (hrNow - 12);
		ap = " pm";
	}
	
	var minute=mydate.getMinutes()
	if (minute < 10)
		minute = "0" + minute
	var dayarray=new Array("Ch&#7911; Nh&#7853;t","Th&#7913; Hai","Th&#7913; Ba","Th&#7913; T&#432;","Th&#7913; N&#259;m","Th&#7913; S&#225;u","Th&#7913; B&#7843;y")
	document.write(dayarray[dayw]+", ng&#224;y "+day+"-"+month+"-"+year)
}
function checkbox_all_item_id(p_chk_obj){
	//remove class cua tat ca cac tr trong table
	$('tr').removeClass('selected');
	try{
		v_count = p_chk_obj.length;
		if(v_count){
			if(document.forms[0].chk_all_item_id.checked == true){
				for(i=0;i<=v_count;i++){
					if(p_chk_obj[i].disabled == false && (p_chk_obj[i].value != 0) ){
						p_chk_obj[i].checked = 'checked';
						$(p_chk_obj[i]).parent().parent().addClass('selected');
					}
				}
			}else{
				for(i=0;i<p_chk_obj.length;i++){
					p_chk_obj[i].checked = '';
				}		
			}
		}else{
			if(document.forms[0].chk_all_item_id.checked == true){
				if(p_chk_obj.disabled == false){
					p_chk_obj.checked = 'checked';
					$(p_chk_obj).parent().parent().addClass('selected');
				}
			}else{
				p_chk_obj.checked = '';
			}
		}
	}catch(e){;}
}
/**
 * 
 */
function btn_addwork(p_checkbox_obj, p_hidden_obj, p_url){	
	_save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'),document.getElementById('hdn_filter_xml_value_list'), true);
	if (!checkbox_value_to_list(p_checkbox_obj,",")){
		alert("chua cho doi tuong nao duoc chon");
	}else{
		p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj,","); //Xac dinh cac phan tu duoc checked va luu vao bien hidden p_hidden_obj
		actionUrl(p_url);
	}
}
function check_color_handle(obj, radio_obj, value){
	for(i = 0; i< radio_obj.length; i++){
		if(radio_obj[i].value == value && radio_obj[i].disabled == false){
			radio_obj[i].checked = true;
			$('td').parent().removeClass('selected');
			$(obj).addClass('selected'); //Adclass
		}else{
			radio_obj[i].checked = false;
		}		
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