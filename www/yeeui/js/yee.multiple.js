(function ($, Yee) {
    Yee.extend(':input,a', 'multiple', function (element, option) {
        var qem = $(element);
        var textData = qem.data('text') || {};

        var textBox = $('<div></div>').insertAfter(qem);

        textBox.attr('class', qem.attr('class'));
        textBox.attr('style', qem.attr('style'));
        textBox.attr('placeholder', qem.attr('placeholder'));
        textBox.data('val-for', '#' + qem.attr('id') + '-validation');
        qem.hide();

        qem.on('displayError', function (ev) {
            textBox.removeClass('input-valid input-default').addClass('input-error');
        });
        qem.on('displayDefault', function (ev) {
            textBox.removeClass('input-valid input-error').addClass('input-default');
        });
        qem.on('displayValid', function (ev) {
            textBox.removeClass('input-error input-default').addClass('input-valid');
        });

        textBox.on('mousedown', function () {
            if (typeof(qem.setDefault) == 'function') {
                qem.setDefault();
            }
        });

        var span = $('<span></span>').insertAfter(textBox);
        var button = $('<a class="form-inp button" href="javascript:;" yee-module="dialog" data-maxmin="false" style="margin-left: 5px;">选择</a>').appendTo(span);
        button.data('carry', '#' + qem.attr('id'));
        if (option.width) {
            button.data('width', option.width);
        }
        if (option.height) {
            button.data('height', option.height);
        }
        button.data('href', option.href || option.url || '');
        button.data('assign', {value: qem.val(), text: textData});

        button.on('mousedown', function () {
            if (typeof(qem.setDefault) == 'function') {
                qem.setDefault();
            }
        });
        var updateText = function (data) {
            if (data && $.isArray(data)) {
                textBox.empty();
                for (var i = 0, len = data.length; i < len; i++) {
                    var rs = data[i];
                    var item = $('<label><span></span><i class="icofont icofont-close"></i></label>');
                    item.data('value', rs.value || 0);
                    item.data('text', rs.text || 0);
                    item.find('span').text(rs.text);
                    textBox.append(item);
                }
                textData = data;
                button.data('assign', {value: qem.val(), text: data});
            }
        }

        textBox.on('click', 'label', function () {
            var item = $(this);
            item.remove();
            var values = [];
            var textItems = [];
            textBox.find('label').each(function (idx, elem) {
                var val = $(elem).data('value') || null;
                if (val) {
                    values.push(val);
                    var text = $(elem).data('text') || '';
                    textItems.push({value: val, text: text})
                }
            });
            if (values.length == 0) {
                qem.val('');
                updateText([]);
            } else {
                qem.val(JSON.stringify(values));
                updateText(textItems);
            }
        });
        updateText(textData);
        button.on('success', function (ev, data) {
            if (data && data.value && data.text) {
                if (typeof(data.value) == 'string' || typeof(data.value) == 'number') {
                    qem.val(data.value);
                } else if (data.value instanceof Array) {
                    if (data.value == 0) {
                        qem.val('');
                    } else {
                        qem.val(JSON.stringify(data.value));
                    }
                }
                updateText(data.text);
                if (typeof qem.setDefault == 'function') {
                    qem.setDefault();
                }
            }
        });
        Yee.update(span);
    });
})(jQuery, Yee);