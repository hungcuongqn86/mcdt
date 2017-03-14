function record_search(baseUrl, module, controller, arrOwner, checkward) {
    myrecord_search = this;
    this.module = module;
    this.controller = controller;
    this.baseUrl = baseUrl;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
    this.arrOwner = arrOwner;
    this.checkward = checkward;
    this.arrCurrentPage = [{0: 15}, {0: 50}, {0: 100}];
    this.arrTitle = [{"C_CODE": "C_TONG_SO", "C_NAME": "TỔNG HỒ SƠ PHẢI XỬ LÝ"}
        , {"C_CODE": "C_HS_KY_TRUOC_CHUYEN_SANG", "C_NAME": "HỒ SƠ KỲ TRƯỚC CHUYỂN SANG"}
        , {"C_CODE": "C_HS_TRONG_KY", "C_NAME": "HỒ SƠ TIẾP NHẬN TRONG KỲ"}
        , {"C_CODE": "C_TONG_SO_DA_GIAI_QUYET", "C_NAME": "TỔNG HỒ SƠ ĐÃ XỬ LÝ"}
        , {"C_CODE": "C_HS_DA_GIAI_QUYET_DH", "C_NAME": "HỒ SƠ ĐÃ XỬ LÝ ĐÚNG HẠN"}
        , {"C_CODE": "C_HS_DA_GIAI_QUYET_QH", "C_NAME": "HỒ SƠ ĐÃ XỬ LÝ QUÁ HẠN"}
        , {"C_CODE": "C_TONG_SO_DANG_GIAI_QUYET", "C_NAME": "TỔNG HỒ SƠ ĐANG XỬ LÝ"}
        , {"C_CODE": "C_HS_DANG_GIAI_QUYET_DUNG_HAN", "C_NAME": "HỒ SƠ ĐANG XỬ LÝ CÒN HẠN"}
        , {"C_CODE": "C_HS_CHAM_CHUYEN_HUYEN", "C_NAME": "HỒ SƠ CHẬM CHUYỂN HUYỆN"}
        , {"C_CODE": "C_HS_DANG_GIAI_QUYET_QUA_HAN", "C_NAME": "HỒ SƠ ĐANG XỬ LÝ QUÁ HẠN"}];
}


record_search.prototype.loadIndex = function (sOwnerCode, checkward) {
    var self = this;

    $('#sOwnerCode').val(sOwnerCode);
    if (checkward == '0') {
        //$('#sOwnerCode').attr('disabled','disabled');
        $("#sOwnerCode option").not(":selected").attr("disabled", "disabled");
    }

    self.loadcate('');
    $('#hdn_current_page').val(1);
    $('#sOwnerCode').change(function () {
        $('#hdn_current_page').val(1);
        var catecode = $('#C_CATE').val();
        self.loadcate(catecode);
    });

    $('#C_CATE').change(function () {
        $('#hdn_current_page').val(1);
        var recordtype = $('#recordType').val();
        self.loadrecordtype(recordtype);
    });

    $('#recordType').change(function () {
        $('#hdn_current_page').val(1);
        self.loadfilter();
    });

    $('.seachbutton').unbind('click');
    $('.seachbutton').click(function () {
        $('#hdn_current_page').val(1);
        self.loadlistrecord();
    });

    $(function () {
        $("#dFromReceiveDate").datepicker({
            changeMonth: true,
            gotoCurrent: true,
            minDate: new Date(1945, 1 - 1, 1),
            changeYear: true
        });
    });
    $(function () {
        $("#dToReceiveDate").datepicker({
            changeMonth: true,
            gotoCurrent: true,
            minDate: new Date(1945, 1 - 1, 1),
            changeYear: true
        });
    });
};

