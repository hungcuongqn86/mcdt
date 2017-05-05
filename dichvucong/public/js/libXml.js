function Replace_VietNamese(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    return str;
}
//
function selectgroup(obj) {
    var classStyle = '', v_group = '', flag = false;
    var group_value = $(obj).parent().parent().find('input[type="checkbox"][name="chk_item_group"]').val();
    var oTableCurrent = $(obj).parent().parent().parent().parent();
    var data = getAllIndex(group_value);
    var startIndex = data.startIndex;
    var endIndex = data.endIndex;
    if ($(obj).hasClass('icon_group_open')) {
        $(obj).addClass('icon_group_close');
        $(obj).removeClass('icon_group_open');
        for (var i = startIndex + 1; i < endIndex; i++) {
            $(oTableCurrent).find('tr:eq(' + i + ')').hide();
        }
    } else {
        $(obj).addClass('icon_group_open');
        $(obj).removeClass('icon_group_close');
        for (var i = startIndex + 1; i < endIndex; i++) {
            $(oTableCurrent).find('tr:eq(' + i + ')').show();
        }
    }
}
//
function checkgroup(obj) {
    var group_value = $(obj).val();
    var data = getAllIndex(group_value);
    var oTableCurrent = $(obj).parent().parent().parent().parent();
    var checked = (obj.checked ? true : false);
    var objtr = '', flag = false;
    for (var i = data.startIndex + 1; i < data.endIndex; i++) {
        objtr = $(oTableCurrent).find('tr:eq(' + i + ')');
        flag = false;
        objtr.find('input[type="checkbox"][name="chk_item_id"]').each(function () {
            if ($(this).is(':disabled') === false) {
                $(this).attr('checked', checked);
                flag = true;
            }
        })
        if (checked && flag) {
            objtr.addClass('selected');
        } else {
            objtr.removeClass('selected');
        }
    }
}
function getAllIndex(group_value) {
    var startIndex = 0;
    var endIndex = 0;
    var index = 0;
    $('table.list-table-data tr').each(function () {
        if ($(this).hasClass('tr_group')) {
            v_group = $(this).find('input[type="checkbox"][name="chk_item_group"]').val();
            if (v_group === group_value)
                startIndex = $(this).index();
            if (v_group != group_value && startIndex != 0 && endIndex === 0)
                endIndex = $(this).index();
        }
        if ($(this).find('input[type="checkbox"]').length > 0)
            index = $(this).index();
    })
    if (endIndex === 0)
        endIndex = index + 1;
    return data = {startIndex: startIndex, endIndex: endIndex};
}

