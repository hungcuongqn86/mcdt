/*
 * Creater: Truongdv
 * Date:24/11/2015
 * Idea: Quan ly tai khoan
 */
function Admin_Account (baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;//Biên List lưu tên module
    myClassList = this;
    this.loadding = false;
    this.frmIndex = $('form#frmaccount');
    this.__status = 0;
}

$.extend(Admin_Account.prototype, {
    loadIndex: function() {
        var self = this;
        lddadepicker();
        $('.cldelete').unbind('click').click(function () {
            self.deleteAccount()
        })

        createModalDialog("div.modal_info", function () {
            $("div.modal_info").dialog({
                autoOpen: false,
                title: 'THÔNG TIN TÀI KHOẢN',
                modal: true,
                resizable: true,
                width: document.documentElement.clientWidth * 0.9,
                height: document.documentElement.clientHeight * 0.8,
                autoResize: false,
                dialogClass: 'dialog-class',
                draggable: true,
                overlay: {
                    opacity: 0.5,
                    background: 'blue'
                },
                buttons: [
                    {
                        text: "Duyệt",
                        click: function () {
                            self.saveapprove()
                        }
                    },
                    {
                        text: "Quay lại",
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        })
        $('[name="status"]', this.frmIndex).change(function(){
            self.handleSwitch($(this).val())
            self.reLoadData();
        })
        self.handleSwitch($('[name="namemodeswitch"]:checked').val())
    },
    handleSwitch: function(value) {
        // $('.blc-right input[type="button"]').hide().css({pointerEvents: 'none'});
        var title = 'DANH SÁCH TÀI KHOẢN CHỜ DUYỆT'
        switch (value) {
            case 'CHO_DUYET':
                title = 'DANH SÁCH TÀI KHOẢN CHỜ DUYỆT'
                break;
            case 'DA_DUYET':
                title = 'DANH SÁCH TÀI KHOẢN ĐÃ DUYỆT'
                break;
        }
        $('.searh-fixed .normal_title').html(title)
    },
    eventTable: function() {
        var self = this
        $('.ic_approve').click(function() {
            self.approve($(this).closest('tr').find('input[type="checkbox"][name="chk_item_id"]').val())
        })

        $('.ic_delete').click(function() {
            select_row($(this).closest('td'))
            self.deleteAccount();
        })

        $('.ic_edit').click(function() {
            self.edit($(this).closest('tr').find('input[type="checkbox"][name="chk_item_id"]').val())
        })
        
    },

    edit: function(id) {
        var self = this,
            url = this.urlPath + '/edit'

        $('.modal_info').dialog(
                    'option',
                    'buttons',
                        [{
                            text: 'Cập nhật',
                            click: function() {
                                self.save();
                            }
                        },
                        {
                            text: 'Đóng',
                            click: function() {
                                $(this).dialog('close');
                            }
                        }]
                );
        $.ajax({
            url: url,
            type: "POST",
            data: {id: id},
            success: function (html) {
                $('.modal_info').html(html).dialog('open')
                lddadepicker();
                hideloadpage();
            },
            beforeSend: showloadpage()
        })
    },

    approve: function(id) {
        var self = this,
            url = this.urlPath + '/approve'

        $('.modal_info').dialog(
                    'option',
                    'buttons',
                        [{
                            text: 'Cập nhật',
                            click: function() {
                                self.saveapprove();
                            }
                        },
                        {
                            text: 'Đóng',
                            click: function() {
                                $(this).dialog('close');
                            }
                        }]
                );
        $.ajax({
            url: url,
            type: "POST",
            data: {id: id},
            success: function (html) {
                $('.modal_info').html(html).dialog('open')
                hideloadpage();
            },
            beforeSend: showloadpage()
        })
    },
    saveapprove: function(){
        var self = this,
            url = this.urlPath + '/saveapprove'
        if (verify(document.forms['frm_approve'])) {
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: $('#frm_approve').serialize(),
                success: function (data) {
                    hideloadpage();
                    jAlert(data.msg, 'Thông báo')
                    if (data.error == false) {
                        $('.modal_info').dialog('close')
                        self.reLoadData();
                    }
                },
                beforeSend: showloadpage()
            })
        }
    },

    save: function(){
        var self = this,
            url = this.urlPath + '/save'
        if (verify(document.forms['frm_approve'])) {
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: $('#frm_approve').serialize(),
                success: function (data) {
                    hideloadpage();
                    jAlert(data.msg, 'Thông báo')
                    if (data.error == false) {
                        $('.modal_info').dialog('close')
                        self.reLoadData();
                    }
                },
                beforeSend: showloadpage()
            })
        }
    }
})