record_search.prototype.loadcate = function (catecode) {
    var self = this;
    var ownercode = $('#sOwnerCode').val();
    var arrdata = {
        ownercode: ownercode
    };
    var url = this.urlPath + '/loadcate';
    $('#note-process').show();
    $.ajax({
        url: url,
        type: 'POST',
        data: arrdata,
        dataType: 'json',
        success: function (arrResult) {
            var htmlcate = '';
            $.each(arrResult, function (i, item) {
                if (item.C_CODE == catecode) {
                    htmlcate += '<option id="' + item.C_CODE + '" value="' + item.C_CODE + '" name="' + item.C_NAME + '" selected="selected">' + item.C_NAME + '</option>';
                } else {
                    htmlcate += '<option id="' + item.C_CODE + '" value="' + item.C_CODE + '" name="' + item.C_NAME + '">' + item.C_NAME + '</option>';
                }
            });
            $('#C_CATE').html(htmlcate);
            self.loadrecordtype();
        }
    });
};


record_search.prototype.loadrecordtype = function (recordtype) {
    var self = this;
    var catecode = $('#C_CATE').val();
    var ownercode = $('#sOwnerCode').val();
    var arrdata = {
        ownercode: ownercode,
        catecode: catecode
    };
    var url = this.urlPath + '/loadrecordtype';
    $('#note-process').show();
    $.ajax({
        url: url,
        type: 'POST',
        data: arrdata,
        dataType: 'json',
        success: function (arrResult) {
            var htmlrecordtype = '';
            $.each(arrResult, function (i, item) {
                if (item.PK_RECORDTYPE == recordtype) {
                    htmlrecordtype += '<option id="' + item.C_CODE + '" value="' + item.PK_RECORDTYPE + '" name="' + item.C_NAME + '" selected="selected">' + item.C_NAME + '</option>';
                } else {
                    htmlrecordtype += '<option id="' + item.C_CODE + '" value="' + item.PK_RECORDTYPE + '" name="' + item.C_NAME + '">' + item.C_NAME + '</option>';
                }
            });
            $('#recordType').html(htmlrecordtype);
            self.loadfilter();
        }
    });
};

record_search.prototype.loadfilter = function () {
    var self = this;
    var recordtype = $('#recordType').val();
    var ownercode = $('#sOwnerCode').val();
    var arrdata = {
        recordtype: recordtype,
        ownercode: ownercode
    };
    var url = this.urlPath + '/loadfilter';
    $('#note-process').show();
    $.ajax({
        url: url,
        type: 'POST',
        data: arrdata,
        dataType: 'html',
        success: function (stringrt) {
            $('#divfilter').html(stringrt);
            self.loadlistrecord();
        }
    });
};

record_search.prototype.loadlistrecord = function () {
    var self = this;
    var oForm = $('#indexform');
    var numberrow = $('#indexform #hdn_record_number_page').val();
    var page = $('#indexform #hdn_current_page').val();
    _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
    var data = $(oForm).serialize();
    var url = this.urlPath + '/loadlistrecord';
    $('#note-process').show();
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (arrResult) {
            console.log(111);
            $('#recordlist').html(arrResult[2]);
            if (arrResult[0] > 0) {
                var total = arrResult[1];
                self.loadpageindexinfo(arrResult[0], numberrow, page, total);
            } else {
                $('#ListInfo').html('');
            }
            $('#note-process').hide();
        }
    });
};

record_search.prototype.loadpageindexinfo = function (length, numberrow, page, total) {
    var self = this;
    var shtml = '<div style="display: inline-block; width: 30%;text-align: left;">Danh sách có ' + length + '/' + total + ' hồ sơ</div>';
    shtml += '<div style="display: inline-block; width: 30%;text-align: center;" id="paging-content-modal">';
    var totalpage = Math.abs(total / numberrow);
    if (Math.abs(total % numberrow) > 0) {
        totalpage = totalpage + 1;
    }
    var strclass = 'class="pg"';
    if (page > 1) {
        shtml += '<a onclick="obj_record_search.gotopage(' + (parseInt(page) - 1) + ')" ' + strclass + '>Trước</a>';
    }
    for (var i = 1; i <= totalpage; i++) {
        strclass = 'class="pg"';
        if (i == page) {
            strclass = 'class="pg current"';
        }
        shtml += '<a onclick="obj_record_search.gotopage(' + i + ')" ' + strclass + '>' + i + '</a>';
    }
    if (totalpage > page + 1) {
        shtml += '<a onclick="obj_record_search.gotopage(' + (parseInt(page) + 1) + ')" ' + strclass + '>Tiếp</a>';
    }
    shtml += '</div>';
    shtml += '<div style="display: inline-block; width: 30%;text-align: right;" id="paging-content">Hiển thị ';
    var arrCurrentPage = self.arrCurrentPage;
    $.each(arrCurrentPage, function (i, item) {
        strclass = 'class="pg"';
        if (item['0'] == numberrow) {
            strclass = 'class="pg current"';
        }
        shtml += '<a onclick="obj_record_search.gotoindexrowonpage(' + item['0'] + ')" ' + strclass + '>' + item['0'] + '</a>';
    });
    shtml += 'hồ sơ/1 trang</div>';
    $('#ListInfo').html(shtml);
};
record_search.prototype.gotopage = function (page) {
    var self = this;
    $('#hdn_current_page').val(page);
    self.loadlistrecord();
};

