(function ($, Yee) {

    Array.prototype.hasValue = function (val) {
        if (typeof key == 'string' || typeof key == 'number' || typeof key == 'boolean') {
            for (var k in this) {
                var val = this[k];
                if (val == key) {
                    return true;
                }
            }
            return false;
        } else {
            for (var k in this) {
                var val = this[k];
                if (JSON.stringify(val) == JSON.stringify(key)) {
                    return true;
                }
            }
            return false;
        }
    };

    Yee.dynamic = function (type, names) {
        if (typeof(names) == 'string') {
            names = [names];
        }
        $(names).each(function (i, name) {
            var rowId = ('#row_' + name).replace(/(:|\.)/g, '\\$1');
            var boxId = ('#' + name).replace(/(:|\.)/g, '\\$1');
            if (type == 'show') {
                $(rowId).show();
                $(rowId + ' :input').data('val-off', false);
            }
            else if (type == 'hide') {
                $(rowId).hide();
                $(rowId + ' :input').data('val-off', true);
                if ($(rowId + ' :input').setDefault) {
                    $(rowId + ' :input').setDefault();
                }
            }
            else if (type == 'on') {
                $(boxId).data('val-off', false);
            }
            else if (type == 'off') {
                $(boxId).data('val-off', true);
                if ($(boxId).setDefault) {
                    $(boxId).setDefault();
                }
            }
        });
    }

    Yee.extend(':input', 'dynamic', function (elem, option) {
        var qelem = $(elem);
        var dynamicfun = function (item) {
            //显示
            if (item.show !== void 0) {
                Yee.dynamic('show', item.show);
            }
            //隐藏
            if (item.hide !== void 0) {
                Yee.dynamic('hide', item.hide);
            }
            //关闭验证
            if (item['off-val'] !== void 0) {
                Yee.dynamic('off', item['off-val']);
            }
            //开启验证
            if (item['on-val'] !== void 0) {
                Yee.dynamic('on', item['on-val']);
            }
        };
        var dynamicLink = function () {
            if (option['dynamicBind']) {
                var bind = $(option['dynamicBind']);
                if (bind.is(':visible')) {
                    bind.triggerHandler('dynamic');
                }
            }
        }
        if (qelem.is(':input[yee-module~=checkgroup]')) {
            var id = qelem.attr('name');
            var form = qelem.parents('form:first');
            var items = form.find(':input[name="' + id + '"]');
            var initclick = function () {
                var data = qelem.data('dynamic');
                var checkeds = form.find(':input[name="' + id + '"]:checked');
                if ($.isArray(data)) {
                    for (var k in data) {
                        var item = data[k];
                        //相等
                        if (item.eq !== void 0) {
                            $(checkeds).each(function (idx, elm) {
                                var bval = $(elm).val();
                                if (item.eq == bval) {
                                    dynamicfun(item);
                                    return false;
                                }
                            });
                        }
                        //不相等
                        if (item.neq !== void 0) {
                            var neq = true;
                            $(checkeds).each(function (idx, elm) {
                                var bval = $(elm).val();
                                if (item.neq == bval) {
                                    neq = false;
                                    return false;
                                }
                            });
                            if (neq) {
                                dynamicfun(item);
                            }
                        }
                        //包含
                        if (item['in'] !== void 0 && $.isArray(item['in'])) {
                            $(checkeds).each(function (idx, elm) {
                                var bval = $(elm).val();
                                if (item['in'].hasValue(bval)) {
                                    dynamicfun(item);
                                    return false;
                                }
                            });
                        }
                        //不包含
                        if (item.nin !== void 0 && $.isArray(item.nin)) {
                            var nin = true;
                            $(checkeds).each(function (idx, elm) {
                                var bval = $(elm).val();
                                if (item.nin.hasValue(bval)) {
                                    nin = false;
                                    return false;
                                }
                            });
                            if (nin) {
                                dynamicfun(item);
                            }
                        }
                    }
                }
                dynamicLink();
            };
            items.on('click dynamic', initclick);
            initclick();
        } else {
            if (qelem.is(':radio')) {
                var name = qelem.attr('name');
                var form = qelem.parents('form:first');
                var items = form.find(':radio[name="' + name + '"]');
                items.on('click', function () {
                    qelem.triggerHandler('change');
                });
            }
            qelem.on('blur click change dynamic', function () {
                var qthis = $(this);
                var val = qthis.val();
                if (qthis.is(':radio')) {
                    var name = qelem.attr('name');
                    var form = qelem.parents('form:first');
                    var ibox = form.find(':radio[name="' + name + '"]:checked');
                    val = ibox.val() || '';
                }
                if (qthis.is(':checkbox')) {
                    val = qthis.is(':checked') ? true : false;
                }
                var data = qthis.data('dynamic');
                if ($.isArray(data)) {
                    for (var k in data) {
                        var item = data[k];
                        if (item.eq !== void 0) {
                            if (item.eq == val) {
                                dynamicfun(item);
                            }
                        }
                        if (item.neq !== void 0) {
                            if (item.neq != val) {
                                dynamicfun(item);
                            }
                        }
                        if (item['in'] !== void 0 && $.isArray(item['in'])) {
                            if (item.eq.hasValue(val)) {
                                dynamicfun(item);
                            }
                        }
                        if (item.nin !== void 0 && $.isArray(item.nin)) {
                            if (!item.neq.hasValue(val)) {
                                dynamicfun(item);
                            }
                        }
                    }
                }
                dynamicLink();

            });
            qelem.triggerHandler('change');
        }

    });

})(jQuery, Yee);