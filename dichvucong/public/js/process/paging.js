(function ($) {
    $.fn.process_paging = function (options) {
        var self = this,
            url = baseUrl + '/fee6447a1b671db315059e8e91473833',
            lazyloading = 0;

        var settings = $.extend({
            autoStart: 1,
            changeReload: true,
            gridview: $('#table-container', this),
            checkOverlay: function () {
                return $('.ui-widget-overlay').is(':visible');
            },
            getData: function () {
            },
            switchData: function () {

            },
            bindingReload: function(obj, options) {
                options.obj.reLoadData = function () {
                    obj.callback();
                }
            }
        }, options);

        var initialize = function () {
            defineElement();
            self.callback = function () {
                settings.getData(function (data, index) {
                    getPaging(data, index);
                });
            }
            eventDefine();
            if (settings.autoStart == 1)
                self.callback.apply();
            /*if (settings.changeReload)
                options.obj.reLoadData = function () {
                    self.callback();
                }*/
            settings.bindingReload(self, options);
        };

        function defineElement() {
            self.sdocpertotal = $('#sdocpertotal', self);
            self.generateNumberPage = $('#generateStringNumberPage', self);
            self.selectBoxPage = $('#cbo_nuber_record_page', self);
            self.hdn_current_page = $('#hdn_current_page', self);
            self.hdn_record_number_page = $('#hdn_record_number_page', self);
        }

        function eventDefine() {
            $(self.selectBoxPage).unbind('change').change(function () {
                self.hdn_record_number_page.val($(this).val());
                self.hdn_current_page.val(1);
                $('.s11', self).hide();
                self.callback.apply();
            });
            $(('div#searchIcon, .clsearch'), self).unbind('click').click(function () {
                $('.s11', self).hide();
                self.callback.apply();
            });

            shortcut.remove('Enter');
            shortcut.add("Enter", function (event) {
                if (settings.gridview.is(':visible') && settings.checkOverlay() == false)
                    return self.callback.apply();
                switch(event.target.tagName) {
                    case 'TEXTAREA':
                        $(event.target).val($(event.target).val() + '\n')
                        break;
                    case 'INPUT':
                        if (event.target.type =='button') 
                            $(event.target).trigger('click');
                        break;
                }
            });
            if ($('#tab-menu li', self).length > 0) {
                $('#tab-menu li', self).unbind('click').click(function () {
                    if ($(this).hasClass('active') == false) {
                        $('#namemodeswitch', self).val($(this).attr('v_value'));
                        $('#tab-menu li', self).removeClass('active');
                        $(this).addClass('active');
                        options.obj.reLoadData();
                        $(this).closest('.button-link-container').css('height', 31)
                        settings.switchData($(this).attr('v_value'));
                    }
                })

                var liactive = $('#tab-menu li.active', self).attr('v_value');
                settings.switchData(liactive);
                $('#namemodeswitch', self).val(liactive);

            }
        }

        function _setDefault() {
            self.dsp_owner_code.addClass('tm').removeClass('tp').parent().next().hide();
            self.objProcess.addClass('tm').removeClass('tp').closest('table').next().hide();
            $('.infor_process', self).hide();
            $('[name="chk_next_step"]:checked', self).trigger('change');
        }

        /*
         * Phan trang
         * */
        function getPaging(data, index) {

            var iNumberRecord = 0, numberRow = 0;
            if (data.length > 0) {
                iNumberRecord = data[0][index];
                if (typeof(iNumberRecord) === 'undefined') {
                    iNumberRecord = data.length
                }
                numberRow = data.length;
            }

            var sdocpertotal = "Danh sách này không có hồ sơ nào";
            self.generateNumberPage.html('');
            if (iNumberRecord > 0) {
                if (numberRow > 0)
                    sdocpertotal = "Danh sách có " + numberRow + "/" + iNumberRecord + " hồ sơ.";
                var iRowOnPage = self.hdn_record_number_page.val();
                if (iRowOnPage < iNumberRecord) {
                    var data = {
                        iTotalRecord: iNumberRecord,
                        iPage: self.hdn_current_page.val(),
                        FuntionName: 'pagingModal',
                        JsChangePage: '',
                        iRowOnPage: iRowOnPage
                    }
                    $.ajax({
                        url: url,
                        type: "POST",
                        cache: true,
                        data: data,
                        success: function (string) {
                            self.generateNumberPage.html(string);
                            self.generateNumberPage.find('a.pre, a.pg, a.nex').unbind('click').click(function () {
                                gotopageModal($(this).attr('num'));
                            })
                        }
                    });
                }

            }
            self.sdocpertotal.html(sdocpertotal);
        }

        function gotopageModal(num) {
            self.hdn_current_page.val(num);
            self.callback.apply();
        }

        // Khoi tao
        initialize.call(this);
    }


})(jQuery);