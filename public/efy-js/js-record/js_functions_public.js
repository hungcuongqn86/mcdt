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
					alert('Bạn phải nhập ngày hẹn trả hồ sơ (bạn có thể ch�?n vào ngày tiếp nhận hệ thống sẽ trả lại ngày hẹn trả kết quả).');			 
					document.forms[0].txt_appointed_date.focus();
					return;
				}else{
						if(!isdate(v_appointed_date)){
							alert('Ngày hẹn trả kết quả không hoepj lệ (bạn có thể ch�?n vào ngày tiếp nhận hệ thống sẽ trả lại ngày hẹn trả kết quả).');	
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
function petition_period_num_change_tax_date(){
	if(!isnum(document.forms[0].so_ngay_hen.value)){
		alert('So ngay phai la so!');
		document.forms[0].so_ngay_hen.focus();
		return;
	}
	var count = document.forms[0].so_ngay_hen.value;
	var p_year = document.forms[0].hdn_current_year.value;
	var v_list_day_off_of_year = "+/30/04,+/01/05,+/02/09,-/30/12,-/01/01,-/02/01,-/03/01,-/10/03".split(",");
	var v_list_day = "2,3,4,5,6";
	var v_list_luner_date="";
	var v_increase_and_decrease_day = parseInt(1);
	var v_date,v_temp_date;
	var v_input_date = document.forms[0].C_RECEIVED_DATE.value;
	var v_next_date = "";
	//alert('obj_date_input');return;
	if (!isdate(document.forms[0].C_RECEIVED_DATE.value)){
		return;
	}else{	
		var arr_input_date = v_input_date.split("/");
		v_input_date = arr_input_date[0]*1 + "/" + arr_input_date[1]*1 + "/" + arr_input_date[2]*1; 	
	}
	for (var i=0;i<v_list_day_off_of_year.length;i++){
		v_date = v_list_day_off_of_year[i].split("/");
		if (v_date[0]=="-"){
			v_list_luner_date = list_append(v_list_luner_date,v_date[1]+"/" + v_date[2] + "/" + p_year,",");
		}else{
			v_temp_date = Solar2Lunar(v_date[1]+ "/" + v_date[2] + "/" + p_year);
			v_list_luner_date = list_append(v_list_luner_date,v_temp_date,",");
		}
	}
	var i=0;
	v_next_date = v_input_date;
	while ((i<count-v_increase_and_decrease_day)){ //Tinh du tong so ngay tru ngay duoc nghi ra
		if ((list_have_date(v_list_luner_date,Solar2Lunar(v_next_date),",")!=1)&&(Solar2DayofWeek(v_next_date)!=7)&&(Solar2DayofWeek(v_next_date)!=8)){
			i++;
			v_next_date = Next_Date(v_next_date);	
		}else{
			v_next_date = Next_Date(v_next_date);	
		}
	}
	//Neu den han duoc lay roi ma gap ngay khong tiep thi phai cho 
	while ((list_have_element(v_list_day,Solar2DayofWeek(v_next_date),",")<0)){
		v_next_date = Next_Date(v_next_date);		
	} 
	document.forms[0].ngay_hen_nop_thue.value = v_next_date;
}
function list_append(the_list,the_value,the_separator)
{	
	var list=the_list;
	if (list=="") list = the_value;
	else if (the_value !="") list = list+the_separator+the_value;
	return list;
}
function getNext_Date(elTarget) {
	if(isdate(elTarget.value)){
			var theDate,strSeparator,arr,day,month,year;
			strSeparator = "";
			theDate = elTarget.value;
			if (theDate.indexOf("/")!=-1) strSeparator = "/";
			if (theDate.indexOf("-")!=-1) strSeparator = "-";
			if (theDate.indexOf(".")!=-1) strSeparator = ".";
			if (strSeparator != "") {
			arr=theDate.split(strSeparator);
			day=new Number(arr[0])+1;
			month=new Number(arr[1]);
			year=new Number(arr[2]);
			if(day > 28) {
				if (((month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) && (day > 31))
				|| ((month == 4 || month == 6 || month == 9 || month == 11) && (day > 30))||(month == 2 && year % 4 !=0)||(month == 2 && year % 4 ==0 && day > 29)) 
				{
					day = 1;
					month = month+1;
				}
				if (month > 12 ){
					year = year +1;
					month = 1;
				}
				
			}	
			elTarget.value = day + strSeparator + month + strSeparator + year;
		}
   }		
}