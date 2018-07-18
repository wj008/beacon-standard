(function ($, Yee, layer) {

    function UpFile(element, options) {

        options = $.extend({
            input: null,
            button: null,
            token: '',
            domain: '',
        }, options || {});
        var qem = $(element);
        var button = null;
        if (options.button) {
            button = $(options.button);
        } else if (qem.is('input')) {
            button = $('<a  href="javascript:;" class="yee-upfile-btn">选择文件</a>');
            button.insertBefore(qem);
            if (qem.is(':visible')) {
                qem.addClass('not-radius-left');
                button.addClass('not-radius-right');
            }
        } else {
            button = qem;
        }
        var bindBox = options.input ? $(options.input) : null;
        options.bindData = options.bindData || {};
        options.bindData['token'] = options.token;

        var indexLayer = null;

        qem.on('beforeUpload', function (ev) {
            indexLayer = layer.load(0, {shade: false});
        });
        qem.on('completeUpload', function (ev, context) {
            if (indexLayer !== null) {
                layer.close(indexLayer);
                indexLayer = null;
            }
            if (context.key) {
                if (options.domain.substr(-1) != '/') {
                    options.domain = options.domain + '/';
                }
                var url = options.domain + context.key;
                if (qem.is('input')) {
                    qem.val(url);
                }
                if (bindBox) {
                    bindBox.val(url);
                }
                layer.msg('上传成功');
            } else {
                if (context.error) {
                    layer.alert('上传失败:' + context.error);
                } else {
                    layer.alert('上传失败');
                }
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

    Yee.extend('input,a', 'qiniu', UpFile);

})(jQuery, Yee, layer);