record_search.prototype.gotoindexrowonpage = function (rowonpage) {
    var self = this;
    $('#hdn_record_number_page').val(rowonpage);
    self.loadlistrecord();
};
//-----------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------
record_search.prototype.loadGeneral = function (mode, ownercode) {
    var self = this;
    $('#viewAllRecord').hide();
    $('#viewGeneral').show();
    $('form#formfilter #divcate').show();
    self.checkvalauedate();
    self.loaddata(mode, ownercode);

    $('#dFromDate').unbind('change');
    $('#dFromDate').change(function () {
        self.checkvalauedate();
    });

    $('#dToDate').unbind('change');
    $('#dToDate').change(function () {
        self.checkvalauedate();
    });

    $('.seachbutton').unbind('click');
    $('.seachbutton').click(function () {
        self.loaddata(mode, ownercode);
        $('#hdn_current_page').val(1);
    });

    $("#dFromDate").datepicker({
        changeMonth: true,
        gotoCurrent: true,
        minDate: new Date(1945, 1 - 1, 1),
        changeYear: true
    });

    $("#dToDate").datepicker({
        changeMonth: true,
        gotoCurrent: true,
        minDate: new Date(1945, 1 - 1, 1),
        changeYear: true
    });
    $('#hdn_current_page').val(1);
};

record_search.prototype.loadTitle = function (mode, type, ownercode) {
    var self = this;
    var lv0 = $('<a><span style="color:#0000FF;font-size: 13px;" class="lv0 redirect">HUYỆN LỆ THỦY</span></a>');
    if (this.checkward == '1') {
        var lv0 = $('<a><span style="color:#0000FF;font-size: 13px;" onclick="obj_record_search.loadGeneral(\'unit\',\'\');" class="lv0 redirect">HUYỆN LỆ THỦY</span></a>');
    }
    $("#link-view").html(lv0);

    if (ownercode != '') {
        name = self.getColBValue(ownercode, 'unit');
        var lv1 = $('<a> &gt; <span style="color:#0000FF;font-size: 13px;" onclick="obj_record_search.loadGeneral(\'tthc\',\'' + ownercode + '\');" class="lv1 redirect">' + name + '</span></a>');
        $("#link-view").append(lv1);
    }

    if (type != '') {
        var arrTitle = self.arrTitle;
        var typetext = '';
        $.each(arrTitle, function (i, item) {
            if (item.C_CODE == type) {
                typetext = item.C_NAME;
            }
        });
        var lv2 = $('<a> &gt; <span style="color:#0000FF;font-size: 13px;" class="lv2 redirect">' + typetext + '</span></a>');
        $("#link-view").append(lv2);
    }
};

record_search.prototype.loadnode = function (mode) {
    var b = '';
    if (mode == 'unit') {
        b = '<label style= "font-family: Tahoma,arial;font-size: 12px;font-weight: bold;" ><i><font color="#292b8c">* Nhấn vào "Tên đơn vị" để xem chi tiết tình hình giải quyết hồ sơ của đơn vị đó</font></i></label>';
        b += '<br><label style= "font-family: Tahoma,arial;font-size: 12px;font-weight: bold;" ><i><font color="#292b8c">* Nhấn vào "Số lượng hồ sơ" tại mỗi cột để xem chi tiết hồ sơ</font></i></label>';
    } else {
        b = '<label style= "font-family: Tahoma,arial;font-size: 12px;font-weight: bold;" ><i><font color="#292b8c">* Nhấn vào "Số lượng hồ sơ" tại mỗi cột để xem chi tiết hồ sơ</font></i></label>';
    }
    return b;
};

