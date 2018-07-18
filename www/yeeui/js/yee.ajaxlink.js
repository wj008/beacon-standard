(function ($, Yee, layer) {

    var fromTimeout = true;
    //AJAX提交连接
    Yee.extend('a', 'ajaxlink', function (elem) {
        var qem = $(elem);
        if (qem.attr('onsuccess')) {
            var code = qem.attr('onsuccess');
            var func = new Function('ev', 'ret', code);
            qem.on('success', func);
        }
        if (qem.attr('onfail')) {
            var code = qem.attr('onfail');
            var func = new Function('ev', 'ret', code);
            qem.on('fail', func);
        }
        if (qem.attr('onback')) {
            var code = qem.attr('onback');
            var func = new Function('ev', 'ret', code);
            qem.on('back', func);
        }
        if (qem.attr('onbefore')) {
            var code = qem.attr('onbefore');
            var func = new Function('ev', code);
            qem.on('onbefore', func);
        }
        var send = function (url) {
            //防止误触双击
            if (!fromTimeout) {
                return false;
            }
            fromTimeout = false;
            setTimeout(function () {
                fromTimeout = true;
            }, 1000);
            var option = $.extend({
                method: 'get',
            }, qem.data() || {});
            option.url = url;
            var args = Yee.parseUrl(url);
            args.path = args.path || window.location.pathname;
            option.path = args.path;
            option.prams = args.prams;
            option.cache = false;
            //如果有携带值
            if (option['carry']) {
                var carry = $(option['carry']);
                if (carry.length > 0) {
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
                        option.prams[name] = val;
                    });
                }
            }
            if (option['batch']) {
                var batch = $(':checkbox[name="' + option['batch'] + '"]:checked');
                if (batch.length == 0) {
                    layer.msg('没有选择任何选项.', {icon: 7, time: 1000});
                    return;
                }
                var batchItem = [];
                batch.each(function (idx, el) {
                    var qel = $(el);
                    batchItem.push(qel.val());
                });
                option.prams[option['batch']] = batchItem.join(',');
            }
            if (qem.triggerHandler('before', [option]) === false) {
                return;
            }
            $.ajax({
                type: option.method,
                url: option.path,
                data: option.prams,
                cache: option.cache,
                dataType: 'json',
                success: function (ret) {
                    if (qem.triggerHandler('back', [ret]) === false) {
                        return;
                    }
                    //拉取数据成功
                    if (ret.status === true) {
                        if (qem.triggerHandler('success', [ret]) === false) {
                            return;
                        }
                        if (ret.message && typeof (ret.message) === 'string') {
                            layer.msg(ret.message, {icon: 1, time: 1000});
                        }
                    }
                    //拉取数据错误
                    if (ret.status === false) {
                        if (qem.triggerHandler('fail', [ret]) === false) {
                            return;
                        }
                        if (ret.error && typeof (ret.error) === 'string') {
                            layer.msg(ret.error, {icon: 0, time: 2000});
                        }

                    }
                }
            });
        };
        qem.on('send', function (ev, url) {
            send(url);
        });
        qem.on('click', function (ev) {
            var that = $(this);
            if (that.is('.disabled') || that.is(':disabled')) {
                return false;
            }
            if (ev.result === false) {
                return false;
            }
            var url = $(this).data('href') || $(this).attr('href');
            send(url);
            return false;
        });

    });

})(jQuery, Yee, layer);