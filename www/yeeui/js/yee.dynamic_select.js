(function ($, Yee, layer) {

    var cacheOptions = {};
    var oldValFunc = $.fn.val;
    $.fn.val = function (val) {
        var that = $(this);
        if (typeof (val) === 'undefined') {
            if (that.get(0) && that.get(0).yee_modules && that.get(0).yee_modules['dynamic_select']) {
                return oldValFunc.call(this) || that.data('value') || '';
            } else {
                return oldValFunc.call(this);
            }
        } else {
            if (that.get(0) && that.get(0).yee_modules && that.get(0).yee_modules['dynamic_select']) {
                that.data('value', val);
            }
            return oldValFunc.call(this, val);
        }
    };

    Yee.extend('select', 'dynamic_select', function (element, setting) {
        var qem = $(element);
        element.yee_dynamic_select = true;
        var timer = null;
        var header = setting.header || '';
        var options = setting.options || null;
        var onchange = function () {
            var selected = qem.children(':selected');
            if (selected.length > 0) {
                qem.data('value', selected[0].value);
            }
        };
        var initBox = function (items) {
            if (items !== null) {
                var has = false;
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    var obj = {};
                    if (typeof (item) === 'number' || typeof (item) === 'string') {
                        obj.value = item;
                        obj.text = item;
                    } else {
                        if (typeof (item.value) !== 'undefined') {
                            obj.value = item.value;
                        } else if (typeof (item[0]) !== 'undefined') {
                            obj.value = item[0];
                        } else {
                            continue;
                        }
                        if (typeof (item.text) !== 'undefined') {
                            obj.text = item.text;
                        } else if (typeof (item[1]) !== 'undefined') {
                            obj.text = item[1];
                        } else {
                            obj.text = obj.value;
                        }
                    }
                    if (element.length == 1 && (obj.value === null || obj.value === '')) {
                        element.length = 0;
                        obj.value = '';
                    }
                    var optitem = new Option(obj.text, obj.value);
                    element.add(optitem);
                    var boxval = qem.data('value') || '';
                    if (boxval == obj.value) {
                        optitem.selected = true;
                        has = true;
                    }
                }
                if (!has) {
                    qem.data('value', '');
                }
            }
        };
        var createBox = function (items) {
            element.length = 0;
            //添加头部
            if (header) {
                if (typeof header == 'string') {
                    element.add(new Option(header, ''));
                } else if (typeof header == 'object' && header.text) {
                    element.add(new Option(header.text, header.value || ''));
                } else if ($.isArray(header) && header.length >= 2) {
                    element.add(new Option(header[1], header[0]));
                }
            }
            if ($.type(items) === 'string') {
                if (cacheOptions[items]) {
                    createBox(cacheOptions[items]);
                    return;
                }
                $.get(items, function (ret) {
                    if (ret && ret.status) {
                        cacheOptions[items] = ret.data;
                        createBox(data);
                    } else {
                        if (layer) {
                            if (ret.error && typeof (ret.error) === 'string') {
                                layer.msg(ret.error, {icon: 0, time: 3000});
                            } else {
                                layer.msg('无法加载远程数据！', {icon: 0, time: 3000});
                            }
                        }
                    }
                }, 'json');
                return;
            }
            initBox(items);
        };
        qem.on('update', function (ev) {
            if (timer) {
                window.clearTimeout(timer);
                timer = null;
            }
            cacheOptions = {};
            options = qem.data('options') || null;
            createBox(options);
        });
        qem.on('change', onchange);
        timer = window.setTimeout(function () {
            createBox(options);
        }, 10);
    });

})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);
