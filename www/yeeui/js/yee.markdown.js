(function ($, Yee) {
    
    var randomString = function (len) {
        var len = len || 32;
        var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
        var maxPos = chars.length;
        var run = '';
        for (var i = 0; i < len; i++) {
            run += chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return run;
    };

    Yee.extend('textarea', 'markdown', function (element, option) {
        var qem = $(element);
        option = $.extend({
            width: "100%",
            height: 640,
            syncScrolling: "single",
            path: "/yeeui/editormd/lib/",
            emoji: true,
            taskList: true,
            tocm: true,                     // Using [TOCM]
            tex: true,                   // 开启科学公式TeX语言支持，默认关闭
            flowChart: true,             // 开启流程图支持，默认关闭
            sequenceDiagram: true,      // 开启时序/序列图支持，默认关闭,
            saveHTMLToTextarea: true,    // 保存 HTML 到 Textarea
            searchReplace: true
        }, option);
        qem.wrap('<div class="markdown-layout"></div>');
        var markdownLayout = qem.parent('.markdown-layout');
        var id = 'markdown-' + randomString('32');
        markdownLayout.attr('id', id);
        var testEditor = editormd(id, option);
    });
})(jQuery, Yee);