function loadeventsort(obj, callback) {
    if (typeof(callback) != 'function') {
        callback = function () {
        }
    }
    $(obj).find('td.sortdb').disableSelection();
    $(obj).find('td.sortdb').click(function () {
        var columnName = $(this).attr('columnName');
        var tablename = $(this).attr('tablename');
        var xmldata = $(this).attr('xmldata');
        var xmltagindb = $(this).attr('xmltagindb');
        var orderby = '';
        if ($(this).hasClass('headerdf')) {
            refreshclass(obj, $(this));
            $(this).removeClass('headerdf');
            $(this).addClass('headerasc');
            orderby = 'ASC';
        } else {
            if ($(this).hasClass('headerasc')) {
                refreshclass(obj, $(this));
                $(this).removeClass('headerasc');
                $(this).addClass('headerdesc');
                orderby = 'DESC';
            } else {
                refreshclass(obj, $(this));
                $(this).removeClass('headerdesc');
                $(this).addClass('headerasc');
                orderby = 'ASC';
            }
        }
        var sFormID = $(obj).closest("form").attr('id');
        var sHtmlSort = '<input type="hidden" name="hdn_columnName" id="hdn_columnName" value="' + columnName + '" />';
        sHtmlSort += '<input type="hidden" name="hdn_tablename" id="hdn_tablename" value="' + tablename + '" />';
        sHtmlSort += '<input type="hidden" name="hdn_xmldata" id="hdn_xmldata" value="' + xmldata + '" />';
        sHtmlSort += '<input type="hidden" name="hdn_xmltagindb" id="hdn_xmltagindb" value="' + xmltagindb + '" />';
        sHtmlSort += '<input type="hidden" name="hdn_orderby" id="hdn_orderby" value="' + orderby + '" />';
        if ($('div#paramesearch').length > 0) {
            $('div#paramesearch').html(sHtmlSort);
        } else {
            sHtmlSort = '<div id="paramesearch">' + sHtmlSort + '</div>';
            $(obj).closest("form").append(sHtmlSort);
        }
        callback();
    })
}
function refreshclass(obj, oCurrent) {
    $(obj).find('td.sortdb').removeClass('headerasc,headerdesc');
    $(obj).find('td.sortdb').addClass('headerdf');
    oCurrent.removeClass('headerdf');
}
function addTaskOrther() {
    /*var checkOther = taskOther;
    if (checkOther == true) {
        var sHtml = '<a id="showTask" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all link-button elmLink menudropdown fg-menu-open ui-state-active" value="In" href="#" tabindex="0">';
        sHtml += '<span class="ui-icon ui-icon-triangle-1-s"></span>Tác vụ khác</a>';
        sHtml += '<div id="search-engines" class="hidden"><ul>';
        sHtml += '<li><a id="addStar" onclick="addStar()" href="#">Đánh dấu <div class="star" style="display:inline-block"></div></a></li>';
        sHtml += '<li><a id="deleteStar" onclick="removeStar()" href="#">Xóa dấu <div class="unstar" style="display:inline-block"></div></a></li>';
        sHtml += '</ul></div>';
        // var sHtml = '<input id="showTask" class="link-button elmLink" type="button"  value="Tác vụ khác">';
        var oDivButton = $('div.button-link-container div.blc-right');
        if ($('#showTask').length === 0)
            oDivButton.append(sHtml);
        loadmenudropdown();
    }
    else return false;*/
}
// Danh sao
function addStar() {
    var pkrecordlist = '', arrRecord = [];
    $('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        pkrecordlist += $(this).val() + ',';
        arrRecord.push($(this).val());
    })
    if (pkrecordlist.length === 0) {
        jAlert('Bạn chưa chọn một hồ sơ nào để đánh dấu!', 'Error');
        return false;
    }
    pkrecordlist = pkrecordlist.substr(0, pkrecordlist.length - 1);
    var data = {pkrecordlist: pkrecordlist};
    $.ajax({
        url: baseUrl + '/main/ajax/markstarupdate/',
        type: 'POST',
        data: data,
        success: function (string) {
            var oCheckMark = $('input[type="checkbox"][name="chk_item_id"]:checked');
            oCheckMark.next().removeClass('unstar');
            oCheckMark.next().addClass('star');
        }
    });
}
// Xoa sao
function removeStar() {
    var pkrecordlist = '', arrRecord = [];
    $('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        pkrecordlist += $(this).val() + ',';
        arrRecord.push($(this).val());
    })
    if (pkrecordlist.length === 0) {
        jAlert('Bạn chưa chọn một hồ sơ nào để bỏ đánh sao!', 'Error');
        return false;
    }
    pkrecordlist = pkrecordlist.substr(0, pkrecordlist.length - 1);
    var data = {pkrecordlist: pkrecordlist};
    $.ajax({
        url: baseUrl + '/main/ajax/removestar/',
        type: 'POST',
        data: data,
        success: function (string) {
            var oCheckMark = $('input[type="checkbox"][name="chk_item_id"]:checked');
            oCheckMark.next().removeClass('star');
            oCheckMark.next().addClass('unstar');
        }
    });
}
function loadmenudropdown() {
    $(".menudropdown").each(function () {
        menudropdown($(this));
    })
}
function getdatalistfilter(obj, callback) {
    if ($(obj).find('input[type="checkbox"][name="chk_all_item_id"]').is(':hover')) {
        return false;
    }
    var sHtml = '';
    var oForm = $(obj).closest("form");
    sHtml += '<div class="filter1" style="display:none">';
    sHtml += '<div id="detailtask">'
    sHtml += '<ul class="f2">';
    sHtml += '<li class="lif2 normal_label" star="1">Lọc theo tác vụ đánh dấu <div class="star" style="display:inline-block"></div></li>';
    sHtml += '<li class="lif2 normal_label" star="0">Lọc theo tác vụ đánh dấu <div class="unstar" style="display:inline-block"></div></li>';
    sHtml += '</ul>';
    sHtml + '</div>';
    sHtml + '</div>';
    var top = $(oForm).find('table#table-data').position().top;
    var left = $(oForm).find('tr.header td:eq(0)').position().left + 5;
    if ($('.filter1').length === 0)
        $('body').append(sHtml);

    $('.filter1').css(
        {
            // 'width':$('.s1').width(),
            'position': 'fixed',
            'visibility': 'visible',
            'z-index': 9999,
            'top': top + 28, //+ $('.s2').height() + 2
            'left': left

        }
    );
    if ($('.filter1').is(':hidden')) {
        $('.filter1').show();
    }
    else {
        $('.filter1').hide();
    }
    $('li.lif2').unbind('click');
    $('li.lif2').click(function () {
        var star = $(this).attr('star');
        var oForm = $(obj).closest("form");
        var sHidden = '<input type="hidden" id="hdn_star" name="hdn_star" value="' + star + '" />';
        $(oForm).append(sHidden);
        callback();
        $('div.filter1').hide();
        $(oForm).find('#hdn_star').remove();
    })
}
/**
 * @todo: cat bo tag html trong chuoi java string
 */