Admin_Account.prototype.ldmod = function (callback) {
    var self = this;
    if (this.loadding) return false;
    if (typeof (callback) != 'function')  callback = function () {
    };
    this.loadding = true;
    var url = this.urlPath + '/loadlist';
    var oForm = this.frmIndex;
    var data = $(oForm).serialize();
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: data,
        success: function (arrResult) {
            $('table#table-data tbody tr').not(':eq(0)').remove();
            if (arrResult)
                $('table#table-data tbody tr.header').after(self.genTable(arrResult));
            addRowEmpty($('table#table-data tbody'), 15);
            self.eventTable()
            hideloadpage();
            callback(arrResult, 'C_TOTAL_RECORD');
            $('div#IndexFrm').show();
            $('div#UpdateFrm').hide();
            self.loadding = false;
            scrollTop(1);
        },
        beforeSend: showloadpage()
    })
}

Admin_Account.prototype.genTable = function (arrValue, module) {
    var cov = arrValue.length;
    var shtml = '', options ='';
    for (var j = 0; j < cov; j++) {
        shtml += '<tr>';
        shtml += '<td class="normal_label" align="center"><input type="checkbox" onclick="selectrow(this)"  value="' + arrValue[j]['ID'] + '" id="chk_item_id" name="chk_item_id"></td>';
        shtml += '<td align = "left" class="normal_label"  onclick="select_row(this)">' + arrValue[j]['dRegisterDate'] + '</td>';
        shtml += '<td align = "left" class="normal_label"  onclick="select_row(this)">' + arrValue[j]['sEmail'] + '</td>';
        shtml += '<td align = "left" class="normal_label"  onclick="select_row(this)">' + arrValue[j]['sFullName'] + '</td>';
        shtml += '<td align = "left" class="normal_label"  onclick="select_row(this)">' + arrValue[j]['sMobile'] + '</td>';
        shtml += '<td align = "left" class="normal_label"  onclick="select_row(this)">' + arrValue[j]['sAddress'] + '</td>';
        if (arrValue[j]['sStatus'] == 0) {
            options = '<span class="ic_approve" title="Duyệt">Duyệt</span><span class="ic_delete" title="Xóa">Xóa</span>';
        } else {
            options = '<span class="ic_edit" title="Sửa">Sửa</span><span class="ic_delete" title="Xóa">Xóa</span>';
        }
        shtml += '<td align = "center" class="normal_label"  onclick="select_row(this)">' + options + '</td>';
        shtml += '</tr>';
    }
    
    return shtml;
}

Admin_Account.prototype.deleteAccount = function () {
    var url = this.urlPath + '/delete';
    var self = this, listId = '', count = 0;
    $('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        listId += $(this).val() + ',';
        count++;
    })
    if (count === 0) {
        jAlert('Bạn chưa chọn một đối tượng nào để xóa!', 'Thông báo');
        return false;
    }
    listId = listId.substr(0, listId.length - 1);
    var data = {
        listId: listId
    };
    jConfirm('Bạn có chắc chắn muốn xóa?', 'Thông báo', function (r) {
        if (r) {
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'JSON',
                success: function (arrFields) {
                    if (arrFields['RESULT'] > 0) {
                        jAlert('Xóa thành công', 'Thông báo');
                        self.reLoadData();
                    } else return false;
                }
            });
            return !history.pushState;
        }
    });
}