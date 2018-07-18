// JavaScript Document
(function ($, Yee, layer) {

    var getImg = function (url, show_maxwidth, show_maxheight) {
        var img = $('<img/>');
        img.height(show_maxwidth);
        img.width(show_maxheight);
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
        return img;
    };

    var initUpimgGroup = function (element, options) {

        options = $.extend({
            catSizes: '',
            catType: 0,
            btnWidth: 100,
            btnHeight: 100,
            strictSize: 0,
            size: 0,
            button: null
        }, options || {});

        var qem = $(element).hide();//要填充的输入框
        var size = Number(options.size) || 0;//数量
        //创建id
        var id = qem.attr('id');
        if (!id) {
            id = 'upimgs_' + new Date().getTime();
            qem.attr('id', id);
        }
        var shower = $('<div class="yee-upimggroup-shower clearfix"></div>').insertBefore(qem);
        var button = $('<a href="javascript:;" class="upimggroup-btn" ></a>').appendTo(shower);
        button.width(options.btnWidth).height(options.btnHeight);
        if (qem.setDefault) {
            shower.mouseenter(function () {
                qem.setDefault();
            });
        }

        //跟新值
        var update = function () {
            var imgs = [];
            shower.find('div.yee-upimggroup-item').each(function (index, element) {
                var _this = $(element);
                var dat = _this.data('value');
                imgs.push(dat);
            });
            if (imgs.length === 0) {
                qem.val('');
            }
            else {
                var valstr = JSON.stringify(imgs);
                qem.val(valstr);
            }
            qem.change();
            if (size > 0) {
                if (imgs.length >= size) {
                    button.hide();
                } else {
                    button.show();
                }
            }
        };

        qem.on('reset', function (ev, item) {
            shower.find('div.yee-upimggroup-item').remove();
            if (item && $.isArray(item) && item.length > 0) {
                for (var i = 0, len = item.length; i < len; i++) {
                    addimg(item[i]);
                }
            }
            update();
        });

        var addimg = function (info) {
            if (size > 0 && shower.find('div.yee-upimggroup-item').length >= size) {
                return;
            }
            var host = options.host || '';
            var retUrl = '';
            if (typeof(info) == 'string') {
                retUrl = host + info || '';
            } else {
                host = options.host || info.host || '';
                retUrl = host + (info.url || '');
            }
            if (options.showSize) {
                var retUrl = retUrl.replace(/(\.[a-z]+)$/, function ($0, $1) {
                    return '_' + options.showSize + $1;
                });
            }
            var img = getImg(retUrl, options.btnWidth, options.btnHeight);
            var oitem = $('<div class="yee-upimggroup-item"><table  border="0" cellspacing="0" cellpadding="0"><tr><td style="padding:0px; vertical-align:middle; text-align:center; line-height:0px;"></td></tr></table></div>').data('value', retUrl);
            var table = oitem.find('table');
            var td = oitem.find('td');
            td.append(img);
            oitem.width(options.btnWidth).height(options.btnHeight);
            table.width(options.btnWidth).height(options.btnHeight);

            var delBtn = $('<a href="javascript:void(0);"></a>').addClass('yee-upimggroup-delpic').appendTo(oitem);
            delBtn.click(function () {
                $(this).parent('.yee-upimggroup-item').remove();
                update();
            });
            oitem.insertBefore(button);
            update();
        };
        var valText = qem.val() || '[]';
        var vals = [];
        if (valText !== '' && valText !== 'null') {
            try {
                vals = JSON.parse(valText);
            } catch (e) {
                vals = [];
            }
        }

        for (var i = 0; i < vals.length; i++) {
            var item = {url: vals[i]};
            addimg(item);
        }

        button.yee_upfile(options);
        button.on('completeUpload', function (ev, context) {
            if (!context.status) {
                if (context.error !== '') {
                    layer.alert(context.error);
                }
                return;
            }
            if (context.data && context.data.files && $.isArray(context.data.files)) {
                for (var i = 0; i < context.data.files.length; i++) {
                    addimg(context.data.files[i]);
                }
            } else if (context.data) {
                addimg(context.data);
            }
            if (context.message) {
                layer.msg(context.message);
            }
        });

    };

    Yee.extend('input', 'upimggroup', initUpimgGroup);

})(jQuery, Yee, layer);