(function ($, Yee, layer) {

    Yee.extend('*', 'list', function (elem, option) {
        var qem = $(elem);
        option = $.extend({method: 'get', showMsg: false, autoLoad: false, sendOrder: false, bindForm: null, fillBottom: null}, option);
        if (!option.url) {
            option.autoUrl = 1;
            if (/\/$/.test(String(window.location.pathname))) {
                option.url = window.location.pathname + 'index.json' + window.location.search;
            } else {
                option.url = window.location.pathname + '.json' + window.location.search;
            }
        }
        var lastSendOption = null;
        var bind = option.bind || null;
        var send = function (sendOption) {
            //防止误触双击
            sendOption = $.extend(option, sendOption || {});
            lastSendOption = sendOption;
            var query = sendOption.url;
            var args = Yee.parseUrl(sendOption.url);
            if (option.autoUrl == 1 && typeof(window.history.replaceState) != 'undefined') {
                var thisUrl = Yee.toUrl({path: args.path.replace(/\.json$/i, ''), prams: args.prams});
                window.history.replaceState(null, document.title, thisUrl);
            }
            args.path = args.path || window.location.pathname;
            if (qem.triggerHandler('before', [sendOption]) === false) {
                return;
            }
            $.ajax({
                type: sendOption.method,
                url: args.path,
                data: args.prams,
                cache: false,
                dataType: 'json',
                success: function (ret) {
                    if (qem.triggerHandler('back', [ret]) === false) {
                        return;
                    }
                    //拉取数据成功
                    if (ret.status === true) {
                        if (sendOption.showMsg && layer && ret.message && typeof (ret.message) === 'string') {
                            layer.msg(ret.message, {icon: 1, time: 1000});
                        }
                        if (ret.data) {
                            qem.triggerHandler('source', [ret.data, query || ""]);
                            if (bind != null) {
                                $(bind).triggerHandler('source', [ret.data, query || ""]);
                            }
                        }
                        qem.triggerHandler('success', [ret]);
                    }
                    //拉取数据错误
                    if (ret.status === false) {
                        if (sendOption.showMsg && layer && ret.error && typeof (ret.error) === 'string') {
                            layer.msg(ret.error, {icon: 0, time: 2000});
                        }
                        qem.triggerHandler('error', [ret]);
                    }
                }
            });
        };
        var vueApps = [];
        qem.children('*').each(function (idx, item) {
            var app = new Vue({
                el: item,
                replace: false,
                data: {
                    list: [],
                },
            });
            vueApps.push(app);
        });
        qem.on('source', function (ev, source) {
            var data = qem.triggerHandler('filter', [source]);
            if (data !== void 0) {
                source = data;
            }
            if (vueApps.length > 0) {
                for (var i = 0; i < vueApps.length; i++) {
                    var app = vueApps[i];
                    if (source.list !== void 0) {
                        app.list = source.list;
                    }
                }
                app.$nextTick(function () {
                    setTimeout(function () {
                        Yee.update(qem);
                        qem.triggerHandler('change', [source]);
                    }, 10);
                });
            }
        });
        qem.on('load', function (ev, sendOption) {
            if (sendOption == null) {
                sendOption = option;
            }
            else if (typeof(sendOption) == 'string') {
                sendOption = {url: sendOption};
            }
            send(sendOption);
        });
        qem.on('reload', function (ev, showMsg) {
            var sendOption = $.extend(lastSendOption, {showMsg: showMsg});
            send(sendOption);
        });
        qem.on('reset', function (ev, showMsg) {
            var sendOption = $.extend(option, {showMsg: showMsg});
            send(sendOption);
        });
        $(function () {
            if (option.autoLoad) {
                qem.triggerHandler('load');
            }
        });
    });


})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);