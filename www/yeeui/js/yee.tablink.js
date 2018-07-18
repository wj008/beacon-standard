(function ($, Yee) {

    Yee.extend('ul', 'tablink', function (elem) {
        var qem = $(elem);
        var lis = qem.find('li');
        lis.each(function (idx, el) {
            var a = $(el).find('a');
            var tabIndex = a.data('tab-index') || null;
            if (tabIndex !== null) {
                var href = a.attr('href');
                var ainfo = Yee.parseUrl(href);
                ainfo.prams['tabIndex'] = tabIndex;
                href = Yee.toUrl(ainfo);
                a.attr('href', href);
            }
        });
    });
})(jQuery, Yee);