record_search.prototype.loadheader = function (mode) {
    var b = '';
    if (mode == 'unit') {
        b = 'Tên đơn vị';
    } else {
        b = 'TTHC';
    }
    var html = '<table cellpadding="0" cellspacing="0" border="0" width="99%" align="center" class="list_table2" id="table1"><colgroup><col width="3%"><col width="26%"><col width="8%"><col width="6%"><col width="6%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"><col width="5%"></colgroup>';
    html += '<THEAD><tr class="round_row"><td class="normal_label" rowspan="4" align="center" style="font-weight:bold;">STT</td><td class="normal_label" rowspan="4" align="center" style="font-weight:bold;">' + b + '</td><td class="normal_label" colspan="3" align="center" style="font-weight:bold;">Tổng hồ sơ nhận giải quyết</td><td class="normal_label" colspan="10" align="center" style="font-weight:bold;">Kết quả giải quyết</td><td class="normal_label" rowspan="4" align="center" style="font-weight:bold;">Số hồ sơ chậm chuyển huyện</td></tr>';
    html += '<tr class="round_row"> <td class="normal_label" rowspan="3" align="center" style="font-weight:bold;">Tổng số</td> <td class="normal_label" colspan="2" align="center" style="font-weight:bold;">Trong đó</td> <td class="normal_label" colspan="5" align="center" style="font-weight:bold;">Số hồ sơ đã giải quyết</td> <td class="normal_label" colspan="5" align="center" style="font-weight:bold;">Số hồ sơ đang giải quyết</td></tr>';
    html += '<tr class="round_row"><td class="normal_label" rowspan="2" align="center" style="font-weight:bold;">Kỳ trước chuyển sang</td><td class="normal_label" rowspan="2" align="center" style="font-weight:bold;">Trong kỳ</td><td class="normal_label" rowspan="2" align="center" style="font-weight:bold;">Tổng số</td><td class="normal_label" colspan="2" align="center" style="font-weight:bold;">Đúng hạn</td><td class="normal_label" colspan="2" align="center" style="font-weight:bold;">Quá hạn</td><td class="normal_label" rowspan="2" align="center" style="font-weight:bold;">Tổng số</td><td class="normal_label" colspan="2" align="center" style="font-weight:bold;">Chưa tới hạn</td><td class="normal_label" colspan="2" align="center" style="font-weight:bold;">Quá hạn</td></tr>';
    html += '<tr class="round_row"><td class="normal_label" align="center" style="font-weight:bold;">SL</td> <td class="normal_label" align="center" style="font-weight:bold;">Tỷ lệ %</td> <td class="normal_label" align="center" style="font-weight:bold;">SL</td> <td class="normal_label" align="center" style="font-weight:bold;">Tỷ lệ %</td> <td class="normal_label" align="center" style="font-weight:bold;">SL</td> <td class="normal_label" align="center" style="font-weight:bold;">Tỷ lệ %</td> <td class="normal_label" align="center" style="font-weight:bold;">SL</td> <td class="normal_label" align="center" style="font-weight:bold;">Tỷ lệ %</td> </tr>';
    html += '<tr class="odd_row"><td class="normal_label" align="center" style="font-size: 10px;font-style:italic">A</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">B</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(1)=2+3</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(2)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(3)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(4)=5+7</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(5)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(6)=5/4</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(7)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(8)=7/4</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(9)=10+12</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(10)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(11)=10/9</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(12)</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(13)=12/10</td> <td class="normal_label" align="center" style="font-size: 10px;font-style:italic">(14)</td></tr>';
    html += '</THEAD><TBODY ></TBODY></table>';
    return html;
};

