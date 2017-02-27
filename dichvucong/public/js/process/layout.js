(function ($) {
    $.fn.process_layout = function (options) {
        var self = this,
            url_getcount = baseUrl + '/main/reminder/getnotify';

        var settings = $.extend({
            autoStart: 1,
            timeReconnect: 360000,
            timeRefresh: 60000
        }, options);
        var __time = 0, __count = 0
        var initialize = function () {
            documentEvent();
            if (settings.autoStart) {
                getNotify({t: 0})
            }
            setInterval(function() {
                        getNotify({t: 0});
                    }, settings.timeReconnect);
        };

        function checktime() {
            var t = $.now() - __time;
            return (t > settings.timeRefresh ? 1 : 0);
        }
        function documentEvent()
        {
            $('.icon_bell', self).click(function(){
                if ($('#noti_box', self).is(':visible') == false && checktime()) {
                    getNotify({t: 1});
                }
                $('#noti_box', self).toggle();
            })

            $('#noti_box,.icon_bell', self).click(function (event) {
                $('.gb_u,#gb', self).hide();
                event.stopPropagation();
            })

            $('#accout_info', self).click(function () {
                $('.gb_u,#gb', self).toggle();
            })
            $('body').click(function (event) {
                $('.gb_u,#gb,#noti_box', self).hide();
            })
            $('.gb_u,#gb,#accout_info', self).click(function (event) {
                $('#noti_box', self).hide();
                event.stopPropagation();
            });
            // 
            $('p.file_export_close').click(function () {
                $('div#note-process').removeClass('vY nq');
                $('div#note-process,div#file_export').hide();
            });

            $('#noti_box .view_all a', self).click(function() {
                var url = $(this).attr('href');
                $('#noti_box', self).toggle();
                if (history.pushState) history.pushState("", "", url);
                showloadpage();
                ga('send', 'pageview', url);
                $("#wrapper div.main_content").load(url, {}, function (string) {
                    removeEventOld();
                    hideloadpage();
                });
                return false;
            })
        }
        function getNotify(data, callback) {
            if (typeof(callback) != 'function') 
                    callback = function(){};
            if (window.__isActive == false) return;
            
            $.ajax({
                    url: url_getcount,
                    type: "GET",
                    data: data,
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.type) {
                            __time = $.now();
                            updateview(resp.data)
                            event_handle()                            
                        } else {
                            __time = (__count != resp.data ? 0 : $.now())
                            setCount(resp.data)
                        }                        
                        callback.call(this);
                    }
                })
        }

        function event_handle() {
            $('.tasklist a.task',self).click(function() {
                var resource = $(this).attr('res'),
                    tagLi = $('li#' + resource),
                    url = $(this).attr('href'),
                    params = JSON.parse($.base64('decode', $(this).attr('params')));
                $('#noti_box').toggle();
                if (history.pushState) history.pushState("", "", url);
                showloadpage();
                $(tagLi).trigger('click');
                $("#wrapper div.main_content").load(url, params, function (string) {
                    removeEventOld();
                    hideloadpage();
                });
                return false;
            })
        }

        function updateview(result) {
            var html = '<ul class="tasklist">', number_noti = 0;
            if (result.length > 0) {
                for (var i = 0; i < result.length; i++) {  
                    html += '<li><a class="task" href="'+ result[i]['href'] +'" res="'+ result[i]['resource'] +'" params="'+ result[i]['params'] +'">';
                    html += '<p class="title">Xử lý đơn</p>';
                    html += '<p class="text">'+ result[i]['text'] +'</p>';
                    html += '</a></li>';
                    
                    number_noti += parseInt(result[i]['number'])
                }
            }
            html += '</ul>';
            $('.noti #noti_box ._33p .list', self).html(html);
            __count = number_noti;
            setCount(number_noti)
        }

        function setCount(number) {
            var html = '';
            if (number > 0) 
                html = '<p class="number_noti">' + number + '</p>';
            $('.noti .icon_bell', self).html(html);
        }

        // Khoi tao
        initialize.call(this);
    }


})(jQuery);

(function ($) {
    $.fn.gmenu = function (options) {
        var self = this,
            $targetMenu = $(".list_categories .row p.title", self),
            $targetSubMenu = $(".list_categories .row ul.list li a", self);
        var settings = $.extend({
            callbackInit: function(){}
        }, options);
        var initialize = function () {
            _event();
            settings.callbackInit()
        }
        function _event() {
            $('.list_categories .row p.title', self).click(function () {
                $targetMenu.removeClass('changeColor')
                $(this).addClass("changeColor");
                $('.list_categories .row ul.list').hide('wiggle');
                $(this).next().toggle('wiggle');
            });
            $targetSubMenu.click(function() {
                var url = $(this).attr('href');
                if (history.pushState) history.pushState("", "", url);
                showloadpage();
                $(this).closest('li').trigger('click');
                ga('send', 'pageview', url);
                $("#wrapper div.main_content").load(url, {}, function (string) {
                    removeEventOld();
                    hideloadpage();
                });
                return !history.pushState;
            })

            $(".list_categories .row ul.list li", self).click(function() {
                var targetLink = $(this).find('a');
                $targetSubMenu.removeClass('active');
                $(targetLink).addClass('active');

                if ($(targetLink).closest('.list').is(':visible') == false) {
                    $(targetLink).closest('div.row').find('p.title').addClass("changeColor").trigger('click');
                }
            })
        }

        // Khoi tao
        initialize.call(this);
    }


})(jQuery);