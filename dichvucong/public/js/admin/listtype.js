/*
 * Creater: Truongdv
 * Date:14/12/2012
 * Idea: Class dung chung
 */
function Admin_Listtype(baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
    this.frmIndex = $('form#frmAllListType')
}
//Phương thức load dữ liệu
Admin_Listtype.prototype.ldmod = function (callback) {
    var self = this, 
        url = this.urlPath + '/record', 
        dirxml = $('#filexml').val();
    if (self.loadding == 1) return;
    if (typeof(xmlJS_Listtype) === 'undefined') {
        xmlJS_Listtype = new libXml({dirxml: dirxml});
    }
    var data = $(self.frmIndex).serialize();
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        cache: true,
        data: data,
        success: function (arrResult) {
            xmlJS_Listtype.exportTable({
                result: arrResult,
                objupdate: $('div#table-container'),
                fulltextsearch: $('#txtSearch').val(),
                form: $('form#frmAllListType')
            })
            self.loadding = 0;
            hideloadpage()
            callback(arrResult, 'C_TOTAL_RECORD');
            $('div#IndexFrm').show();
            $('div#UpdateFrm').hide();
            scrollTop(1);
        },
        beforeSend: function() {
            self.loadding = 1;
            showloadpage()  
        } 
    });

}
/**
 * Create: Truongdv
 * Date: 17/12/2012
 * Function: Them moi
 */
Admin_Listtype.prototype.add = function () {
    var self = this,
        url = this.urlPath + '/add';
    $.ajax({
        url: url,
        type: "POST",
        data: {hdn_order: $('#hdn_order').val()},
        cache: true,
        success: function (string) {
            var currentState = {content: string};
            $('div#IndexFrm').hide();
            $('div#UpdateFrm').html(string).show();
            if (history.pushState) history.pushState(currentState, "", url);
            self.loadUpdatefrm();
            hideloadpage()
        },
        beforeSend: showloadpage()
    });
    return !history.pushState;
}
/**
 * Create: Truongdv
 * Date: 17/12/2012
 * Function: Luu ho so
 */
Admin_Listtype.prototype.save = function (act_event) {
    var self = this,
        url = this.urlPath + '/save',
        urlUpdate = this.urlPath + '/add';
    if (verify(document.forms['frmAddListType'])) {
        $.ajax({
            url: url,
            type: "POST",
            data: $('#frmAddListType').serialize(),
            cache: true,
            success: function (string) {;
                if (act_event === 'next') {
                    var hdn_order = parseInt($('#hdn_order').val()), C_ORDER = parseInt($('#C_ORDER').val());
                    if (C_ORDER >= hdn_order) {
                        hdn_order = hdn_order + 1;
                        $('#hdn_order').val(hdn_order);
                    }
                    var datanext = {hdn_order: hdn_order};
                    $("div#UpdateFrm").load(urlUpdate, datanext, function () {
                        self.loadUpdatefrm();
                    });
                } else {
                    self.reLoadData();
                    $('div#IndexFrm').show();
                    $('div#UpdateFrm').hide();
                }
                hideloadpage()
            },
            beforeSend: function() {
                showloadpage()
            },
            error: function () {
                jAlert('Có lỗi xảy ra', 'Error Dialog');
            }
        });
    }
}
//Phương thức sửa
Admin_Listtype.prototype.edit = function () {
    var self = this, url = this.urlPath + '/edit', PkListType = '';
    var count = 0;
    $('input[name="chk_item_id"]:checked', self.frmIndex).each(function () {
        PkListType = $(this).val();
        count++;
    });
    if (count == 0) {
        return jWarning('Bạn chưa chọn một đối tượng nào để sửa!', 'Edit Dialog');
    }
    if (count > 1) {
        return jWarning('Bạn chỉ được chọn một đối tượng để sửa!', 'Edit Dialog');
    }
    $.ajax({
        url: url,
        type: "POST",
        data: {PkListType: PkListType},
        cache: true,
        success: function (string) {
            $('div#IndexFrm').hide();
            $('div#UpdateFrm').html(string).show();
            var currentState = {html: string};
            if (history.pushState) history.pushState(currentState, currentState, url);
            self.loadUpdatefrm();
            hideloadpage()
        },
        beforeSend: showloadpage()
    });
    return !history.pushState;
}

//Phương thức view
Admin_Listtype.prototype.view = function () {
    alert('view');
}
//Phương thức xóa
Admin_Listtype.prototype.deleteRecord = function () {
    var self = this,
        url = this.urlPath + '/delete', 
        listId = '';
    $('form#frmAllListType input[type=checkbox][name="chk_item_id"]:checked').each(function () {
        listId += $(this).val() + ',';
    });
    listId = listId.substr(0, listId.length - 1);
    if (listId == '') {
        return jWarning('Bạn chưa chọn đối tượng nào', 'Thông báo');
    }
    jConfirm('Bạn có chắc chắn muốn xóa những hồ sơ này?', 'Xóa hồ sơ', function (r) {
        if (r) {
            $.ajax({
                url: url,
                type: "POST",
                data: {hdn_object_id_list: listId},
                cache: true,
                success: function (string) {
                    self.reLoadData();
                    hideloadpage()
                },
                beforeSend: showloadpage()
            });
        }
    });
}

Admin_Listtype.prototype.exportcachefile = function () {
    $.ajax({
            url: this.urlPath + '/exportcachefile',
            type: 'GET',
            success: function () {
                try{
                    jAlert('Đã xuất file thành công', 'Thông báo');
                    
                } catch(err){
                    alert('Đã xuất file thành công')
                    $('#popup_overlay').hide()
                }
                hideloadpage();
            },
            beforeSend: showloadpage()
        });
    return false;
}
Admin_Listtype.prototype.loadIndexfrm = function () {
    var self = this;
    //Thêm
    $('.cladd').unbind('click').click(function () {
        self.add();
    })
    //Sửa
    $('.cledit').unbind('click').click(function () {
        self.edit();
    })
    //Xóa
    $('.cldelete').unbind('click').click(function () {
        self.deleteRecord();
    })
    //Xuất xml
    $('.clxml').unbind('click').click(function () {
        self.exportcachefile();
    })
}

Admin_Listtype.prototype.loadUpdatefrm = function () {
    var self = this;
    $("#sCode").focus();
    $('.clsave').unbind('click').click(function () {
        self.save($(this).attr('act_event'));
    })
    $('.clback').unbind('click').click(function () {
        $('div#IndexFrm').show();
        $('div#UpdateFrm').hide();
        self.reLoadData();
        scrollTop(1);
    })
}


function chk_show_hide_auto_generate_object_code() {
    if ($('#CHK_AUTO_GENERATE_OBJECT_CODE:checked').val() == undefined) {
        $('#CHK_AUTO_GENERATE_OBJECT_CODE').attr("checked", true);
        $('#ID_AUTO_GENERATE_OBJECT_CODE').show();
        if ($('#TXT_AUTO_GENERATE_OBJECT_CODE').val() == "") {
            $('#TXT_AUTO_GENERATE_OBJECT_CODE').attr("value", $("#sCode").val());
        }
    } else {
        $('#CHK_AUTO_GENERATE_OBJECT_CODE').attr("checked", false);
        $('#ID_AUTO_GENERATE_OBJECT_CODE').hide();
    }
}   