record_search.prototype.loaddata = function (mode, ownercode) {
    var self = this;
    var oForm = $('#formfilter');
    var data = $(oForm).serialize();
    data += '&mode=' + mode;
    data += '&ownercode=' + ownercode;
    $('#note-process').show();
    var url = this.urlPath + '/loaddata';
    $('#table1 TBODY').html('');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (arrResult) {
            self.loadTitle(mode, '', ownercode);
            $('form#formGeneral #node').html(self.loadnode(mode));
            $('form#formGeneral #div_matrix').html(self.loadheader(mode));
            if (arrResult.length > 0) {
                var html = self.generalMatrix(arrResult, mode);
                $('form#formGeneral #table1 TBODY').html(html);
                self.loadmatrixeven();
            }
            $('#note-process').hide();
        }
    });
};

record_search.prototype.getColBValue = function (code, mode) {
    var arrB = new Array();
    var value = '';
    if (mode == 'unit') {
        arrB = this.arrOwner;
    }
    $.each(arrB, function (i, item) {
        if (item.code == code) {
            value = item.name;
        }
    });
    return value;
};

record_search.prototype.loadmatrixeven = function () {
    var self = this;
    $('.colB').click(function () {
        var mode = $(this).attr('mode');
        if (mode == 'unit') {
            var ownercode = $(this).attr('code');
            self.loaddata('tthc', ownercode);
            $('.seachbutton').unbind('click');
            $('.seachbutton').click(function () {
                self.loaddata('tthc', ownercode);
            });
        }
        return true;
    });

    $('.datacell').click(function () {
        var mode = $(this).attr('mode');
        var type = $(this).attr('type');
        var ownercode = $(this).attr('code');
        var recordtype = $(this).attr('recordtype');
        self.loadRecordList(mode, type, recordtype, ownercode);
        return true;
    });
};

record_search.prototype.loadRecordList = function (mode, type, recordtype, ownercode) {
    var self = this;
    var oForm = $('#formfilter');
    var numberrow = $('#formfilter #hdn_record_number_page').val();
    var page = $('#formfilter #hdn_current_page').val();
    var data = $(oForm).serialize();
    data += '&mode=' + mode;
    data += '&type=' + type;
    data += '&recordtype=' + recordtype;
    data += '&ownercode=' + ownercode;
    $('#note-process').show();
    var url = this.urlPath + '/loadrecord';
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (arrResult) {
            self.loadTitle(mode, type, ownercode);
            $('#viewGeneral').hide();
            $('form#formfilter #divcate').hide();
            var html = '';
            if (arrResult.length > 0) {
                html = self.generalList(arrResult);
            }
            $('form#formRecordList #table1 TBODY').html(html);
            self.addRowEmpty($('form#formRecordList #table1 TBODY'), 15);
            if (arrResult.length > 0) {
                var total = arrResult[0]['C_TOTAL_RECORD'];
                self.loadpageinfo(arrResult.length, numberrow, page, total, mode, type, recordtype, ownercode);
            } else {
                $('#ListInfo').html('');
            }
            self.loadlisteven(mode, type, recordtype, ownercode);
            $('#viewAllRecord').show();
            $('#note-process').hide();
            return true;
        }
    });
};

record_search.prototype.loadpageinfo = function (length, numberrow, page, total, mode, type, recordtype, ownercode) {
    var self = this;
    var shtml = '<div style="display: inline-block; width: 30%;text-align: left;">Danh sách có ' + length + '/' + total + ' hồ sơ</div>';
    shtml += '<div style="display: inline-block; width: 30%;text-align: center;" id="paging-content-modal">';
    var totalpage = Math.abs(total / numberrow);
    if (Math.abs(total % numberrow) > 0) {
        totalpage = totalpage + 1;
    }
    var strclass = 'class="pg"';
    if (page > 1) {
        shtml += '<a onclick="obj_record_search.gotopageModal(' + (parseInt(page) - 1) + ',\'' + mode + '\',\'' + type + '\',\'' + recordtype + '\',\'' + ownercode + '\')" ' + strclass + '>Trước</a>';
    }
    for (var i = 1; i <= totalpage; i++) {
        strclass = 'class="pg"';
        if (i == page) {
            strclass = 'class="pg current"';
        }
        shtml += '<a onclick="obj_record_search.gotopageModal(' + i + ',\'' + mode + '\',\'' + type + '\',\'' + recordtype + '\',\'' + ownercode + '\')" ' + strclass + '>' + i + '</a>';
    }
    if (totalpage > page + 1) {
        shtml += '<a onclick="obj_record_search.gotopageModal(' + (parseInt(page) + 1) + ',\'' + mode + '\',\'' + type + '\',\'' + recordtype + '\',\'' + ownercode + '\')" ' + strclass + '>Tiếp</a>';
    }
    shtml += '</div>';

    shtml += '<div style="display: inline-block; width: 30%;text-align: right;" id="paging-content">Hiển thị ';
    var arrCurrentPage = self.arrCurrentPage;
    $.each(arrCurrentPage, function (i, item) {
        strclass = 'class="pg"';
        if (item['0'] == numberrow) {
            strclass = 'class="pg current"';
        }
        shtml += '<a onclick="obj_record_search.gotorowonpage(' + item['0'] + ',\'' + mode + '\',\'' + type + '\',\'' + recordtype + '\',\'' + ownercode + '\')" ' + strclass + '>' + item['0'] + '</a>';
    });
    shtml += 'hồ sơ/1 trang</div>';
    $('#ListInfo').html(shtml);
};

