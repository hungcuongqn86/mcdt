function record_wapprove(baseUrl, module, controller) {
    myrecord_wapprove = this;
    this.module = module;
    this.controller = controller;
    this.baseUrl = baseUrl;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
}
// Load su kien tren man hinh index
record_wapprove.prototype.loadIndex = function () {
    var self = this;
    $('.btn_approve').unbind('click');
    $('.btn_approve').click(function () {
        self.approve();
    })
};

// Load su kien tren man hinh index
record_wapprove.prototype.loadApproveEven = function () {
    var self = this;
    $('.normal_radiobutton').click(function () {
        var value = $(this).val();
        if(value=='LD_DONVI_CAPPHEP'){
            $('#idea').val('Duyệt, chuyển một cửa trả kết quả công dân!');
        }
        if(value=='DUYET_CHUYEN_LIEN_THONG'){
            $('#idea').val('Duyệt, chuyển liên thông một cửa huyện!');
        }
        if(value=='LD_DONVI_TRALAI'){
            $('#idea').val('Trả lại một cửa, bổ sung hồ sơ!');
        }
        if(value=='LD_DONVI_TUCHOI'){
            $('#idea').val('Từ chối, trả hồ sơ cho công dân!');
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
            alert('Bạn phải nhập nội dung ý kiến duyệt!');
            $("form#frmSubmitorder #idea").focus();
            return false;
        }
        actionUrl('');
    });
    $('#btnback').click(function () {
        var url = self.urlPath + '/index';
        actionUrl(url);
    })
};

record_wapprove.prototype.approve = function () {
    var url = this.urlPath + '/approve';
    var record_id_list = '', count = 0;
    $('form#formreceive').find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        if (record_id_list == "") record_id_list = $(this).val();
        else if (record_id_list != "") record_id_list = record_id_list + ',' + $(this).val();
        count++;
    });
    if (count === 0) {
        alert('Bạn chưa chọn hồ sơ cần duyệt!', 'Thông báo');
        return false;
    }
    document.getElementById('hdn_object_id_list').value = record_id_list;
    actionUrl(url);
};

function ResetSearch(){
    document.getElementById('hdn_current_page').value = "1";
}

function checkvalue(){
    if(document.getElementById('txtfullTextSearch').value != ""){
        actionUrl('');
    }
}