function skip_tag(str) {
    var tmp = 0;
    for (i = 0; i < str.length; i++) {
        if (str[i] == '<')
            if (i == 0 || i != tmp) { // i = 0: Thể html nằm vị trí đầu của string, i != tmp: 1 phần nội dung string nằm giữa 2 thẻ
                str = '';
                break;
            }
            else
                for (j = i; j < str.length; j++)
                    if (str[j] == '>') {
                        var tag = str.substring(i, j + 1);
                        str = str.replace(tag, "");
                        // alert(str);
                        tmp = i;
                        i--;
                        continue;
                    }
    }
    return str;
}

function select_row(obj) {
    var oTable = $(obj).closest('table');
    $('tr', oTable).removeClass('selected');
    $('input[name="chk_item_id"]', oTable).attr('checked', false);
    $(obj).parent().addClass('selected');
    var attDisabled = $(obj).parent().find('input[name="chk_item_id"]').attr('disabled');
    if (typeof(attDisabled) === 'undefined' || attDisabled == '')
        $(obj).parent().find('input[name="chk_item_id"]').trigger('click');
}


(function () {
    String.prototype.allIndexOf = function (string, ignoreCase) {
        if (this === null) {
            return [-1];
        }
        var t = (ignoreCase) ? this.toString().toLowerCase() : skip_tag(Replace_VietNamese(this)),
            s = (ignoreCase) ? string.toString().toLowerCase() : skip_tag(Replace_VietNamese(string.toString())),
            i = skip_tag(Replace_VietNamese(this).indexOf(skip_tag(Replace_VietNamese(s)))),
            len = this.length,
            n,
            indx = 0,
            result = [];
        if (len === 0 || i === -1) {
            return [i];
        }
        for (n = 0; n <= len; n++) {
            i = t.indexOf(s, indx);
            if (i !== -1) {
                indx = i + 1;
                result.push(i);
            } else {
                return result;
            }
        }
        return result;
    }
    // To mau
    String.prototype.searchStringColor = function (string, ignoreCase) {
        if (string == '' || string == ' ') {
            return this;
        }
        var result = this.allIndexOf(string, ignoreCase);
        var len = result.length;
        strlen = string.length, stroutput = '', t = 0;
        if (result[0] == -1) {
            stroutput = this;
        } else {
            for (var i = 0; i < len; i++) {
                stroutput += this.substring(t, result[i]) + '<span class = "lblcolor">' + this.substring(result[i], result[i] + strlen) + '</span>';
                t = result[i] + strlen;
            }
            stroutput += this.substring(t, this.length);
        }
        return stroutput;
    }
})();

