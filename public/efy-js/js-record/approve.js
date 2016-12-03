function record_approve(baseUrl, module, controller) {
    myrecord_wapprove = this;
    this.module = module;
    this.controller = controller;
    this.baseUrl = baseUrl;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
}

// Load su kien tren man hinh index
record_approve.prototype.loadIndex = function () {
    var self = this;
    $('.btn_approve').unbind('click');
    $('.btn_approve').click(function () {
        self.approve();
    })
};

// Load su kien tren man hinh index
record_approve.prototype.loadAssign = function () {
    var self = this;
    $('.btn_assign').unbind('click');
    $('.btn_assign').click(function () {
        self.assign();
    })
};

// Load su kien tren man hinh index
record_approve.prototype.loadApproveEven = function () {
    var self = this;

    $('#div_LeaderLv3').hide();
    $('#div_LeaderLv2').hide();
    $('#div_handle').hide();
    $('.normal_radiobutton').click(function () {
        var value = $(this).val();
        if(value=='TRINH_LD_DONVI'){
            $('#div_LeaderLv3').show();
            $('#div_LeaderLv2').hide();
            $('#div_handle').hide();
            $('#idea').val('Trình lãnh đạo phê duyệt');
        }
        if(value=='TRINH_LD_PHONG'){
            $('#div_LeaderLv3').hide();
            $('#div_LeaderLv2').show();
            $('#div_handle').hide();
            $('#idea').val('Trình lãnh đạo phê duyệt');
        }
        if(value=='LD_DONVI_CAPPHEP'){
            $('#div_LeaderLv3').hide();
            $('#div_LeaderLv2').hide();
            $('#div_handle').hide();
            $('#idea').val('Duyệt, chuyển một cửa trả kết quả công dân!');
        }
        if(value=='LD_PHONG_CAPPHEP'){
            $('#div_LeaderLv3').hide();
            $('#div_LeaderLv2').hide();
            $('#div_handle').hide();
            $('#idea').val('Duyệt, chuyển một cửa trả kết quả công dân!');
        }
        if(value=='DUYET_CHUYEN_PHONG_BAN'){
            $('#idea').val('Duyệt, chuyển cán bộ phụ trách thụ lý hồ sơ!');
        }
        if(value=='LD_PHONG_TRALAI'){
            $('#idea').val('Trả lại cán bộ phụ trách, thụ lý lại hồ sơ!');
        }
        if(value=='CHUYEN_CAN_BO_XL'){
            $('#div_LeaderLv3').hide();
            $('#div_LeaderLv2').hide();
            $('#div_handle').show();
            $('#idea').val('Chuyển cán bộ khác tiếp tục thụ lý hồ sơ!');
        }
        if(value=='LD_PHONG_TUCHOI'){
            $('#idea').val('Từ chối, trả hồ sơ cho công dân!');
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

record_approve.prototype.approve = function () {
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

record_approve.prototype.assign = function () {
    var url = this.urlPath + '/add';
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

record_approve.prototype.loadAddEven = function () {
    var self = this;
    $('#div_handle').show();
    $('.normal_radiobutton').click(function () {
        var value = $(this).val();
        if(value=='PHAN_CONG'){
            $('#idea').val('Chuyển cán bộ chuyên môn thụ lý hồ sơ!');
            $('#div_handle').show();
            $('#div_tax').hide();
            $('#div_limit_date').show();
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

        if(chk_process_type=='PHAN_CONG'){
            var chk_handle = '';
            $('form#frmSubmitorder').find('input[type="radio"][name="chk_handle"]:checked').each(function () {
                chk_handle = $(this).val();
            });
            if(chk_handle==''){
                alert('Bạn chưa chọn cán bộ thụ lý!');
                return false;
            }
        }

        if($("form#frmSubmitorder #idea").val()==''){
            alert('Bạn phải nhập nội dung ý kiến xử lý!');
            $("form#frmSubmitorder #idea").focus();
            return false;
        }
        actionUrl('');
    });

    $('#btnback').click(function () {
        var url = self.urlPath + '/index';
        actionUrl(url);
    })
    $( "#limit_date" ).datepicker({
        changeMonth: true,
        gotoCurrent: true,
        minDate: new Date(1945, 1 - 1, 1),
        changeYear: true
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