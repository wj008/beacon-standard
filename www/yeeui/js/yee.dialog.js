(function ($, Yee, layer) {
    window.openYeeDialog = function (url, title, option, callwin, qelem) {
        callwin = callwin || window;
        if (window.top != window) {
            if (window.top.openYeeDialog) {
                window.top.openYeeDialog(url, title, option, callwin, qelem);
            } else {
                if (window.top.Yee) {
                    window.top.Yee.loader('yee-dialog', function () {
                        window.top.openYeeDialog(url, title, option, callwin, qelem);
                    });
                }
            }
            return;
        }
        title = title || '网页对话框';
        option = option || {};
        option.width = option.width || 1060;
        option.height = option.height || 720;
        var winW = $(window).width() - 20;
        var winH = $(window).height() - 20;
        option.width = option.width > winW ? winW : option.width;
        option.height = option.height > winH ? winH : option.height;
        var iframe = null;
        var dialogWindow = null;
        //携带参数
        if (option['carry']) {
            var carry = $(option['carry']);
            if (carry.length > 0) {
                var args = Yee.parseUrl(url);
                args.path = args.path || window.location.pathname;
                option.path = args.path;
                carry.each(function (idx, el) {
                    var qel = $(el);
                    if (!qel.is(':input')) {
                        return;
                    }
                    var name = qel.attr('name') || qel.attr('id') || '';
                    if (name == '') {
                        return;
                    }
                    var val = qel.val() || '';
                    if (qel.is(':radio')) {
                        var xname = qel.attr('name');
                        var xform = qel.parents('form:first');
                        var ibox = xform.find(':radio[name="' + xname + '"]:checked');
                        val = ibox.val() || '';
                    }
                    if (qel.is(':checkbox')) {
                        val = qel.is(':checked') ? qel.val() : '';
                    }
                    args.prams[name] = val;
                });
                url = Yee.toUrl(args);
            }
        }
        var layIndex = layer.open({
            type: 2,
            title: title,
            area: [option.width + 'px', option.height + 'px'],
            maxmin: option.maxmin === void 0 ? true : option.maxmin,
            content: url,
            fixed: false,
            end: function () {
                if (callwin.jQuery) {
                    callwin.jQuery(callwin).triggerHandler('closeYeeDialog', [option]);
                }
                if (qelem) {
                    if (dialogWindow && dialogWindow.returnValue !== void 0) {
                        qelem.triggerHandler('dlgclose', [dialogWindow.returnValue]);
                    } else {
                        qelem.triggerHandler('dlgclose', [null]);
                    }
                }
                if (iframe != null) {
                    iframe.remove();
                    iframe = null;
                }
            },
            success: function (layero, index) {
                dialogWindow = null;
                layero.find('.layui-layer-content').css('height', 'calc(100% - 43px)');
                iframe = layero.find('iframe');
                iframe.css('height', '100%');
                if (iframe.length > 0) {
                    var winName = iframe[0].name;
                    dialogWindow = window[winName];
                }
                if (dialogWindow) {
                    dialogWindow.emit = function (event, data) {
                        if (callwin.jQuery) {
                            callwin.jQuery(callwin).triggerHandler(event, [data]);
                        }
                    }
                    dialogWindow.trigger = function (event, data) {
                        if (qelem) {
                            qelem.triggerHandler(event, [data]);
                        }
                    }
                    dialogWindow.success = function (data) {
                        if (qelem) {
                            qelem.triggerHandler('success', [data]);
                        }
                    }
                    dialogWindow.closeYeeDialog = function () {
                        layer.close(layIndex);
                    };
                    if (!(dialogWindow.document.title === null || dialogWindow.document.title === '')) {
                        layer.title(dialogWindow.document.title, index);
                    }
                    //准备好了
                    var tryIndex = 0;
                    var readyFunc = function () {
                        if (typeof dialogWindow.readyYeeDialog == 'function') {
                            if (option.assign !== void 0) {
                                dialogWindow.readyYeeDialog(option.assign, callwin, qelem);
                            } else {
                                dialogWindow.readyYeeDialog(null, callwin, qelem);
                            }
                        } else {
                            tryIndex++;
                            if (tryIndex < 50) {
                                setTimeout(readyFunc, 100);
                            }
                        }
                    }
                    readyFunc();
                    dialogWindow.YeeDialog = true;
                }
            }
        });
    };
    Yee.extend('a', 'dialog', function (elem) {
        var qem = $(elem);
        if (qem.attr('onsuccess')) {
            var code = qem.attr('onsuccess');
            var func = new Function('ev', 'ret', code);
            qem.on('success', func);
        }
        if (qem.attr('ondata')) {
            var code = qem.attr('ondata');
            var func = new Function('ev', 'ret', code);
            qem.on('data', func);
        }
        if (qem.attr('ondlgclose')) {
            var code = qem.attr('ondlgclose');
            var func = new Function('ev', 'ret', code);
            qem.on('dlgclose', func);
        }
        if (qem.attr('onbefore')) {
            var code = qem.attr('onbefore');
            var func = new Function('ev', code);
            qem.on('before', func);
        }
        qem.on('click', function (ev) {
            var that = $(this);
            if (that.is('.disabled') || that.is(':disabled')) {
                return false;
            }
            if (that.triggerHandler('before') === false) {
                return;
            }
            var url = that.data('href') || that.attr('href');
            var title = that.attr('title') || '';
            var option = $.extend({
                height: 720,
                width: 1060
            }, that.data() || {});
            window.openYeeDialog(url, title, option, window, qem);
            ev.preventDefault();
            return false;
        });
    });
})(jQuery, Yee, layer);