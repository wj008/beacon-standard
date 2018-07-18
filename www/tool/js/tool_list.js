Yee.loader('yee-layer', function () {
    var cacheFileds = [];
    var cacheTbname = '';
    var changeForm = function () {
        var that = $('#formId');
        var formId = $('#formId').val();
        $.post('/tool/tool_list/get_field', {formId: formId}, function (ret) {

            if (ret && ret.status) {
                cacheFileds = ret.data.options;
                cacheTbname = ret.data.tbname;
                $('#tbName').val(cacheTbname);
                //更新字段列表
                $('.dbfield').each(function (idx, item) {
                    $(item).data('options', cacheFileds).triggerHandler('update');
                });
                //更新排序列表
                $('.orderfield').each(function (idx, item) {
                    $(item).data('options', cacheFileds).triggerHandler('update');
                });
            }
        }, 'json');
        var optstr = that.find(':selected').text();
        var optarr = optstr.split('|');
        if (optarr.length === 2) {
            var namebox = $('#title');
            var captionbox = $('#caption');
            var keybox = $('#key');
            if (namebox.val() === '') {
                namebox.val($.trim(optarr[0]));
            }
            if (captionbox.val() === '') {
                captionbox.val($.trim(optarr[0]));
            }
            if (keybox.val() === '') {
                keybox.val($.trim(optarr[1]));
            }
        }
    }
    $('#formId').on('change', changeForm);
    changeForm();
    $('#row_fields').on('addItem', function (ev, item) {
        if (item) {
            item.find('.dbfield').data('options', cacheFileds).triggerHandler('update');
            item.find('.orderfield').data('options', cacheFileds).triggerHandler('update');
        }
    });

    $('#row_fields').on('change', 'select.dbfield', function (ev) {
        var emt = $(this);
        var val = emt.val();
        var ptext = emt.find("option:selected").text().split(' | ')[0];
        var parent = emt.parents('.plugin-item:first');
        var box_tic = parent.find('input.title');
        var box_val = parent.find('textarea.code');
        if (val !== '0' && val !== '') {
            if (box_tic.val() === '') {
                box_tic.val(ptext);
            }
            var txval = box_val.val();
            box_val.val(txval + '{$rs.' + val + '}');
        }
    });

    $(document.body).on('change', 'select.shortcut', function (ev) {
        var sbox = $(this);
        var val = sbox.val();
        if (val) {
            var p = sbox.parents('div.plugin-item:first');
            var tbox = p.find('textarea');
            tbox.val(sbox.val());
        }
    });

    $('#select-all-btn').on('click', function () {
        $(':checkbox.select-item').prop('checked', $(this).prop('checked'));
    });
    $('#copy-btn').on('click', function () {
        var sitem = $(':checkbox.select-item:checked');
        if (sitem.length == 0) {
            layer.msg('没有选择任何要复制的栏目');
            return;
        }
        var dataItems = [];
        sitem.each(function () {
            var item = $(this).parents('.plugin-item:first');
            var data = {};
            data.title = item.find(':input[name*="[title]"]').val();
            data.orderName = item.find(':input[name*="[orderName]"]').val();
            data.thAlign = item.find(':input[name*="[thAlign]"]').val();
            data.thWidth = item.find(':input[name*="[thWidth]"]').val();
            data.tdAlign = item.find(':input[name*="[tdAlign]"]').val();
            data.tdAttrs = item.find(':input[name*="[tdAttrs]"]').val();
            data.field = item.find(':input[name*="[field]"]').val();
            data.thFixed = item.find(':input[name*="[thFixed]"]').val();
            data.code = item.find(':input[name*="[code]"]').val();
            dataItems.push(data);
        });
        if (window.clipboardData) {
            window.clipboardData.setData('text', JSON.stringify({cptype: 'listField', datas: dataItems}));
            layer.msg('复制成功');
        } else if (window.localStorage) {
            window.localStorage.setItem('copyList', JSON.stringify({cptype: 'listField', datas: dataItems}));
            layer.msg('复制成功');
        } else {
            layer.msg('浏览器不支持复制');
        }
    });

    $('#delall-btn').on('click', function () {
        var sitem = $(':checkbox.select-item:checked');
        if (sitem.length == 0) {
            layer.msg('没有选择任何要复制的栏目');
            return;
        }
        var idx = layer.confirm('确定要黏贴字段了吗？', function () {
            layer.close(idx);
            var dataItems = [];
            sitem.each(function () {
                var item = $(this).parents('.plugin-item:first');
                dataItems.push(item);
            });
            if (dataItems.length > 0) {
                for (var i = dataItems.length - 1; i >= 0; i--) {
                    var delitem = dataItems[i];
                    if (delitem) {
                        delitem.remove();
                    }
                }
                var obj = Yee.getModuleInstance('#row_fields', 'plugin');
                if (obj) {
                    obj.updateIndex();
                }
            }
        });
    });

    $('#paste-btn').on('click', function () {

        var ret = null;
        if (window.clipboardData) {
            ret = window.clipboardData.getData('text');
        } else if (window.localStorage) {
            ret = window.localStorage.getItem('copyList');
        }
        if (ret == null) {
            return;
        }
        try {
            ret = JSON.parse(ret);
            if (ret['cptype'] && ret['cptype'] == 'listField' && ret['datas']) {
                var dataItems = ret['datas'];
                var obj = Yee.getModuleInstance('#row_fields', 'plugin');
                if (obj) {
                    for (var i = 0; i < dataItems.length; i++) {
                        var data = dataItems[i];
                        var item = obj.addItem();
                        if (item) {
                            (function (data, item) {
                                setTimeout(function () {
                                    item.find(':input[name*="[title]"]').val(data.title);
                                    item.find(':input[name*="[orderName]"]').val(data.orderName);
                                    item.find(':input[name*="[thAlign]"]').val(data.thAlign);
                                    item.find(':input[name*="[thWidth]"]').val(data.thWidth);
                                    item.find(':input[name*="[tdAlign]"]').val(data.tdAlign);
                                    item.find(':input[name*="[tdAttrs]"]').val(data.tdAttrs);
                                    var filed = item.find(':input[name*="[field]"]').val(data.field);
                                    filed.data('value', data.field);
                                    item.find(':input[name*="[thFixed]"]').val(data.thFixed);
                                    item.find(':input[name*="[code]"]').val(data.code);
                                }, 100);
                            })(data, item);
                        }
                    }
                }
            }
        } catch (e) {

        }
    });

});