record_search.prototype.gotopageModal = function (page, mode, type, recordtype, ownercode) {
    var self = this;
    $('#hdn_current_page').val(page);
    self.loadRecordList(mode, type, recordtype, ownercode);
};

record_search.prototype.gotorowonpage = function (rowonpage, mode, type, recordtype, ownercode) {
    var self = this;
    $('#hdn_record_number_page').val(rowonpage);
    self.loadRecordList(mode, type, recordtype, ownercode);
};

record_search.prototype.loadlisteven = function (mode, type, recordtype, ownercode) {
    var self = this;
    $('.dataview').click(function () {
        var recordid = $(this).attr('code');
        var ownercode = $(this).attr('ownercode');
        if (recordid != '') {
            self.loadview(recordid, ownercode);
        }
        return true;
    });
    $('.seachbutton').unbind('click');
    $('.seachbutton').click(function () {
        $('#hdn_current_page').val(1);
        self.loadRecordList(mode, type, recordtype, ownercode);
    });

    $('.datarow').click(function () {
        select_row($(this));
    });
};

record_search.prototype.loadview = function (recordid, ownercode) {
    var self = this;
    var arrdata = {
        recordid: recordid,
        ownercode: ownercode
    };
    $('#note-process').show();
    var url = this.urlPath + '/loadview';
    $.ajax({
        url: url,
        type: 'POST',
        data: arrdata,
        dataType: 'html',
        success: function (string) {
            $('#modal_tempo').html(string);
            $('#viewall').hide();
            $('#modal_tempo').show();
            self.loadviewEven();
            $('#note-process').hide();
            return true;
        }
    });
};

record_search.prototype.loadviewEven = function () {
    $('#btn_back').unbind('click');
    $('#btn_back').click(function () {
        $('#viewall').show();
        $('#modal_tempo').hide();
    });
};

