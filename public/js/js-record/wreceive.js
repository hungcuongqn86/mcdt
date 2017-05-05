function record_wreceive(baseUrl, module, controller) {
    myrecord_receive = this;
    this.module = module;
    this.controller = controller;
    this.baseUrl = baseUrl;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
}
// Load su kien tren man hinh index
record_wreceive.prototype.loadIndex = function () {
    var self = this;
    $('.add').unbind('click');
    $('.add').click(function () {
        actionUrl('../wreceive/add');
    });

    $('.edit').unbind('click');
    $('.edit').click(function () {
        btn_update_onclick(document.getElementsByName('chk_item_id'),'../wreceive/edit');
    });

    $('.delete').unbind('click');
    $('.delete').click(function () {
        btn_delete_onclick(document.getElementsByName('chk_item_id'),document.getElementById('hdn_object_id_list'),'../wreceive/delete',self.urlPath,'HO_SO','T_eCS_RECORD');
    });

    $('.submitorder').unbind('click');
    $('.submitorder').click(function () {
        var url = self.urlPath + '/submitorder';
        self.submitorder(url);
    });

    $('.forward').unbind('click');
    $('.forward').click(function () {
        var url = self.urlPath + '/forward';
        self.forward(url);
    })

    $('.print_receive').unbind('click');
    $('.print_receive').click(function () {
        var tempcode = $(this).attr('tempcode');
        self.printOtherReceipt(tempcode);
    })
};

// Load su kien tren man hinh index
record_wreceive.prototype.loadAddition = function () {
    var self = this;

    $('.edit').unbind('click');
    $('.edit').click(function () {
        btn_update_onclick(document.getElementsByName('chk_item_id'),'../wreceive/update');
    });

    $('.submitorder').unbind('click');
    $('.submitorder').click(function () {
        var url = self.urlPath + '/submitorderadd';
        self.submitorder(url);
    });

    $('.forward').unbind('click');
    $('.forward').click(function () {
        var url = self.urlPath + '/forwardadd';
        self.forward(url);
    })
};


record_wreceive.prototype.loadSubmitorderEven = function (action) {
    var self = this;
    $('#btnSubmit').click(function () {
        var leader_id = '';
        $('form#frmSubmitorder').find('input[type="radio"][name="chk_leader"]:checked').each(function () {
            leader_id = $(this).val();
        });
        if(leader_id==''){
            alert('Bạn chưa chọn lãnh đạo duyệt!');
            return false;
        }
        if($("form#frmSubmitorder #idea").val()==''){
            alert('Bạn phải nhập nội dung ý kiến trình duyệt!');
            $("form#frmSubmitorder #idea").focus();
            return false;
        }
        actionUrl('');
    });
    $('#btnback').click(function () {
        var url = self.urlPath + '/' + action;
        actionUrl(url);
    })
};

// Load su kien tren man hinh index
record_wreceive.prototype.loadForwardEven = function (action) {
    var self = this;
    $('.normal_radiobutton').click(function () {
        var value = $(this).val();
        if(value=='CHUYEN_LIEN_THONG'){
            $('#idea').val('Chuyển liên thông một cửa huyện!');
        }
        if(value=='CHUYEN_TRA_KQ'){
            $('#idea').val('Chuyển chờ trả kết quả hồ sơ!');
        }
    });


    $('#btnSubmit').click(function () {
        var chk_process_type = '';
        $('form#frmSubmitorder').find('input[type="radio"][name="chk_process_type"]:checked').each(function () {
            chk_process_type = $(this).val();
        });
        if(chk_process_type==''){
            alert('Bạn chưa chọn hình thức xử lý!');
            return false;
        }
        if($("form#frmSubmitorder #idea").val()==''){
            alert('Bạn phải nhập nội dung ý kiến trình duyệt!');
            $("form#frmSubmitorder #idea").focus();
            return false;
        }
        actionUrl('');
    });
    $('#btnback').click(function () {
        var url = self.urlPath + '/' + action;
        actionUrl(url);
    })
};

record_wreceive.prototype.submitorder = function (url) {
    var record_id_list = '', count = 0;
    $('form#formreceive').find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        if (record_id_list == "") record_id_list = $(this).val();
        else if (record_id_list != "") record_id_list = record_id_list + ',' + $(this).val();
        count++;
    });
    if (count === 0) {
        alert('Bạn chưa chọn hồ sơ để trình duyệt!', 'Thông báo');
        return false;
    }
    document.getElementById('hdn_object_id_list').value = record_id_list;
    actionUrl(url);
};

record_wreceive.prototype.forward = function (url) {
    var record_id_list = '', count = 0;
    $('form#formreceive').find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        if (record_id_list == "") record_id_list = $(this).val();
        else if (record_id_list != "") record_id_list = record_id_list + ',' + $(this).val();
        count++;
    });
    if (count === 0) {
        alert('Bạn chưa chọn hồ sơ để chuyển xử lý!', 'Thông báo');
        return false;
    }
    document.getElementById('hdn_object_id_list').value = record_id_list;
    actionUrl(url);
};

record_wreceive.prototype.printReceipt = function (tempname) {
    var url = this.urlPath + '/printreceipt';
    var record_id_list = '', count = 0;
    $('form#formreceive').find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        if (record_id_list == "") record_id_list = $(this).val();
        else if (record_id_list != "") record_id_list = record_id_list + ',' + $(this).val();
        count++;
    });
    if (count === 0) {
        alert('Bạn chưa chọn hồ sơ để in!', 'Thông báo');
        return false;
    }
    if (count > 1) {
        alert('Bạn chỉ được chọn 1 hồ sơ để in!', 'Thông báo');
        return false;
    }
    var arrdata = {
        hdn_object_id: record_id_list,
        fileName:tempname
    };
    $.ajax({
        url: url,
        type: "POST",
        data: arrdata,
        success: function (string) {
            window.open(string, '_blank');
        }
    });
};

record_wreceive.prototype.printOtherReceipt = function (tempname) {
    var url = this.urlPath + '/printotherreceipt';
    var record_id_list = '', count = 0;
    $('form#formreceive').find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        if (record_id_list == "") record_id_list = $(this).val();
        else if (record_id_list != "") record_id_list = record_id_list + ',' + $(this).val();
        count++;
    });
    if (count === 0) {
        alert('Bạn chưa chọn hồ sơ để in!', 'Thông báo');
        return false;
    }
    if (count > 1) {
        alert('Bạn chỉ được chọn 1 hồ sơ để in!', 'Thông báo');
        return false;
    }
    var arrdata = {
        hdn_object_id: record_id_list,
        fileName:tempname
    };
    $.ajax({
        url: url,
        type: "POST",
        data: arrdata,
        success: function (string) {
            window.open(string, '_blank');
        }
    });
};


function ResetSearch(){
    document.getElementById('hdn_current_page').value = "1";
}

function checkvalue(){
    if(document.getElementById('txtfullTextSearch').value != ""){
        actionUrl('');
    }
}





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