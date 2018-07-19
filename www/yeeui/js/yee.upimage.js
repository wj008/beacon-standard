(function ($, Yee, layer) {


    function createImage(url, width, height) {
        var def = $.Deferred();
        var imgtemp = new Image();
        imgtemp.onload = function () {
            var w = imgtemp.width;
            var h = imgtemp.height;
            if (w > width) {
                var pt = width / w;//高比宽
                w = width;
                h = h * pt;
            }
            if (h > height) {
                var pt = height / h;//宽比高
                h = height;
                w = w * pt;
            }
            var img = $('<img/>');
            img.height(Math.round(h));
            img.width(Math.round(w));
            img.attr('src', url);
            var table = $('<table  border="0" cellspacing="0" cellpadding="0"><tr><td style="padding:0px; vertical-align:middle; text-align:center; overflow: hidden; line-height:0px;"></td></tr></table>');
            table.width(width);
            table.height(height);
            table.find('td').append(img);
            def.resolve(table);
        };
        imgtemp.onerror = function () {
            def.reject('图片加载失败..');
        };
        imgtemp.src = url;
        return def;
    }

    function UpImage(element, options) {
        var qem = $(element);
        options = $.extend({
            catSizes: '',
            catType: 0,
            btnWidth: 150,
            btnHeight: 100,
            strictSize: 0,
            button: null
        }, options || {});

        if (qem.is('img')) {
            options.btnWidth = qem.width();
            options.btnHeight = qem.height();
        }

        var bindData = {};
        bindData.catSizes = options.catSizes || null;
        bindData.catType = options.catType || null;
        bindData.strictSize = options.strictSize || null;
        options.bindData = bindData;

        if (qem.is('input')) {
            qem.hide();
            qem.parent().wrapInner('<div></div>');
            var boxLayout = qem.parent();
            var btnLayout = $('<div class="up_image_layout"></div>');
            btnLayout.insertBefore(boxLayout);
            var button = $('<a class="up_image_btn" href="javascript:;" style="display: inline-block;"></a>').appendTo(btnLayout);
            button.width(options.btnWidth).height(options.btnHeight);
            options.button = button;

            var delBtn = $('<a href="javascript:void(0);"></a>').addClass('up_image_delpic').hide().appendTo(btnLayout);

            var showImg = function (url) {
                createImage(url, options.btnWidth, options.btnHeight).then(function (img) {
                    button.empty().append(img);
                    delBtn.show();
                }).fail(function (error) {
                    console.log(error);
                });
            }

            var hideImg = function () {
                button.empty();
                delBtn.hide();
            }

            delBtn.click(function () {
                qem.val('');
                hideImg();
            });

            if (qem.val() == '') {
                hideImg();
            } else {
                var val = qem.val();
                showImg(val);
            }

            var bindBox = options.input ? $(options.input) : null;
            qem.on('displayError', function (ev, data) {
                button.addClass('error');
            });
            qem.on('displayDefault displayValid', function (ev, data) {
                button.removeClass('error');
            });
            button.on('mouseenter', function () {
                if (typeof(qem.setDefault) == 'function') {
                    qem.setDefault();
                }
            });

            qem.on('completeUpload', function (ev, context) {
                if (!context.status) {
                    if (context.error !== '') {
                        layer.alert(context.error);
                    }
                    return;
                }
                if (qem.is('input')) {
                    qem.val(context.data.url);
                    showImg(context.data.url);
                } else {
                    showImg(context.data.url);
                }
                if (bindBox) {
                    bindBox.val(url);
                }
                if (context.message) {
                    layer.msg(context.message);
                }
            });
            if (typeof FormData == 'function') {
                button.on('click', function () {
                    qem.triggerHandler('upload');
                });
                new Yee.Html5Upload(qem, options);
            } else {
                new Yee.FrameUpload(qem, button, options);
            }
        }
        if (qem.is('img')) {
            var bindBox = options.input ? $(options.input) : null;
            qem.on('completeUpload', function (ev, context) {
                if (!context.status) {
                    if (context.error !== '') {
                        layer.alert(context.error);
                    }
                    return;
                }
                qem.attr('src', context.data.url);
                if (bindBox) {
                    bindBox.val(url);
                }
                if (context.message) {
                    layer.msg(context.message);
                }
            });
            if (typeof FormData == 'function') {
                qem.on('click', function () {
                    qem.triggerHandler('upload');
                });
                new Yee.Html5Upload(qem, options);
            } else {
                new Yee.FrameUpload(qem, qem, options);
            }
        }
    }

    Yee.extend('input,img', 'upimage', UpImage);
})(jQuery, Yee, layer);