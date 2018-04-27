(function ($, Yee, layer) {
    var Index = 0;
    Yee.extend('div', 'ajax_plugin', function (element, option) {
        var qem = $(element);
        var bindParam = option['bindParam'] || '';
        var autoLoad = option['autoLoad'] || false;
        option.value = option['value'] || null;

        function send(url, postData) {
            var args = Yee.parseUrl(url);
            if (bindParam) {
                var form = qem.parents('form:first');
                var arrtemp = bindParam.split(',');
                for (var i = 0; i < arrtemp.length; i++) {
                    var xname = arrtemp[i];
                    var sle = ':input[name=' + xname + ']';
                    var qel = form.find(sle);
                    if (qel.length > 0) {
                        if (!qel.is(':input')) {
                            return;
                        }
                        var val = qel.val() || '';
                        if (qel.is(':radio')) {
                            var ibox = form.find(':radio[name="' + xname + '"]:checked');
                            val = ibox.val() || '';
                        }
                        if (qel.is(':checkbox')) {
                            val = qel.is(':checked') ? qel.val() : '';
                        }
                        args.prams[xname] = val;
                    } else {
                        var xAars = Yee.parseUrl(window.location.search);
                        if (xAars.prams[xname] !== void 0) {
                            args.prams[xname] = xAars.prams[xname];
                        }
                    }
                }
            }
            url = Yee.toUrl(args);
            $.post(url, postData || null, function (ret) {
                if (ret.status == false) {
                    if (ret.error && typeof (ret.error) === 'string') {
                        layer.msg(ret.error, {icon: 0, time: 3000});
                    }
                    return;
                }
                //拉取数据成功
                if (ret.status) {
                    qem.empty();
                    if (ret.data && typeof ret.data == 'string') {
                        qem.html(ret.data);
                        Yee.update(qem);
                    }
                }
            }, 'json');
        }

        qem.on('load', function (ev, url, postData) {
            url = url || option.url;
            if (!url) {
                layer.msg('url参数未指定', {icon: 0, time: 3000});
                return;
            }
            send(url, postData);
        });

        if (autoLoad) {
            send(option.url, option.value);
        }

    });
})(jQuery, Yee, layer);