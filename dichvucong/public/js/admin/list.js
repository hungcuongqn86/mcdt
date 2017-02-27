/*
 * Creater: Truongdv
 * Date:17/12/2012
 * Idea: Class xu ly danh muc doi tuong
 */
function Admin_List (baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;//Biên List lưu tên module
    myClassList = this;
    this.loadding = false;
}
Admin_List.prototype.ldmod = function () {
    this.reLoadData();
}
//Phương thức load dữ liệu
Admin_List.prototype.getRecord = function (callback) {
    var self = this,
        url = this.urlPath + '/record';
    if (this.loadding == 1) return;
    var dirxml = $('#filexml').val();
    if (typeof(xmlJS_List) === 'undefined') {
        xmlJS_List = new libXml({dirxml: dirxml});
    }
    _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), false)
    var data = $('form#frmlist').serialize();
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: data,
        success: function (arrData) {
            var arrResult = arrData.arrAllList, filexml = arrData.filexml;
            $('#hdn_xml_file').val(filexml);
            //Thay đổi lại cấu trúc màn hình danh sách khi file xml mô tả khác khác file mặc định.
            if (dirxml != filexml) {
                xmlJS_List = new libXml({dirxml: filexml});
                $('#filexml').val(filexml);
            }
            //fix width chosen
            xmlJS_List.exportTable({
                result: arrResult,
                objupdate: $('div#table-container'),
                form: $('form#frmlist')
            })
            self.loadding = 0;
            hideloadpage()
            callback(arrResult, 'TOTAL_RECORD');
            $('div#IndexFrm').show();
            $('div#UpdateFrm').hide();            
            var widthdiv = $('div.searh-fixed #listtype_type').css('width');
            $('div.searh-fixed .chzn-container').css('width', widthdiv);
            scrollTop(1);
        },
        beforeSend: function (){
            self.loadding = 1;
            showloadpage()
        }
   })  

}
/**
 * Create: Truongdv
 * Date: 17/12/2012
 * Function: Them moi
 */
Admin_List.prototype.add = function () {

    var self = this,
        url = this.urlPath + '/add';
    var data = {hdn_id_listtype: $('form#frmlist #listtype_type').val(), hdn_xml_file: $('#hdn_xml_file').val()};
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (string) {
            var currentState = {content: string};
            $('div#IndexFrm').hide();
            $('div#UpdateFrm').html(string).show();
            if (history.pushState) history.pushState(currentState, "", url);
            self.loadUpdatefrm();
            hideloadpage()
        },
        beforeSend: showloadpage()
    }) 
    return !history.pushState;
}
/**
 * Create: Truongdv
 * Date: 17/12/2012
 * Function: Luu ho so
 */
Admin_List.prototype.save = function (act_event) {
    var self = this,
        url = this.urlPath + '/save', 
        urlUpdate = this.urlPath + '/add';
    var p_hdn_tag_obj = document.getElementById('hdn_xml_tag_list'),
        p_hdn_value_obj = document.getElementById('hdn_xml_value_list');
    _save_xml_tag_and_value_list(document.forms['frmAddList'], p_hdn_tag_obj, p_hdn_value_obj, true);
    if (verify(document.forms['frmAddList'])) {
        //Hidden luu danh sach the va gia tri tuong ung trong xau XML
        document.getElementById('hdn_XmlTagValueList').value = p_hdn_tag_obj.value + '|{*^*}|' + p_hdn_value_obj.value;
        var data = $("form#frmAddList").serialize();
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            cache: true,
            success: function (string) {
                if (act_event === 'next') {
                    var datanext = {hdn_id_listtype: $('form#frmAddList #hdn_id_listtype').val()};
                    $("div#UpdateFrm").load(urlUpdate, datanext, function () {
                        self.loadUpdatefrm();
                    });
                } else {
                    self.ldmod();
                    $('div#IndexFrm').show();
                    $('div#UpdateFrm').hide();
                }
                hideloadpage();
            },
            beforeSend: showloadpage(),
            error: function () {
                jWarning('Có lỗi xảy ra', 'Error Dialog');
            }
        });
    }
}
//Phương thức sửa
Admin_List.prototype.edit = function () {
    var self = this, 
        url = this.urlPath + '/edit',
        hdn_list_id = '', count = 0;
    $('form#frmlist input[type=checkbox][name="chk_item_id"]:checked').each(function () {
        hdn_list_id = $(this).val();
        count++;
    });
    if (count == 0) {
        return jWarning('Bạn chưa chọn một đối tượng nào để sửa!', 'Thông báo');
    }
    if (count > 1) {
        return jWarning('Bạn chỉ được chọn một đối tượng để sửa!', 'Thông báo');
    }

    var data = {
        hdn_list_id: hdn_list_id,
        hdn_id_listtype: $('form#frmlist #listtype_type').val(),
        hdn_xml_file: $('#hdn_xml_file').val()
    };
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        cache: true,
        success: function (string) {
            $('div#IndexFrm').hide();
            $('div#UpdateFrm').html(string).show();
            var currentState = {html: string};
            if (history.pushState) history.pushState(currentState, currentState, url);
            self.loadUpdatefrm();  
            hideloadpage();
        },
        beforeSend: showloadpage()
    });
    return !history.pushState;
}

//Phương thức view
Admin_List.prototype.view = function () {
    alert('view');
}
//Phương thức xóa
Admin_List.prototype.deleteRecord = function () {
    var self = this, 
        url = this.urlPath + '/delete', 
        listId = '';
    $('form#frmlist input[type=checkbox][name="chk_item_id"]:checked').each(function () {
        listId += $(this).val() + ',';
    });
    listId = listId.substr(0, listId.length - 1);
    if (listId == '') {
        return jWarning('Bạn chưa chọn đối tượng nào', 'Thông báo');
    }
    jConfirm('Bạn có chắc chắn muốn xóa những hồ sơ này?', 'Thông báo', function (r) {
        if (r) {
            var arrdata = {hdn_object_id_list: listId, hdn_id_listtype: $('form#frmlist #listtype_type').val()};
            $.ajax({
                url: url,
                type: "POST",
                data: arrdata,
                cache: true,
                success: function (string) {
                    hideloadpage()
                    if (string == 'OK') {
                        $('form#frmlist input[type=checkbox][name="chk_item_id"]:checked').each(function () {
                            $(this).parent().parent().remove();
                        });
                    }
                },
                beforeSend: showloadpage()
            });
        }
    });
}
//Phương thức xóa
Admin_List.prototype.exportxml = function () {
    $.ajax({url: this.urlPath + '/xml', type: "POST",});
}
Admin_List.prototype.loadIndexfrm = function () {
    var self = this;
    //Thêm
    $('.cladd').click(function () {
        self.add();
    })
    //Sửa
    $('.cledit').click(function () {
        self.edit();
    })
    //Xóa
    $('.cldelete').click(function () {
        self.deleteRecord();
    })
}

Admin_List.prototype.loadUpdatefrm = function () {
    var self = this;
    $('#list_code_update').focus();
    $('.clsave').click(function () {
        self.save($(this).attr('act_event'));
    })
    $('.clback').click(function () {
        scrollTop(1);
        $('div#IndexFrm').show();
        $('div#UpdateFrm').hide();
        self.reLoadData()
    })
}