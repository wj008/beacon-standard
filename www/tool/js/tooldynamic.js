(function ($, Yee, layer) {
    function ToolDynamic(element, setting) {
        var that = $(element).hide();
        var showbox = $('<div style="line-height: 36px;"></div>').insertAfter(that);
        var argsspan = $('<span></span>').appendTo(showbox);
        var addbtn = $('<a style="margin-left:10px;" class="form-inp button" href="#">添加</a>').appendTo(showbox);
        $('<a style="margin-left:10px;" class="form-inp button" href="#">显示</a>').appendTo(showbox).click(function () {
            if ($(this).text() === '显示') {
                that.show();
                $(this).text('隐藏');
                return false;
            } else {
                that.hide();
                $(this).text('显示');
                return false;
            }
        });
        var mt1 = $('<select  type="text" class="form-inp select"><option value="">请选择条件</option><option value="eq">相等</option><option value="neq">不相等</option></select>').appendTo(argsspan);
        $('<span style="margin-left:10px;">值：</span>').appendTo(argsspan);
        var mt2 = $('<input  type="text" class="form-inp stext"/>').appendTo(argsspan);
        $('</br>').appendTo(argsspan);
        var mt3 = $('<select  type="text" class="form-inp select"><option value="show">显示字段</option><option value="on">开启验证</option></select>').appendTo(argsspan);
        $('<span style="margin-left:0;">：</span>').appendTo(argsspan);
        var mtshow = $('<input type="text" class="form-inp ltext"/>').appendTo(argsspan);
        $('</br>').appendTo(argsspan);
        var mt4 = $('<select  type="text" class="form-inp select"><option value="hide">隐藏字段</option><option value="off">关闭验证</option></select>').appendTo(argsspan);
        $('<span style="margin-left:0;">：</span>').appendTo(argsspan);
        var mthide = $('<input type="text" class="form-inp ltext"/>').appendTo(argsspan);
        var additem = function (vals) {
            var itemd = $('<div style="line-height:20px;" class="valid_item"></div>').appendTo(showbox);
            $('<span class="valid_item_text"></span>').text(JSON.stringify(vals)).appendTo(itemd);
            $('<a style="margin-left:10px;" href="#">编辑</a>').on('click', function () {
                var oitemd = $(this).parent();
                var ovals = oitemd.data('itdat');
                mt1.val('');
                mt2.val('');
                mt3.val('show');
                mt4.val('hide');
                mtshow.val('');
                mthide.val('');
                for (var key in ovals) {
                    if (key == 'eq') {
                        mt1.val('eq');
                        mt2.val(ovals[key]);
                    }
                    if (key == 'neq') {
                        mt1.val('neq');
                        mt2.val(ovals[key]);
                    }
                    if (key == 'show' || key == 'on') {
                        mt3.val(key);
                        if (typeof ovals[key] == 'string') {
                            mtshow.val(ovals[key]);
                        } else {
                            mtshow.val(ovals[key].join(','));
                        }
                    }
                    if (key == 'hide' || key == 'off') {
                        mt4.val(key);
                        if (typeof ovals[key] == 'string') {
                            mthide.val(ovals[key]);
                        } else {
                            mthide.val(ovals[key].join(','));
                        }
                    }
                }
                addbtn.text('编辑').data('item', oitemd);
                return false;
            }).appendTo(itemd);

            $('<a style="margin-left:10px;" href="#">删除</a>').one('click', function () {
                $(this).parent().remove();
                update();
                return false;
            }).appendTo(itemd);
            itemd.data('itdat', vals);
            update();
        };

        var update = function () {
            var alldat = [];
            var items = showbox.find('div.valid_item');
            if (items.length === 0) {
                that.val('');
            } else {
                items.each(function (index, element) {
                    var itdat = $(element).data('itdat');
                    alldat.push(itdat);
                    that.val(JSON.stringify(alldat));
                });
            }
        };

        addbtn.bind('click', function () {
            var obj = {};
            var val1 = mt1.val();
            var val2 = mt2.val();
            var val3 = mt3.val();
            var val4 = mt4.val();
            var valshow = String(mtshow.val() || '').split(',');
            var valhide = String(mthide.val() || '').split(',');
            if (val1.length === 0) {
                if (layer) {
                    layer.msg('请选择条件');
                } else {
                    alert('请选择条件');
                }
                return false;
            }
            if (val2.length === 0) {
                if (layer) {
                    layer.msg('至少需要填写值信息');
                } else {
                    alert('至少需要填写值信息');
                }
                return false;
            }
            obj[val1] = val2;
            if ($.isArray(valshow) && valshow.length > 0) {
                for (var i = 0; i < valshow.length; i++) {
                    valshow[i] = $.trim(valshow[i]);
                }
                if (val3 == 'show') {
                    obj.show = valshow.join(',');
                } else {
                    obj.on = valshow.join(',');
                }
            }
            if ($.isArray(valhide) && valhide.length > 0) {
                for (var i = 0; i < valhide.length; i++) {
                    valhide[i] = $.trim(valhide[i]);
                }
                if (val4 == 'hide') {
                    obj.hide = valhide.join(',');
                } else {
                    obj.off = valhide.join(',');
                }
            }
            mt1.val('');
            mt2.val('');
            mt3.val('show');
            mt4.val('hide');
            mtshow.val('');
            mthide.val('');

            if ($(this).text() == '编辑') {
                var oitemd = $(this).data('item');
                oitemd.data('itdat', obj);
                $(this).text('添加');
                oitemd.find('span.valid_item_text').text(JSON.stringify(obj));
                update();
                return false;
            }
            additem(obj);
            return false;
        });

        var updatediv = function () {
            showbox.find('div.valid_item').remove();
            try {
                var boxval = that.val();
                var boxdata = JSON.parse(boxval);
                if ($.isArray(boxdata)) {
                    for (var i = 0; i < boxdata.length; i++) {
                        additem(boxdata[i]);
                    }
                }
            } catch (ex) {

            }
        };
        updatediv();
        that.blur(updatediv);
        //!!有待调整
    }

    Yee.loader('css!/tool/css/tooldynamic.css');
    Yee.extend('textarea', 'tooldynamic', ToolDynamic);

})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);