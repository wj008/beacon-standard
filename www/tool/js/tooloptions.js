(function ($, Yee, layer) {

    function ToolOptions(element, setting) {
        var that = $(element);
        var showbox = $('<div></div>').insertAfter(that);
        var argsspan = $('<span></span>').appendTo(showbox);
        var addbtn = $('<a style="margin-left:10px;" class="yee-btn" href="#">添加</a>').appendTo(showbox);
        $('<a style="margin-left:10px;" class="yee-btn" href="#">显示</a>').appendTo(showbox).click(function () {
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

        $('<span style="margin-left:0px;">值：</span>').appendTo(argsspan);
        var mt1 = $('<input  type="text" class="form-inp sstext"/>').appendTo(argsspan);
        $('<span style="margin-left:10px;">文本：</span>').appendTo(argsspan);
        var mt2 = $('<input  type="text" class="form-inp sstext"/>').appendTo(argsspan);
        $('<span style="margin-left:10px;">备注：</span>').appendTo(argsspan);
        var mt3 = $('<input  type="text" class="form-inp sstext"/>').appendTo(argsspan);

        var additem = function (vals) {
            if (typeof (vals) === 'string' || typeof (vals) === 'number') {
                vals = [vals, vals];
            }
            var itemd = $('<div style="line-height:20px;" class="valid_item"></div>').appendTo(showbox);
            $('<span class="valid_item_text"></span>').text(JSON.stringify(vals)).appendTo(itemd);
            $('<a style="margin-left:10px;" href="#">编辑</a>').on('click', function () {
                var oitemd = $(this).parent();
                var ovals = oitemd.data('itdat');
                mt1.val('');
                mt2.val('');
                mt3.val('');
                for (var key in ovals) {
                    if (key == 0) {
                        mt1.val(ovals[key]);
                    }
                    if (key == 1) {
                        mt2.val(ovals[key]);
                    }
                    if (key == 2) {
                        mt3.val(ovals[key]);
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
                });
                that.val(JSON.stringify(alldat));
            }
        };

        addbtn.bind('click', function () {
            var obj = null;
            var val1 = mt1.val();
            var val2 = mt2.val();
            var val3 = mt3.val();
            if (val2.length === 0) {
                alert('至少需要填写文本信息');
                return false;
            }
            if (val3.length === 0) {
                obj = [val1, val2];
            } else {
                obj = [val1, val2, val3];
            }
            mt1.val('');
            mt2.val('');
            mt3.val('');

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
                    for (var key in boxdata) {
                        additem(boxdata[key]);
                    }
                }
            } catch (ex) {

            }
        };
        updatediv();
        that.blur(updatediv);
        //!!有待调整
    }

    Yee.extend('textarea', 'tooloptions', ToolOptions);

})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);
