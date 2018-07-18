(function ($, Yee, layer) {

    var optlist = {
        required: {
            text: '必填项', def: '{lable}必须填写',
            ntype: {
                'radiogroup': '请选择{lable}',
                'select': '请选择{lable}',
                'checkgroup': '请选择{lable}',
                'labelcheckgroup': '请选择{lable}',
                'labelradiogroup': '请选择{lable}',
                'upfile': '请上传{lable}'
            }
        },
        email: {text: '邮箱格式', def: '{lable}格式书写不符'},
        mobile: {text: '手机号码', def: '{lable}格式不符'},
        idcard: {text: '身份证号码', def: '{lable}格式不符'},
        number: {text: '数值形式', def: '{lable}只能输入数值'},
        integer: {text: '整数形式', def: '{lable}只能输入整数'},
        date: {text: '时间日期格式', def: '{lable}格式错误'},
        min: {text: '最小范围', def: '{lable}不能低于{0}'},
        max: {text: '最大范围', def: '{lable}不能大于{0}'},
        range: {text: '区间范围', def: '{lable}需在{0}-{1}之间'},
        minlength: {text: '最小长度', def: '{lable}字数长度不足{0}位'},
        maxlength: {text: '最大长度', def: '{lable}字数长度超出{0}位'},
        rangelength: {text: '长度区间', def: '{lable}字数长度需在{0}-{1}之间'},
        money: {text: '金额格式', def: '{lable}格式不正确'},
        url: {text: 'URL格式', def: '{lable}格式不正确'},
        equal: {text: '与给定值相等', def: '{lable}不符要求'},
        notequal: {text: '不能与给定值相等', def: '{lable}不符要求'},
        equalto: {text: '与控件值相等', def: '两次输入的{lable}不一致'},
        regex: {text: '正则表达式', def: '{lable}格式不符'},
        remote: {text: 'AJAX远程验证', def: '{lable}已经存在，请更换其他'}
    };

    function ToolValidtor(element) {
        var that = $(element).hide();
        var valdbox = $('<div class="valdbox"></div>').insertAfter(that);
        var showbox = $('<div></div>').insertAfter(that);
        var select = $('<select class="form-inp select"><select>').appendTo(showbox);
        for (var key in optlist) {
            var it = optlist[key];
            var item = $('<option value="' + key + '">' + it.text + '|' + key + '</option>');
            select.append(item);
        }
        var argsspan = $('<span></span>').appendTo(showbox);
        var addbtn = $('<a style="margin-left:10px;" class="form-inp button" href="#">添加</a>').appendTo(showbox);
        $('<a style="margin-left:10px;" class="form-inp button" href="#">显示</a>').appendTo(showbox).on('click', function () {
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
        var update = function () {
            var alldat = {};
            var items = valdbox.find('div.valid_item');
            if (items.length === 0) {
                that.val('');
            } else {
                items.each(function (index, element) {
                    var itdat = $(element).data('itdat');
                    alldat = $.extend(alldat, itdat);
                });
                that.data('alldat', alldat);
                $(that).triggerHandler('update');
                that.val(JSON.stringify(alldat));
            }
        };
        var updatediv = function () {
            valdbox.empty();
            try {
                var boxval = that.val();
                var boxdata = JSON.parse(boxval);
                if (typeof (boxdata) === 'object') {
                    for (var key in boxdata) {
                        addtype(key, boxdata[key]);
                    }
                }
            } catch (ex) {
            }
        };

        var tiptype = function (type) {
            var strtext = $('#label').val();
            var ntype = $('input[name=ntype]').val();
            var opt = optlist[type] && optlist[type].def || '{lable}格式不符';
            if (optlist[type] && optlist[type].ntype && optlist[type].ntype[ntype]) {
                opt = optlist[type].ntype[ntype];
            }
            opt = opt.replace('{lable}', strtext);
            var bind = that.data('bind') || '';
            if (bind) {
                $(bind).triggerHandler('additem', [{type: type, vals: opt}]);
            }
        };

        var addtype = function (type, vals) {
            var items = valdbox.find('div.valid_item');
            var canadd = true;
            items.each(function () {
                var itemd = $(this);
                var itdat = itemd.data('itdat');
                if (itdat[type]) {
                    itdat[type] = vals;
                    itemd.data('itdat', itdat);
                    itemd.find('span').text(JSON.stringify(itdat));
                    canadd = false;
                    update();
                    return false;
                }
            });
            if (!canadd) {
                return;
            }
            var itemd = $('<div style="line-height:20px;" class="valid_item"></div>').appendTo(valdbox);
            var itdat = {};
            itdat[type] = vals;
            tiptype(type);
            $('<span></span>').text(JSON.stringify(itdat)).appendTo(itemd);
            $('<a style="margin-left:10px;" href="#">删除</a>').one('click', function () {
                var p = $(this).parent();
                var itdat = p.data('itdat');
                var type = '';
                for (var i in itdat) {
                    type = i;
                    break;
                }
                p.remove();
                update();
                $(that).triggerHandler('delitem', [type]);
                return false;
            }).appendTo(itemd);
            itemd.data('itdat', itdat);
            update();
        };
        select.change(function () {
            var type = $(this).val();
            addbtn.off('click');
            argsspan.empty();
            switch (type) {
                case 'min':
                    $('<span style="margin-left:10px;">最小范围：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括最小：</span>').appendTo(argsspan);
                    var mt2 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (!/^[\-\+]?((\d+(\.\d*)?)|(\.\d+))$/.test(val1)) {
                            alert('最小范围必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (mt2.is(':checked')) {
                            addtype(type, [Number(val1), true]);
                        } else {
                            addtype(type, Number(val1));
                        }
                        return false;
                    });
                    break;
                case 'max':
                    $('<span style="margin-left:10px;">最大范围：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括最大：</span>').appendTo(argsspan);
                    var mt2 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (!/^[\-\+]?((\d+(\.\d*)?)|(\.\d+))$/.test(val1)) {
                            alert('最大范围必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (mt2.is(':checked')) {
                            addtype(type, [Number(val1), true]);
                        } else {
                            addtype(type, Number(val1));
                        }
                        return false;
                    });
                    break;
                case 'minlength':
                    $('<span style="margin-left:10px;">最小长度：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括最小：</span>').appendTo(argsspan);
                    var mt2 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (!/^[0-9]+$/.test(val1)) {
                            alert('最小长度必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (mt2.is(':checked')) {
                            addtype(type, [Number(val1), true]);
                        } else {
                            addtype(type, Number(val1));
                        }
                        return false;
                    });
                    break;
                case 'maxlength':
                    $('<span style="margin-left:10px;">最大长度：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括最大：</span>').appendTo(argsspan);
                    var mt2 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (!/^[0-9]+$/.test(val1)) {
                            alert('最大长度必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (mt2.is(':checked')) {
                            addtype(type, [Number(val1), true]);
                        } else {
                            addtype(type, Number(val1));
                        }
                        return false;
                    });
                    break;
                case 'range':
                    $('<span style="margin-left:10px;">最小范围：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">最大范围：</span>').appendTo(argsspan);
                    var mt2 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括范围：</span>').appendTo(argsspan);
                    var mt3 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        var val2 = mt2.val();
                        if (!/^[\-\+]?((\d+(\.\d*)?)|(\.\d+))$/.test(val1)) {
                            alert('最小范围必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (!/^[\-\+]?((\d+(\.\d*)?)|(\.\d+))$/.test(val2)) {
                            alert('最小范围必须是数字形式的数值！');
                            mt2.focus();
                            return false;
                        }
                        if (mt3.is(':checked')) {
                            addtype(type, [Number(val1), Number(val2), true]);
                        } else {
                            addtype(type, [Number(val1), Number(val2)]);
                        }
                        return false;
                    });
                    break;
                case 'rangelength':
                    $('<span style="margin-left:10px;">最小长度：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">最大长度：</span>').appendTo(argsspan);
                    var mt2 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">包括范围：</span>').appendTo(argsspan);
                    var mt3 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        var val2 = mt2.val();
                        if (!/^[0-9]+$/.test(val1)) {
                            alert('最小长度必须是数字形式的数值！');
                            mt1.focus();
                            return false;
                        }
                        if (!/^[0-9]+$/.test(val2)) {
                            alert('最小长度必须是数字形式的数值！');
                            mt2.focus();
                            return false;
                        }
                        if (mt3.is(':checked')) {
                            addtype(type, [Number(val1), Number(val2), true]);
                        } else {
                            addtype(type, [Number(val1), Number(val2)]);
                        }
                        return false;
                    });
                    break;
                case 'equal':
                case 'notequal':
                    $('<span style="margin-left:10px;">给定比较值：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp stext"/>').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        addtype(type, val1);
                        return false;
                    });
                    break;
                case 'equalto':
                    $('<span style="margin-left:10px;">控件ID：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp integer"/>').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (val1.length === 0) {
                            alert('必须填写控件ID！');
                            mt1.focus();
                            return false;
                        }
                        addtype(type, '#' + val1);
                        return false;
                    });
                    break;
                case 'regex':
                    $('<span style="margin-left:10px;">请使用PHP和JS兼容的正则表达式：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp stext"/>').appendTo(argsspan);
                    addbtn.on('click', function () {
                        var val1 = mt1.val();
                        if (val1.length === 0) {
                            alert('必须填写正则表达式');
                            mt1.focus();
                            return false;
                        }
                        addtype(type, val1);
                        return false;
                    });
                    break;
                case 'remote':
                    $('<span style="margin-left:10px;">URL：</span>').appendTo(argsspan);
                    var mt1 = $('<input  type="text" class="form-inp stext"/>').appendTo(argsspan);
                    var mt2 = $('<select style="margin-left:10px;" class="form-inp select">\n\
<option value="GET">GET</option>\n\
<option value="POST">POST</option>\n\
</select>').appendTo(argsspan);
                    $('<span style="margin-left:10px;">附加控件name 多个用半角 "," 分割：</span>').appendTo(argsspan);
                    var mt3 = $('<input  type="text" class="form-inp stext"/>').appendTo(argsspan);
                    addbtn.on('click', function () {

                        var val1 = mt1.val();
                        var val2 = mt2.val();
                        var val3 = mt3.val();

                        if (val1.length === 0) {
                            alert('必须填写URL');
                            mt1.focus();
                            return false;
                        }

                        if (!(val3.length === 0 || /^[a-zA-Z0-9_,]+$/.test(val3))) {
                            alert('附件控件名称属性不正确');
                            mt3.focus();
                            return false;
                        }
                        if (val3.length === 0) {
                            if (val2 === 'GET') {
                                addtype(type, val1);
                            } else {
                                addtype(type, [val1, val2]);
                            }
                        } else {
                            addtype(type, [val1, val2, val3]);
                        }

                        return false;
                    });
                    break;
                case 'url':
                    $('<span style="margin-left:10px;">包括#号：</span>').appendTo(argsspan);
                    var mt1 = $('<input type="checkbox">').appendTo(argsspan);
                    addbtn.on('click', function () {
                        if (mt1.is(':checked')) {
                            addtype(type, [true, true]);
                        } else {
                            addtype(type, true);
                        }
                        return false;
                    });
                    break;
                default:
                    addbtn.on('click', function () {
                        addtype(type, true);
                        return false;
                    });
                    break;
            }
        });
        updatediv();
        that.on('blur', updatediv);
        select.change();
    }

    function ToolValidMsg(element) {
        var that = $(element).hide();
        var valdbox = $('<div class="valdbox"></div>').insertAfter(that);
        var showbox = $('<div></div>').insertAfter(that);
        var select = $('<select class="form-inp select"><select>').appendTo(showbox);
        for (var key in optlist) {
            var it = optlist[key];
            var item = $('<option value="' + key + '">' + it.text + '|' + key + '</option>');
            select.append(item);
        }
        that.on('additem', function (ev, data) {
            select.val(data.type);
            addtype(data.type, data.vals, false);
        });
        that.on('delitem', function (ev, type) {
            var items = valdbox.find('div.valid_item');
            items.each(function () {
                var itemd = $(this);
                var itdat = itemd.data('itdat');
                if (itdat[type]) {
                    itemd.remove();
                    update();
                    return false;
                }
            });
        });
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
        var update = function () {
            var alldat = {};
            var items = valdbox.find('div.valid_item');
            if (items.length === 0) {
                that.val('');
            } else {
                items.each(function (index, element) {
                    var itdat = $(element).data('itdat');
                    alldat = $.extend(alldat, itdat);
                });
                that.data('alldat', alldat);
                $(that).triggerHandler('update');
                that.val(JSON.stringify(alldat));
            }
        };
        var updatediv = function () {
            valdbox.empty();
            try {
                var boxval = that.val();
                var boxdata = JSON.parse(boxval);
                if (typeof (boxdata) === 'object') {
                    for (var key in boxdata) {
                        addtype(key, boxdata[key], true);
                    }
                }
            } catch (ex) {

            }
        };
        var addtype = function (type, vals, over) {
            if (over === false) {
                var items = valdbox.find('div.valid_item');
                var canadd = true;
                items.each(function () {
                    var itdat = $(this).data('itdat');
                    if (itdat[type]) {
                        canadd = false;
                        return false;
                    }
                });
                if (!canadd) {
                    return;
                }
            } else {
                var items = valdbox.find('div.valid_item');
                var canadd = true;
                items.each(function () {
                    var itemd = $(this);
                    var itdat = itemd.data('itdat');
                    if (itdat[type]) {
                        itdat[type] = vals;
                        itemd.data('itdat', itdat);
                        itemd.find('span').text(JSON.stringify(itdat));
                        canadd = false;
                        update();
                        return false;
                    }
                });
                if (!canadd) {
                    return;
                }

            }
            var itemd = $('<div style="line-height:20px;" class="valid_item"></div>').appendTo(valdbox);
            var itdat = {};
            itdat[type] = vals;
            $('<span></span>').text(JSON.stringify(itdat)).appendTo(itemd);
            $('<a style="margin-left:10px;" href="#">删除</a>').one('click', function () {
                $(this).parent().remove();
                update();
                return false;
            }).appendTo(itemd);
            itemd.data('itdat', itdat);
            update();
        };
        select.change(function () {
            var type = $(this).val();
            addbtn.unbind('click');
            argsspan.empty();
            $('<span style="margin-left:10px;">出错提示消息：</span>').appendTo(argsspan);
            var mt1 = $('<input  type="text" class="form-inp text"/>').appendTo(argsspan);
            addbtn.on('click', function () {
                var val1 = mt1.val();
                if (val1.length === 0) {
                    return false;
                }
                addtype(type, val1, true);
                return false;
            });
        });
        updatediv();
        that.on('blur', updatediv);
        select.change();
    }

    function ToolValidtorGroup(element) {

        var Timer = null;
        var that = $(element).hide();
        var showbox = $('<div></div>').insertAfter(that);
        var addbtn = $('<a style="margin-left:0px;" class="form-inp button" href="#">添加验证组</a>').appendTo(showbox);
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

        var update = function () {
            var Bigalldat = {rule: [], msg: []};
            var Bigitems = showbox.find('div.group-item');

            if (Bigitems.length === 0) {
                that.val('');
            } else {

                Bigitems.each(function () {
                    var itemboxs = $(this).find('div.group-item-boxlay textarea.itembox');
                    var itemmsgs = $(this).find('div.group-item-msglay textarea.itemmsg');
                    if (itemboxs.length === 0 || itemmsgs.length == 0) {
                        return;
                    } else {
                        var rule = itemboxs.data('alldat') || {};
                        var msgs = itemmsgs.data('alldat') || {};
                        Bigalldat.rule.push(rule);
                        Bigalldat.msg.push(msgs);
                    }
                });

                that.val(JSON.stringify(Bigalldat));
            }
        };

        var additem = function (rule, msg) {
            var groupitem = $('<div class="group-item" style="margin-top:5px; padding:5px; background-color:#fafeff; border:1px dashed #dfdfdf"></div>').appendTo(showbox);
            var boxlay = $('<div class="group-item-boxlay"></div>').appendTo(groupitem);
            var itembox = $('<textarea class="form-inp textarea itembox" style="height:30px;" yee-module="validtor"/>').appendTo(boxlay);
            var msglay = $('<div class="group-item-msglay"></div>').appendTo(groupitem);
            var itemmsg = $('<textarea class="form-inp textarea itemmsg" style="height:30px;"  yee-module="validmsg"/>').appendTo(msglay);

            Yee.update(groupitem);
            if (rule) {
                itembox.val(rule).triggerHandler('blur');
            }
            if (msg) {
                itemmsg.val(msg).triggerHandler('blur');
            }
            itembox.on('additem', function (ev, data) {
                itemmsg.triggerHandler('additem', [data]);
            });
            itembox.on('delitem', function (ev, data) {
                itemmsg.triggerHandler('delitem', [data]);
            });
            itembox.on('update', function () {
                console.log('1111');
                if (Timer !== null) {
                    window.clearTimeout(Timer);
                    Timer = null;
                }
                Timer = window.setTimeout(update, 200);
            });
            itemmsg.on('update', function () {
                if (Timer !== null) {
                    window.clearTimeout(Timer);
                    Timer = null;
                }
                Timer = window.setTimeout(update, 200);
            });
            $('<a style="margin-left:0px;" class="form-inp button" href="#">删除组</a>').one('click', function () {
                $(this).parent().remove();
                update();
                return false;
            }).appendTo(groupitem);
        };

        var updatediv = function () {
            showbox.find('div.group-item').remove();
            try {
                var boxval = that.val();
                var boxdata = JSON.parse(boxval);
                if (boxdata && $.isArray(boxdata.rule) && $.isArray(boxdata.msg)) {
                    for (var key = 0; key < boxdata.rule.length; key++) {
                        additem(JSON.stringify(boxdata.rule[key]), JSON.stringify(boxdata.msg[key] || null));
                    }
                }
            } catch (ex) {
            }
        };

        addbtn.on('click', function () {
            additem(null, null);
            return false;
        });
        updatediv();
        that.on('blur', updatediv);

    }

    Yee.extend('textarea', 'validtor', ToolValidtor);
    Yee.extend('textarea', 'validmsg', ToolValidMsg);
    Yee.extend('textarea', 'validgroup', ToolValidtorGroup);

})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);
