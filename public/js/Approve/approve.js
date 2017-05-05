//Ham btn_sent_onclick duoc goi khi NSD bam vao nut 'gui'
function btn_approve_onclick(p_checkbox_obj, p_url){
	record_id_list = checkbox_value_to_list(p_checkbox_obj,",");
	if (!record_id_list){
		alert("Chưa có đối tượng nào được chọn");
	}else{
		document.getElementById('hdn_object_id_list').value = record_id_list;
		actionUrl(p_url);	
	}
}
function btn_worklist_onclick(p_checkbox_obj, p_url){
	record_id_list = checkbox_value_to_list(p_checkbox_obj,",");
	if (!record_id_list){
		alert("Chưa có đối tượng nào được chọn");
	}else{
		arr_value = record_id_list.split(",");
		if (arr_value.length > 1){
			alert("Chỉ được chọn một đối tượng để thực hiện")
			return;
		}else{
			document.getElementById('hdn_object_id_list').value = record_id_list;
			actionUrl(p_url);	
		}
	}
}
//Ham btn_sent_onclick duoc goi khi NSD bam vao nut 'gui'
function btn_updateapprove_onclick(radio_obj,p_url){
	if(!test_date(document.getElementById('C_WORK_DATE').value)){
		alert('Ngày thực hiện không đúng định dạng ngày/tháng/năm');
		return false;
	}
	approveStatus = '';
	for(i = 0; i < radio_obj.length; i++){
		if(radio_obj[i].checked){
			approveStatus = radio_obj[i].value;
			break;
		}
	}
	if(approveStatus == ''){
		alert('Chưa chọn trạng thái phê duyệt');
		radio_obj[0].focus();
		return false;
	}
	if(document.getElementById('C_SUBMIT_ORDER_CONTENT').value == ''){
		alert('Phải xác định thông tin nội dung!');
		document.getElementById('C_SUBMIT_ORDER_CONTENT').focus();
		return false;
	}
	if($('#DUYET_CHUYEN_PHONG_BAN_2').attr('checked')) {
	   if(!verify(document.forms[0])){
	      return false;
	   }
		
	}
	alert($('#DUYET_CHUYEN_PHONG_BAN_2').attr('checked'));
	document.getElementsByTagName('form')[0].action = p_url;
	document.getElementsByTagName('form')[0].submit();
}