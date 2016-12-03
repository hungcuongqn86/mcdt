/*
 * Creater: ##
 * Date:##
 * Idea: ##
 */
function Admin_Log(baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
    this.loadding = false;
}

Admin_Log.prototype.loadIndex = function () {
    var self = this;
    lddadepicker();
    $('.cldelete').unbind('click').click(function () {
        self.logDelete()
    })
}

Admin_Log.prototype.loadList = function (callback) {
    var self = this;
    if (this.loadding) return false;
    if (typeof (callback) != 'function')  callback = function () {
    };
    this.loadding = true;
    var url = this.urlPath + '/loadlist';
    var oForm = $('form#frmLog');
    var data = $(oForm).serialize();
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: data,
        success: function (arrResult) {
            $('table#table-data tbody tr').not(':eq(0)').remove();
            if (arrResult)
                $('table#table-data tbody tr.header').after(self.genTable(arrResult, 'historyLogin'));
            addRowEmpty($('table#table-data tbody'), 15);
            hideloadpage();
            callback(arrResult, 'TOTAL_RECORD');
            $('div#IndexFrm').show();
            $('div#UpdateFrm').hide();
            self.loadding = false;
            scrollTop(1);
        },
        beforeSend: showloadpage()
    })
}

Admin_Log.prototype.genTable = function (arrValue, module) {
    var cov = arrValue.length;
    var shtml = '';
    if (module == 'historyLogin') {
        for (var j = 0; j < cov; j++) {
            shtml += '<tr>';
            shtml += '<td class="normal_label" align="center"><input type="checkbox" onclick="selectrow(this)"  value="' + arrValue[j]['ID'] + '" id="chk_item_id" name="chk_item_id"></td>';
            shtml += '<td align = "left" class="normal_label"  onclick="">' + arrValue[j]['sPositionName'] + '</td>';
            shtml += '<td align = "left" class="normal_label"  onclick="">' + arrValue[j]['Ip'] + '</td>';
            shtml += '<td align = "left" class="normal_label"  onclick="">' + arrValue[j]['domain'] + '</td>';
            shtml += '<td align = "left" class="normal_label"  onclick="">' + arrValue[j]['dTime'] + '</td>';
            shtml += '</tr>';
        }
    }
    return shtml;
}

Admin_Log.prototype.logDelete = function () {
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
                        return false;
                    }
                    self.reLoadData();
                }
            });
            return !history.pushState;
        }
    });
}