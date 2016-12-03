var Account_Profile = function (options) {

    this.module = options.module;
    this.controller = options.controller;
    this.urlPath = baseUrl + '/' + this.module + '/' + this.controller;
}

$.extend(Account_Profile.prototype, {
    initialize: function () {
        var self = this;
        // menu
        $('.tabmenu').click(function () {
            $('form.item').hide();
            $('.tabmenu').removeClass('active');
            $(this).addClass('active');
            var menu = $(this).attr('for');
            $('#' + menu).show();
        })
        // event
        $("#saveinfo").unbind('click').click(function () {
            if ($('#infor')[0].checkValidity()) {
                self.saveinfo();
                return false;
            }
        });
        $("#updatepass").unbind('click').click(function () {
            if ($('#changepass')[0].checkValidity()) {
                if ($('#passNew').val() != $('#repassNew').val()) {
                    document.getElementById('repassNew').setCustomValidity('Nhập lại mật khẩu chưa chính xác!');
                    return false;
                } else
                    document.getElementById('repassNew').setCustomValidity('');
                self.savechangepass();
                return false;
            }
        });
        $('#Birthday').datepicker({
            changeMonth: true,
            gotoCurrent: true,
            minDate: '-65Y',
			maxDate: '-18Y',
            changeYear: true
        });
        // default
        $('li[for="infor"]').trigger('click');
    },
    saveinfo: function () {
        var self = this,
            url = this.urlPath + '/saveinfo',
            data = $('form#infor').serialize();
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            // dataType: 'json',
            success: function (rsp) {
                if (rsp == 1) {
                    jAlert('Cập nhật thông tin thành công!', 'Thông báo');
                } else {
                    jAlert('Cập nhật bị lỗi!', 'Thông báo');
                }
                hideloadpage();
            },
            beforeSend: function (xhr) {
                showloadpage();
            },
            errors: function () {
                hideloadpage();
            }
        });
        return !history.pushState;
    },
    savechangepass: function () {
        var self = this,
            url = this.urlPath + '/savechangepass',
            data = {
                passOld: $.base64.encode($('#passOld').val()),
                passNew: $.base64.encode($('#passNew').val())
            };
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function (rsp) {
                if (rsp == 1) {
                    jAlert('Thay đổi mật khẩu thành công!', 'Thông báo');
                    document.getElementById('changepass').reset();
                } else {
                    jAlert('Mật khẩu không chính xác', 'Thông báo');
                }

                hideloadpage();
            },
            beforeSend: function (xhr) {
                showloadpage();
            },
            errors: function () {
                hideloadpage();
            }
        });
        return false;
    }

});