function record_handle(baseUrl, module, controller) {
    myrecord_handle = this;
    this.module = module;
    this.controller = controller;
    this.baseUrl = baseUrl;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
}

record_handle.prototype.loadIndex = function () {
    var self = this;
    $('.process').unbind('click');
    $('.process').click(function () {
        self.process();
    });

    $('.submitorder').unbind('click');
    $('.submitorder').click(function () {
        var url = self.urlPath + '/submitorder';
        self.submitorder(url);
    });

};

record_handle.prototype.process = function () {
    var url = this.urlPath + '/process';
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

record_handle.prototype.loadProcess = function () {
    var self = this;
    $('#div_handle').show();
    $('#div_tax').hide();
    $('#div_limit_date').hide();
    $('.normal_radiobutton').click(function () {
        var value = $(this).val();
        if(value=='CHUYEN_CAN_BO_XL'){
            $('#idea').val('Chuyển cán bộ chuyên môn tiếp tục thụ lý hồ sơ!');
            $('#div_handle').show();
            $('#div_tax').hide();
            $('#div_limit_date').show();
        }
        if(value=='CHUYEN_THUE'){
            $('#idea').val('Chuyển chi cục thuế!');
            $('#div_handle').hide();
            $('#div_tax').show();
            $('#div_limit_date').show();
        }
        if(value=='YEU_CAU_BO_SUNG'){
            $('#idea').val('Trả lại bộ phận một cửa yêu cầu bổ sung hồ sơ!');
            $('#div_handle').hide();
            $('#div_tax').hide();
            $('#div_limit_date').hide();
        }
        if(value=='TU_CHOI'){
            $('#idea').val('Từ chối, không nhận xử lý hồ sơ!');
            $('#div_handle').hide();
            $('#div_tax').hide();
            $('#div_limit_date').hide();
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

        if(chk_process_type=='CHUYEN_THUE'){
            var chk_tax = '';
            $('form#frmSubmitorder').find('input[type="radio"][name="chk_tax"]:checked').each(function () {
                chk_tax = $(this).val();
            });
            if(chk_tax==''){
                alert('Bạn chưa chọn cán bộ thuế xử lý!');
                return false;
            }
        }

        if(chk_process_type=='CHUYEN_CAN_BO_XL'){
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

record_handle.prototype.submitorder = function (url) {
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

record_handle.prototype.loadSubmitorderEven = function (action) {
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

record_handle.prototype.loadBackongate = function () {
    var self = this;
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
            alert('Bạn phải nhập nội dung ý kiến xử lý!');
            $("form#frmSubmitorder #idea").focus();
            return false;
        }
        actionUrl('');
    });

    $('#btnback').click(function () {
        var url = self.urlPath + '/result';
        actionUrl(url);
    })
};