record_search.prototype.generalMatrix = function (arrResult, mode) {
    var self = this;
    var html;
    html = '';
    var classtr = "odd_row";
    var t1 = 0, t2 = 0, t3 = 0, t4 = 0, t5 = 0, t7 = 0, t9 = 0, t10 = 0, t12 = 0, t14 = 0;
    $.each(arrResult, function (i, item) {
        if (classtr == 'odd_row') {
            classtr = "round_row";
        } else {
            classtr = "odd_row";
        }
        stt = i + 1;
        name = '';
        code = item.C_OWNER_CODE;
        recordtype = '';
        if (mode == 'unit') {
            name = self.getColBValue(item.C_OWNER_CODE, mode);
        } else {
            name = item.C_NAME;
            recordtype = item.FK_RECORDTYPE;
        }
        tongso = item.C_HS_KY_TRUOC_CHUYEN_SANG + item.C_HS_TRONG_KY;
        tongsodagiaiquyet = item.C_HS_DA_GIAI_QUYET_DH + item.C_HS_DA_GIAI_QUYET_QH;
        tongsodanggiaiquyet = item.C_HS_DANG_GIAI_QUYET_DUNG_HAN + item.C_HS_DANG_GIAI_QUYET_QUA_HAN;

        if (tongsodagiaiquyet > 0) {
            tl_dgq_dh = ((item.C_HS_DA_GIAI_QUYET_DH / tongsodagiaiquyet) * 100).toFixed(2);
            tl_dgq_qh = ((item.C_HS_DA_GIAI_QUYET_QH / tongsodagiaiquyet) * 100).toFixed(2);
        } else {
            tl_dgq_dh = '';
            tl_dgq_qh = '';
        }

        if (tongsodanggiaiquyet > 0) {
            tl_danggq_dh = ((item.C_HS_DANG_GIAI_QUYET_DUNG_HAN / tongsodanggiaiquyet) * 100).toFixed(2);
            tl_danggq_qh = ((item.C_HS_DANG_GIAI_QUYET_QUA_HAN / tongsodanggiaiquyet) * 100).toFixed(2);
        } else {
            tl_danggq_dh = '';
            tl_danggq_qh = '';
        }
        //Tong so
        t1 = t1 + tongso;
        t2 = t2 + item.C_HS_KY_TRUOC_CHUYEN_SANG;
        t3 = t3 + item.C_HS_TRONG_KY;
        t4 = t4 + tongsodagiaiquyet;
        t5 = t5 + item.C_HS_DA_GIAI_QUYET_DH;
        t7 = t7 + item.C_HS_DA_GIAI_QUYET_QH;
        t9 = t9 + tongsodanggiaiquyet;
        t10 = t10 + item.C_HS_DANG_GIAI_QUYET_DUNG_HAN;
        t12 = t12 + item.C_HS_DANG_GIAI_QUYET_QUA_HAN;
        t14 = t14 + item.C_HS_CHAM_CHUYEN_HUYEN;
        html += '<tr class="' + classtr + '">' +
            '<td align="center" class="normal_label">' + stt + '</td>' +
            '<td align="left" class="normal_label colB" mode="' + mode + '" code="' + code + '" style="padding-left: 5px;">' + name + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_TONG_SO" >' + tongso + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_KY_TRUOC_CHUYEN_SANG" >' + item.C_HS_KY_TRUOC_CHUYEN_SANG + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_TRONG_KY" >' + item.C_HS_TRONG_KY + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_TONG_SO_DA_GIAI_QUYET" >' + tongsodagiaiquyet + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_DA_GIAI_QUYET_DH" >' + item.C_HS_DA_GIAI_QUYET_DH + '</td>' +
            '<td align="center" class="normal_label"><span style="color:blue;">' + tl_dgq_dh + '%</span></td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_DA_GIAI_QUYET_QH" >' + item.C_HS_DA_GIAI_QUYET_QH + '</td>' +
            '<td align="center" class="normal_label"><span style="color:red;">' + tl_dgq_qh + '%</span></td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_TONG_SO_DANG_GIAI_QUYET" >' + tongsodanggiaiquyet + '</td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_DANG_GIAI_QUYET_DUNG_HAN" >' + item.C_HS_DANG_GIAI_QUYET_DUNG_HAN + '</td>' +
            '<td align="center" class="normal_label"><span style="color:blue;">' + tl_danggq_dh + '%</span></td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_DANG_GIAI_QUYET_QUA_HAN" >' + item.C_HS_DANG_GIAI_QUYET_QUA_HAN + '</td>' +
            '<td align="center" class="normal_label"><span style="color:red;">' + tl_danggq_qh + '%</span></td>' +
            '<td align="center" class="normal_label datacell" mode="' + mode + '" code="' + code + '" recordtype="' + recordtype + '" type="C_HS_CHAM_CHUYEN_HUYEN" ><span style="color:red;">' + item.C_HS_CHAM_CHUYEN_HUYEN + '</span></td>' +
            '</tr>';
    });
    if (classtr == 'odd_row') {
        classtr = "round_row";
    } else {
        classtr = "odd_row";
    }
    if (t4 > 0) {
        tl_dgq_dh = ((t5 / t4) * 100).toFixed(2);
        tl_dgq_qh = ((t7 / t4) * 100).toFixed(2);
    } else {
        tl_dgq_dh = '';
        tl_dgq_qh = '';
    }

    if (t9 > 0) {
        tl_danggq_dh = ((t10 / t9) * 100).toFixed(2);
        tl_danggq_qh = ((t12 / t9) * 100).toFixed(2);
    } else {
        tl_danggq_dh = '';
        tl_danggq_qh = '';
    }
    html += '<tr class="' + classtr + '">' +
        '<td align="center" style="font-weight: bold;" colspan="2" class="normal_label">Tổng</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t1 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t2 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label" style="">' + t3 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t4 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label" style="">' + t5 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label"><span style="color:blue;">' + tl_dgq_dh + '%</span></td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t7 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label"><span style="color:red;">' + tl_dgq_qh + '%</span></td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t9 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t10 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label"><span style="color:blue;">' + tl_danggq_dh + '%</span></td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label">' + t12 + '</td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label"><span style="color:red;">' + tl_danggq_qh + '%</span></td>' +
        '<td align="center" style="font-weight: bold;" class="normal_label"><span style="color:red;">' + t14 + '</span></td>' +
        '</tr>';
    return html;
};

