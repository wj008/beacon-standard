(function ($, Yee) {
    Yee.extend(':input,a', 'select_dialog', function (element, option) {
        var qem = $(element);
        if (qem.is(':input')) {
            var textBox = $('<input type="text" readonly="readonly"/>').insertAfter(qem);
            textBox.attr('class', qem.attr('class'));
            textBox.attr('style', qem.attr('style'));
            textBox.attr('placeholder', qem.attr('placeholder'));
            textBox.data('val-for', '#' + qem.attr('id') + '-validation');
            qem.hide();
            if (typeof textBox.setError == 'function') {
                qem.on('displayError', function (ev) {
                    textBox.setError();
                });
                qem.on('displayDefault', function (ev) {
                    textBox.setDefault();
                });
                qem.on('displayValid', function (ev) {
                    textBox.setValid();
                });
                textBox.on('mouseenter', function () {
                    if (typeof(qem.setDefault) == 'function') {
                        qem.setDefault();
                    }
                });
            }
            var span = $('<span></span>').insertAfter(textBox);
            var button = $('<a class="form-inp button" href="javascript:;" yee-module="dialog" style="margin-left: 5px">选择</a>').appendTo(span);
            if (option.btnText) {
                button.text(option.btnText);
            }
            button.data('carry', '#' + qem.attr('id'));
            if (option.width) {
                button.data('width', option.width);
            }
            if (option.height) {
                button.data('height', option.height);
            }
            textBox.on('click', function () {
                button.trigger('click');
            });
            button.data('href', option.href || option.url || '');
            button.data('assign', {value: qem.val(), text: qem.data('text') || ''});
            textBox.val(qem.data('text') || '');
            button.on('success', function (ev, data) {
                if (data && data.value && data.text) {
                    var ret = qem.emit('select', data);
                    if (ret !== null) {
                        qem.val(data.value);
                        textBox.val(data.text);
                        if (typeof textBox.setDefault == 'function') {
                            textBox.setDefault();
                        }
                    }
                }
            });

            if (option.clearBtn) {
                var clearBtn = $('<a class="form-inp button" href="javascript:;" style="margin-left: 5px">清除</a>').appendTo(span);
                clearBtn.on('click', function () {
                    qem.val('');
                    textBox.val('');
                });
            }
            setTimeout(function () {
                Yee.update(span);
            }, 100);
        }
        else if (qem.is('a')) {
            qem.on('click', function () {
                if (window.YeeDialog) {
                    window.success($(this).data());
                    window.closeYeeDialog();
                }
            });
        }
    });
})(jQuery, Yee);