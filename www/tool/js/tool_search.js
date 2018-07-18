$(function () {


    var cacheOption = {};
    var typeCache = {};


    var dbtypeBox = $('#dbtype');
    var old_type_val = $(':input[name=type]:checked').val();
    var setType = function (item) {
        if (item && item.len) {
            $('#dblen').data('val-off', false);
            $('#row_dblen').show();
        } else {
            $('#dblen').data('val-off', true).val(0);
            $('#row_dblen').hide();
        }
        if (item && item.point) {
            $('#dbpoint').data('val-off', false);
            $('#row_dbpoint').show();
        } else {
            $('#dbpoint').data('val-off', true).val(0);
            $('#row_dbpoint').hide();
        }
    }
    $(':input[name=type]').on('click', function () {
        var that = $(this);
        var type_options = $(this).data('type');
        var val = $(this).val();
        if (type_options) {
            cacheOption = {};
            var option = [];
            $(type_options).each(function (idx, item) {
                var m = item.match(/(\w+)(?:\((\d+)(?:,(\d+))?\))?/);
                if (m) {
                    var opt = {value: m[1], len: m[2], point: m[3]};
                    if (typeCache[opt.value]) {
                        if (opt.len && typeCache[opt.value]['len']) {
                            opt.len = typeCache[opt.value]['len'];
                        }
                        if (opt.point && typeCache[opt.value]['point']) {
                            opt.point = typeCache[opt.value]['point'];
                        }
                    }
                    cacheOption[opt.value] = opt;
                    option.push(opt);
                }
            });

            var dbtype = dbtypeBox.val() || dbtypeBox.data('value');
            var defaultd = option[0];
            //更新
            if (dbtype == null || dbtype == '' || (old_type_val != val && dbtype != defaultd.value)) {
                dbtypeBox.data('value', defaultd.value);
                dbtypeBox.val(defaultd.value);
                setType(defaultd);
                if (defaultd.len) {
                    $('#dblen').val(defaultd.len);
                }
                if (defaultd.point) {
                    $('#dbpoint').val(defaultd.point);
                }
            } else {
                var item = cacheOption[dbtype] || null;
                if (item) {
                    setType(item);
                    typeCache[dbtype] = typeCache[dbtype] || {};
                    if (item.len) {
                        typeCache[dbtype].len = $('#dblen').val();
                    }
                    if (item.point) {
                        typeCache[dbtype].point = $('#dbpoint').val();
                    }
                }
            }
            dbtypeBox.data('options', option);
            dbtypeBox.triggerHandler('update');
        } else {
            dbtypeBox.data('options', []);
            dbtypeBox.triggerHandler('update');
        }
        old_type_val = val;
    });

    dbtypeBox.on('change', function () {
        var dbtype = $(this).val();
        if (dbtype) {
            var item = cacheOption[dbtype] || null;
            setType(item);
            if (item && item.len) {
                $('#dblen').val(item.len);
            }
            if (item && item.point) {
                $('#dbpoint').val(item.point);
            }
        }

    });

    $('#dblen').on('blur', function () {
        var dbtype = dbtypeBox.val() || dbtypeBox.data('value');
        var item = cacheOption[dbtype] || null;
        typeCache[dbtype] = typeCache[dbtype] || {};
        if (item.len) {
            typeCache[dbtype].len = $(this).val();
        }
    });

    $('#dbpoint').on('blur', function () {
        var dbtype = dbtypeBox.val() || dbtypeBox.data('value');
        var item = cacheOption[dbtype] || null;
        typeCache[dbtype] = typeCache[dbtype] || {};
        if (item.point) {
            typeCache[dbtype].point = $(this).val();
        }
    });

    $(':input[name=type]:checked').triggerHandler('click');

    var ctype = $(':input[name=type]:checked').val();

    setTimeout(function () {
        $(':input[name=type]').on('click', function () {
            var that = $(this);
            var val = that.val();
            if (val == ctype) {
                return;
            }
            ctype = val;
            var extend_layout = $('#extendAttrs');
            var sendData = extend_layout.find(':input').serialize();
            extend_layout.triggerHandler('load', [null, sendData]);
        });
    }, 500);


});