record_search.prototype.generalList = function (arrResult) {
    var self = this;
    var html;
    html = '';
    var classtr = "odd_row";
    $.each(arrResult, function (i, item) {
        if (classtr == 'odd_row') {
            classtr = "round_row";
        } else {
            classtr = "odd_row";
        }
        html += '<tr class="' + classtr + '">' +
            '<td align="center" class="normal_label datarow" style="padding-left: 5px;">' + item.C_CODE + '</td>' +
            '<td align="left" class="normal_label datarow" >' + item.C_RECORDTYPE_NAME + '</td>' +
            '<td align="center" class="normal_label datarow" >' + item.C_RECEIVED_DATE + '</td>' +
            '<td align="left" class="normal_label datarow" style="padding-left: 3px;padding-right: 3px;">' + item.CHU_HS + '</td>' +
            '<td align="left" class="normal_label datarow" style="padding-left: 3px;padding-right: 3px;">' + item.DC_CHU_HS + '</td>' +
            '<td align="center" class="normal_label datarow" >' + item.C_APPOINTED_DATE + '</td>' +
            '<td align="center" class="normal_label dataview" code="' + item.PK_RECORD + '" ownercode="' + item.C_OWNER_CODE + '"><a style="text-decoration: underline;">Chi tiết</a></td>' +
            '</tr>';
    });
    return html;
};

record_search.prototype.checkvalauedate = function () {
    var from_date = $('#dFromDate').val().split('/');
    var to_date = $('#dToDate').val().split('/');

    var dateTwo = new Date(to_date[2], to_date[1] - 1, to_date[0]);
    var dateOne = new Date(from_date[2], from_date[1] - 1, from_date[0]);
    var datenow = new Date();

    if (dateTwo > datenow) {
        alert('Đến ngày phải nhỏ hơn hoặc bằng ngày hiện tại!');
        $('#dToDate').val('');
        $('#dToDate').focus();
    }

    if (dateOne > dateTwo) {
        alert('Từ ngày phải nhỏ hơn đến ngày!');
        $('#dFromDate').val('');
        $('#dFromDate').focus();
    }
};

record_search.prototype.addRowEmpty = function (obj, num) {
    var numRow = $(obj).children().length, numCol = 7, shtml = '';
    var classrow = $(obj).children(':last-child').attr('class');
    if (numRow < num) {
        for (var i = 0; i < (num - numRow); i++) {
            if (classrow == 'odd_row') {
                classrow = "round_row";
            } else {
                classrow = "odd_row";
            }
            shtml += '<tr class="' + classrow + '">';
            for (var j = 0; j < numCol; j++) {
                shtml += '<td>&nbsp;</td>';
            }
            shtml += '</tr>';
        }
        $(obj).append(shtml);
    }
};

function select_row(obj) {
    var oTable = $(obj).closest('table');
    $('tr', oTable).removeClass('selected');
    $('input[name="chk_item_id"]', oTable).attr('checked', false);
    $(obj).parent().addClass('selected');
    var attDisabled = $(obj).parent().find('input[name="chk_item_id"]').attr('disabled');
    if (typeof(attDisabled) === 'undefined' || attDisabled == '')
        $(obj).parent().find('input[name="chk_item_id"]').trigger('click');
}