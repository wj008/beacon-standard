(function ($, Yee, layer) {

    var setImgWH = function (img, url, show_maxwidth, show_maxheight) {

        var imgtemp = new Image();
        imgtemp.onload = function () {
            var width = imgtemp.width;
            var height = imgtemp.height;
            if (width > show_maxwidth) {
                var pt = show_maxwidth / width;//高比宽
                width = show_maxwidth;
                height = height * pt;
            }
            if (height > show_maxheight) {
                var pt = show_maxheight / height;//宽比高
                height = show_maxheight;
                width = width * pt;
            }
            img.height(Math.round(height));
            img.width(Math.round(width));
            img.attr('src', url);
        };
        imgtemp.src = url;
    };

    var oldValFunc = $.fn.val;
    $.fn.val = function (val) {
        var that = $(this);
        if (typeof (val) === 'undefined') {
            return oldValFunc.call(this);
        } else {
            var instance = that.getModuleInstance('upimage');
            if (instance) {
                if (val != '') {
                    instance.showImg(val);
                } else {
                    instance.hideImg();
                }
            }
            return oldValFunc.call(this, val);
        }
    };

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
            var table = $('<table  border="0" cellspacing="0" cellpadding="0"><tr><td style="padding:0px; vertical-align:middle; text-align:center; overflow: hidden; line-height:0px;"></td></tr></table>').appendTo(button);
            table.width(options.btnWidth);
            table.height(options.btnHeight);
            var delBtn = $('<a href="javascript:void(0);"></a>').addClass('up_image_delpic').hide().appendTo(btnLayout);
            var image = $('<img title="请选择上传图片"/>').appendTo(table.find('td'));
            var showImg = this.showImg = function (url) {
                setImgWH(image, url, options.btnWidth, options.btnHeight);
                table.show();
                delBtn.show();
            }
            var hideImg = this.hideImg = function (url) {
                table.hide();
                delBtn.hide();
            }
            delBtn.click(function () {
                qem.val('');
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