(function () {
    libXml = function (options) {
        this.dirxml = baseUrl + '/main/index/xmldatalist?f=' + options.dirxml;
        this._settings = $.extend(this._settings, options);
        this.init();
        if (this._settings.autoStart == 1)
            this.loadfile();
    }
    libXml.prototype._settings = {
        oClass: function () {
        },
        minrow: 15,
        autoStart: 1,
        utilities: false,
        modaldialog: false,
        getdata: function () {
        }

    }
    libXml.prototype.init = function () {
        this.xml = '';
        this.identity = 0;
        this.row_index = '';
        this.v_current_style_name = '';
        this.v_id_column = '';
        this.v_onclick = '';
        this.v_ondblclick = '';
        this.v_have_move = '';
        this.v_table = '';
        this.v_pk_column = '';
        this.v_filename_column = '';
        this.content_column = '';
        this.v_append_column = '';
        this.p_arr_item = '';
        this.display_option = '';
        this.url_exec = '';
        this.pClassname = '';
        this.objectId = '';
        this.v_width = '';
        this.v_label = '';
        this.v_align = '';
        this.columnName = '';
        this.alias_name = '';
        this.xmlData = '';
        this.xmlTagInDb = '';
        this.v_value = '';
        this.tablename = '';
        this.datasearch = false;
        this.markstar = 0;
    }
    libXml.prototype.loadfile = function () {
        var self = this;
        $.ajax({
            type: "GET",
            url: self.dirxml,
            dataType: "xml",
            success: function (xml) {
                self.xml = xml;
            }
        })
    }

    libXml.prototype._xmlGetXmlTagValue = function (sXmlString, sXmlParentTag, sXmlTag) {
        var valueReturn = "";//create default variable
        $(sXmlString).find(sXmlParentTag).each(function () {
            valueReturn = $(this).find(sXmlTag).text()
        });
        return valueReturn;
    }

    libXml.prototype._yyyymmddToDDmmyyyyhhmm = function (sDateTimeValue) {
        var sReturnDate = "";
        var dateTimeSplit = sDateTimeValue.split(' ');
        var dateSplit = dateTimeSplit[0].split('-');
        var currentDate = dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];
        //Get time
        var currentTime = dateTimeSplit[1];
        if (currentTime != "") {
            sReturnDate = currentDate + " " + currentTime;
        }
        return sReturnDate;
    }

    libXml.prototype._yyyymmddToDDmmyyyy = function (sDateTimeValue) {
        var sReturnDate = "";
        var dateTimeSplit = sDateTimeValue.split(' ');
        var dateSplit = dateTimeSplit[0].split('-');
        var currentDate = dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];
        return sReturnDate;
    }

    libXml.prototype._ddmmyyyyToYYyymmdd = function (sDateTimeValue) {
        var sReturnDate = "";
        var dateTimeSplit = sDateTimeValue.split(' ');
        var dateSplit = dateTimeSplit[0].split('/');
        var currentDate = dateSplit[2] + '-' + dateSplit[1] + '-' + dateSplit[0];
        return sReturnDate;
    }

    libXml.prototype._ddmmyyyyToYYyymmddhhmm = function (sDateTimeValue) {
        var sReturnDate = "";
        var dateTimeSplit = sDateTimeValue.split(' ');
        var dateSplit = dateTimeSplit[0].split('/');
        var currentDate = dateSplit[2] + '-' + dateSplit[1] + '-' + dateSplit[0];
        //Get date
        var now = new Date();
        var outStr = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
        sReturnDate = currentDate + " " + outStr;
        return sReturnDate;
    }
    libXml.prototype.exportTable = function (options) {
        var self = this;
        if (typeof(this.xml) !== 'object') {
            setTimeout(function () {
                self.exportTable(options);
            }, 100);
        } else {
            options = $.extend({
                result: [],
                objupdate: $('#table-container'),
                form: $('form'),
                addrow: true,
                callback: function() {
                }
            }, options);
            if (typeof(options.fulltextsearch) === 'undefined') options.fulltextsearch = $(':input[column_name="sDataTemp"]', options.form).val();
            self.generalTable(options);
        }
    }

    libXml.prototype.generalTable = function (options) {
        var self = this, count_column = '', colspan = 0, xmlString = '', htmlexport = '', v_type = '';
        var cols = self.parseCols(),
            arrResult = options.result,
            fulltextsearch = options.fulltextsearch;
        var hdn_columnName = $('#hdn_columnName').val(),
            hdn_orderby = $('#hdn_orderby').val(),
            classtemp = 'headerdf';
        var xml = this.xml, iTotalRecord = arrResult.length;
        htmlexport += '<table class="list-table-data" cellpadding="0" cellspacing="0" border="0" align="center" id="table-data">';
        var objHeader = self.genHtmlHeader(cols);
        htmlexport += objHeader.colgroup + objHeader.header;
        // groupcol
        var groupname = $('level1groupid', xml).text(), v_default1 = '', function_group = $('function_group', xml).text(), groupdisplay = '';
        if (groupname === '')
            groupname = $('group_column', xml).text();
        count_column = parseInt($('col', xml).length);
        colspan = count_column - 1;
        
        for (var i = 0; i < iTotalRecord; i++) {
            self.markstar = arrResult[i]['MARK_STAR'];
            if (typeof(self.markstar) === 'undefined') self.markstar = 0;
            if (groupname != '' && v_default1 != arrResult[i][groupname]) {
                v_default1 = arrResult[i][groupname];
                if (typeof(function_group) != 'undefined' && function_group != '') {
                    groupdisplay = arrResult[i]['f_' + groupname];
                } else {
                    groupdisplay = v_default1;
                }
                htmlexport += '<tr class="tr_group"><td align="CENTER"><input type="checkbox" onclick="selectrow(this);checkgroup(this)" class="groupcol" name="chk_item_group" value="' + v_default1 + '" /></td>';
                htmlexport += '<td colspan="' + colspan + '"><div class="icon_group_open" onclick="selectgroup(this)">&nbsp;&nbsp;</div><label style="font-weight: bold;width: auto;">' + groupdisplay + '</label></td></tr>';
            }
            if (arrResult[i]['Check'] == 'co_mau_hs') {
                htmlexport += '<tr style="color:blue;" title="Hồ sơ bên tiếp nhận">';
            }
            else {
                htmlexport += '<tr>';
            }
            //render rows
            $.each(cols, function (index, value) {
                $.extend(self, value)
                if (value.xmlData == 'false') {
                    self.v_value = arrResult[i][value.alias_name];
                } else {
                    if (typeof(arrResult[i][value.xmlTagInDb]) == 'undefined') {
                        xmlString = arrResult[i][value.alias_name];
                        self.v_value = self._xmlGetXmlTagValue(xmlString, "data_list", value.xmlTagInDb);
                    } else {
                        self.v_value = arrResult[i][value.xmlTagInDb];
                    }                    
                }
                if (value.phpfunction != '') {
                    self.v_value = arrResult[i]['f_' + value.alias_name];
                }
                if(typeof(self.v_value) == 'undefined') self.v_value = ''
                v_type = value.v_type;
                if (v_type != 'checkbox' && typeof(fulltextsearch) != 'undefined' && fulltextsearch != '') {
                    var s = new String(self.v_value);
                    self.v_value = s.searchStringColor(fulltextsearch);
                }
                htmlexport += self._generateHtmlForColumn(v_type, i);
            })
            htmlexport += '</tr>';

        }
        $(options.objupdate).html(htmlexport);
        if (options.addrow) {
            //Auto fill row empty in table
            self.addRowEmpty($('table#table-data tbody', options.form), self._settings.minrow);
            loadeventsort($('table#table-data tbody', options.form), self._settings.getdata);
        }
        if (this._settings.utilities) {
            var oTDCheck = $('tr.header td:eq(0)', options.objupdate);
            $(oTDCheck).click(function () {
                getdatalistfilter($(this), self._settings.getdata);
            })
        }
        // Callback
        options.callback.apply();
    }

    libXml.prototype._generateHtmlForColumn = function (v_type, i) {
        var htmloutput = '', self = this, cstar = 'unstar';
        v_type = v_type.toLowerCase();

        switch (v_type) {
            case "checkbox":
                self.v_onclick = 'selectrow(this);' + self.v_onclick;
                htmloutput += '<td align="' + this.v_align + '"><input type="checkbox" ondblclick="' + self.v_ondblclick + '" onclick="{' + self.v_onclick + '}" name="chk_item_id" value="' + this.v_value + '" />';
                if (this.utilities) {
                    cstar = (self.markstar === 1 ? 'star' : 'unstar');
                }
                htmloutput += '</td>';
                break;
            case "text":
                self.v_onclick = 'select_row(this);' + self.v_onclick;
                htmloutput += '<td class="data" align="' + this.v_align + '" ondblclick="' + self.v_ondblclick + '" onclick="{' + self.v_onclick + '}">' + '' + this.v_value + '</td>';
                break;
            //Kiểu date
            case "date":
                htmloutput += '<td class="data" align="' + this.v_align + '" ondblclick="' + self.v_ondblclick + '" onclick="select_row(this)">' + '' + self._yyyymmddToDDmmyyyy(this.v_value) + '</td>';
                break;
            case "identity":
                var identity = parseInt(i) + 1;
                self.v_onclick = 'select_row(this);' + self.v_onclick;
                htmloutput += '<td class="data" align="' + this.v_align + '" ondblclick="' + self.v_ondblclick + '" onclick="{' + self.v_onclick + '}">' + '' + identity + '</td>';
                break;
            default:
                htmloutput += this.v_value;
        }
        return htmloutput;
    }
    libXml.prototype.parseCols = function () {
        var self = this, cols = [],
            v_width, v_label, v_type, v_align, datasearch, tablename, phpfunction,
            xmlData, columnName, alias_name, xmlTagInDb, v_onclick, v_ondblclick

        $(self.xml).find('list_body col').each(function () {
            v_width = $('width', this).text();
            v_label = $('label', this).text();
            v_type = $('type', this).text();
            v_type = v_type.toLowerCase();
            v_align = $('align', this).text();
            if (typeof(v_align) === 'undefined') v_align = '';
            xmlData = $(this).find('xml_data').text();
            if (typeof(xmlData) === 'undefined') xmlData = 'false';
            columnName = $(this).find('column_name').text();
            if (typeof(columnName) === 'undefined') columnName = '';
            alias_name = $(this).find('alias_name').text();
            if (typeof(alias_name) === 'undefined' || alias_name === '') alias_name = columnName;
            xmlTagInDb = $(this).find('xml_tag_in_db').text();
            if (typeof(xmlTagInDb) === 'undefined') xmlTagInDb = '';
            datasearch = $(this).find('datasearch').text();
            if (typeof(datasearch) === 'undefined' || datasearch === 'undefined') datasearch = false;
            tablename = $(this).find('table_name').text();
            if (typeof(tablename) === 'undefined') tablename = '';
            v_onclick = '';
            v_ondblclick = ($('event_url', this) ? $('event_url', this).text() : '');
            phpfunction = ($('phpfunction', this) ? $('phpfunction', this).text() : '');
            cols.push({
                v_width: v_width,
                v_label: v_label,
                v_type: v_type,
                v_align: v_align,
                xmlData: xmlData,
                columnName: columnName,
                alias_name: alias_name,
                xmlTagInDb: xmlTagInDb,
                datasearch: datasearch,
                tablename: tablename,
                v_onclick: v_onclick,
                v_ondblclick: v_ondblclick,
                phpfunction: phpfunction
            })
        })
        return cols;
    }
    libXml.prototype.genHtmlHeader = function (cols) {
        var self = this, classtemp, colgroup = '', header = ''
        var hdn_columnName = $('#hdn_columnName').val(),
            hdn_orderby = $('#hdn_orderby').val()
        $.each(cols, function (index, value) {
            colgroup += '<col width="' + value.v_width + '">';
            if (value.datasearch === 'true') {
                if (hdn_columnName === value.columnName) {
                    if (hdn_orderby === 'ASC') {
                        classtemp = 'headerasc';
                    } else {
                        classtemp = 'headerdesc';
                    }
                }
                header += '<td class="sortdb ' + classtemp + '" columnName="' + value.columnName + '" xmldata="' + value.xmlData + '" xmlTagInDb="' + value.xmlTagInDb + '" tablename="' + value.tablename + '" align="center">' + value.v_label + '</td>';
            } else {
                header += '<td class="" align="center">' + value.v_label + '</td>';
            }
        });
        return {
            colgroup: '<colgroup>' + colgroup + '</colgroup>',
            header: '<tr class="header">' + header + '</tr>'
        }
    }

    libXml.prototype.addRowEmpty = function (obj, num) {
        var numRow = $(obj).children().length - 1, numCol = $(obj).children(':first-child').children().length, shtml = '';
        if (numRow < num) {
            for (var i = 0; i < (num - numRow); i++) {
                shtml += '<tr>';
                for (var j = 0; j < numCol; j++) {
                    shtml += '<td>&nbsp;</td>';
                }
                shtml += '</tr>';
            }
            $(obj).append(shtml);
        }
    }

})();

