(function ($, Yee, layer) {
    Yee.extend('a', 'batch', function (elem, option) {
        var qem = $(elem);
        option = option || {};
        qem.on('click', function (ev) {
            var that = $(this);
            if (that.is('.disabled') || that.is(':disabled')) {
                return false;
            }
            if (ev.result === false) {
                return false;
            }
            var url = $(this).data('href') || $(this).attr('href');
            var qurey = Yee.parseUrl(url);
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
                qurey.prams[option['batch']] = batchItem.join(',');
            }
            $(this).attr('href', Yee.toUrl(qurey));
            return true;
        });
    });
})(jQuery, Yee, layer);