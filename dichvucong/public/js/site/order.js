/*
 * Creater: ##
 * Date:##
 * Idea: ##
 */
function site_order(baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
    mysite_record = this;
    this.loadding = false;
    this.showDuplication = false;
    this.duplicateData = new Array();
    this.frmIndex = $('form#frm_record_index');
    this.objectXml = [];
    this.__recordtype = '';
    this.__recordtypeId = '';
    this.__recordTypeName = '';


}
// Load su kien tren man hinh index
$.extend(site_order.prototype, {
    loadIndex: function() {
        var self = this;
        $("#recordtype", self.frmIndex).chosen();
        
        $('#btn_add', self.frmIndex).click(function() {
            self.addRecord();
        })
        $('#btn_edit', self.frmIndex).click(function() {
            self.editRecord();
        })
        $('#btn_delete', self.frmIndex).click(function() {
            self.deleteRecord();
        })

        $('#recordtype').unbind('change').change(function () {
            self.__recordtype = $('option[value="' + $(this).val() + '"]').attr('code')
            self.__recordTypeName = $('option[value="' + $(this).val() + '"]').text()
            $('#recordTypeCode').val(self.__recordtype)
            self.__recordtypeId = $(this).val()
            // console.log(self.__recordtype);
            self.reLoadData()
        })
        $('#recordtype').trigger('change');
    },

    eventFormUpdate: function() {
        lddadepicker();
        var self = this;
        self.frmUpdate = $('form#frmUpdateRecord');
        $('.clsave').unbind('click').click(function() {
            self.save();
        })
        $('.clback').unbind('click').click(function() {
            $('div#IndexFrm').fadeIn(500);
            $('div#UpdateFrm').fadeOut(500);
            scrollTop(1);
        })
    },

    _savetagxml: function() {
        var arrXmlTag=[],
            arrXmlValue=[]

        $('input[v_type="multiple_checkbox"]', self.frmUpdate).each(function() {
            var id= $(this).attr('id'),
                obj_table =$('#table_' + id),
                arrValue=[];
            $('input[name="chk_multiple_checkbox"]:checked', obj_table).each(function() {
                arrValue.push($(this).val())
            })
            $(this).val(arrValue.join(','));


        })
        $('[xml_data="true"]', self.frmUpdate).each(function(){
            arrXmlTag.push($(this).attr('xml_tag_in_db'))
            arrXmlValue.push($(this).val())
        })
        $('#hdn_xml_tag_list', self.frmUpdate).val(arrXmlTag.join('!~~!'))
        $('#hdn_xml_value_list', self.frmUpdate).val(arrXmlValue.join('!~~!'))
    },
    __getAttachCheckbox: function() {
        var self = this
        var sFileAttachList = '', sUnFileAttachList = '', sDocTypeList = '', arrData = [], sDelimitor = '!~~!', locationList = '';
        $('div[type="ATTACHFILE_CHECKBOX"]', self.frmUpdate).each(function () {
            var elAttach = $(this),
                elCheckbox = $(this).closest('tr').find('[name="chk_multiple_checkbox"]');
            if ($(elCheckbox).is(':checked')) {
                doctype = $(elCheckbox).val();

                $('p.upload_complete', elAttach).each(function () {
                    sDocTypeList += doctype + sDelimitor;
                    sFileAttachList += $(this).attr('file_name') + sDelimitor;
                    locationList += $(this).attr('old') + sDelimitor;

                })
            }  

            sUnFileAttachList += $('#hdn_delete_file_item', elAttach).val() + sDelimitor;

        })
        
        var valuestring = '';
        $('table[v_type="multiplecheckbox_fileattach"] input.sglatach:checked').each(function () {
            valuestring += $(this).val() + ',';
            if ($(this).attr('attachFile') != '') {
                sDocTypeList += this.value + sDelimitor;
                sFileAttachList += $(this).attr('attachFile') + sDelimitor;
            }
        });
        // Loai bo phan tu thua
        valuestring = valuestring.substr(0, valuestring.length - 1);
        $('input.hdn_multiattach[v_type="multiplecheckbox_fileattach"]').val(valuestring);

        sDocTypeList = sDocTypeList.substr(0, sDocTypeList.length - sDelimitor.length);
        sFileAttachList = sFileAttachList.substr(0, sFileAttachList.length - sDelimitor.length);
        sUnFileAttachList = sUnFileAttachList.substr(0, sUnFileAttachList.length - sDelimitor.length);
        locationList = locationList.substr(0, locationList.length - sDelimitor.length);

        if (option === 1) {
            var string = ''
            string += '&sFileAttachList=' + sFileAttachList;
            string += '&sDocTypeList=' + sDocTypeList;
            string += '&sUnFileAttachList=' + sUnFileAttachList;
            string += '&locationList=' + locationList;
            string += '&sDelimitor=' + sDelimitor;
            return string;
        } else {
            return arrData = {
                sFileAttachList: sFileAttachList,
                sDocTypeList: sDocTypeList,
                sUnFileAttachList: sUnFileAttachList,
                locationList: locationList,
                sDelimitor: sDelimitor
            }
        }
    },
    addRecord: function() {
        var self = this
        $.ajax({
            url: self.urlPath + '/add',
            type: "POST",
            data: {recordtype: self.__recordtypeId, recordTypeCode: self.__recordtype, recordTypeName: self.__recordTypeName},
            success: function (html) {
                $('div#IndexFrm').hide();
                $('div#UpdateFrm').html(html).show();
                self.eventFormUpdate()
                hideloadpage();
                scrollTop(1);
            },
            beforeSend: showloadpage()
        })
    },

    editRecord: function() {
        var self = this,
            url = this.urlPath + '/add',
            recordId = '', count = 0;
        $('input[type="checkbox"][name="chk_item_id"]:checked', self.frmIndex).each(function () {
            recordId = $(this).val();
            count++;
        })
        if (count === 0) {
            jWarning('Bạn chưa chọn một hồ sơ nào để sửa!', 'Thông báo');
            return false;
        }
        if (count > 1) {
            jWarning('Bạn chỉ được chọn một hồ sơ để sửa!', 'Thông báo');
            return false;
        }
        
        $.ajax({
            url: url,
            type: "POST",
            data: {recordtype: self.__recordtypeId, recordTypeCode: self.__recordtype, recordTypeName: self.__recordTypeName, recordId: recordId},
            success: function (html) {
                $('div#IndexFrm').hide();
                $('div#UpdateFrm').html(html).show();
                self.eventFormUpdate()
                hideloadpage();
                scrollTop(1);
            },
            beforeSend: showloadpage()
        });
    },

    deleteRecord: function() {
        var self = this,
            url = this.urlPath + '/delete',
            recordId = '', count = 0;
        recordId = $('input[type="checkbox"][name="chk_item_id"]:checked', self.frmIndex).map(function() {
            return this.value;
        }).get();

        if (recordId =='') {
            jWarning('Bạn chưa chọn một hồ sơ nào để XÓA!', 'Thông báo');
            return false;
        }
        jConfirm('Bạn có chắc chắn muốn xóa những hồ sơ này!?', 'Thông báo', function (r) {
            if (r) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {recordId: recordId},
                    dataType: 'json',
                    success: function (resp) {
                        if(resp.error == false) {
                            jAlert(resp.msg, 'Thông báo');
                            self.reLoadData();
                        } else {
                            jWarning(resp.msg, 'Thông báo');
                        }
                        hideloadpage();
                    },
                    beforeSend: showloadpage()
                });
            }
        })
    },
    save: function() {
        var self = this,
            url = this.urlPath + '/save';
        self._savetagxml();
        if (verify(document.forms['frmUpdateRecord'])) {
            if(uploading > 0) {
                showloadpage();
                done2submit = 1;
                return fndone2submit = function() {
                    self.save()
                }
            }
            //Lay lai ngay gio chuan
            dateVerify();
            var sData = getDataAttachFile(self.frmUpdate, 1);
            var data = $(self.frmUpdate).serialize();
            data += sData;
            this.loadding = true;
            $.ajax({
                url: url,
                type: "POST",
                data: data,
                dataType: "json",
                success: function (resp) {
                    self.loadding = false;
                    if(resp.error == false) {
                        jAlert(resp.msg, 'Thông báo');
                        self.reLoadData();
                        $('#UpdateFrm').hide();
                        $('#IndexFrm').show();
                    } else {
                        jWarning(resp.msg, 'Thông báo');
                    }
                    hideloadpage();
                },
                beforeSend: showloadpage()
            });
        }
    },
    loadList: function(callback) {
        var self = this
        if (this.loadding) return false;
        if (typeof (callback) != 'function')  callback = function () {
        }
        this.loadding = true;
        setTimeout(function(){
            self.datagrid(callback)
        }, 300);
    }
})
// Lay du lieu cho danh sach
site_order.prototype.datagrid = function (callback) {
// return;

    var self = this;
    if (typeof(self.objectXml[self.__recordtype]) === 'undefined') {
        self.objectXml[self.__recordtype] = new libXml({
            utilities: true,
            minrow: 10,
            oClass: self,
            autoStart: 0,
            getdata: function () {
                self.reLoadData()
            }
        });

        self.objectXml[self.__recordtype].loadfile = function() {
            var self_xml = this;
            $.ajax({
                type: "GET",
                url: self.urlPath + '/template',
                data: {code: self.__recordtype},
                dataType: "xml",
                success: function (xml) {
                    self_xml.xml = xml;
                }
            })
        }
        self.objectXml[self.__recordtype].loadfile();
    }
    var url = this.urlPath + '/loadlist';
    var oForm = $(this.frmIndex);
    var data = $(oForm).serialize();
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: data,
        success: function (arrResult) {
            eventDropDown(oForm);
            self.objectXml[self.__recordtype].exportTable({
                result: arrResult,
                objupdate: $('div#table-container'),
                form: $(self.frmIndex)
            })
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

