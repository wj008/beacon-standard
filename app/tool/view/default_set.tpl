<!--{literal left='{@' right='@}'}-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>设置默认值</title>
    <script src="/yeeui/js/jquery-3.2.1.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
    <link rel="stylesheet" href="/yeeui/css/base.css">
    <link rel="stylesheet" href="/yeeui/css/yeeui.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <style>
        body, html {
            background: #fff;
        }
    </style>
</head>
<body>
<div id="createPlace" style="position: relative; height:100%;">
    <div style="padding: 20px 0;">
        <form id="setForm" onsubmit="return false">

            <div class="form-group">
                <label class="form-label" style="width: 120px">默认值类型：</label>
                <div class="form-box">
                    <label class="form-inp radiogroup"><input type="radio" id="type1" name="type" value="1" checked="checked"/><span>内置方法</span></label>
                    <label class="form-inp radiogroup"><input type="radio" id="type2" name="type" value="2"/><span>参数传递</span></label>
                    <label class="form-inp radiogroup"><input type="radio" id="type3" name="type" value="3"/><span>SQL查询</span></label>
                    <label class="form-inp radiogroup"><input type="radio" id="type4" name="type" value="4"/><span>PHP函数</span></label>
                </div>
            </div>

            <div class="form-group row_c1">
                <label class="form-label" style="width: 120px">内置函数名：</label>
                <div class="form-box">
                    <select id="inner" class="form-inp select">
                        <option value="date">当前日期</option>
                        <option value="datetime">当前时间</option>
                        <option value="maxsort">最大排序值</option>
                        <option value="minsort">最小排序值</option>
                    </select></div>
            </div>

            <div class="form-group row_c2" style="display: none">
                <label class="form-label" style="width: 120px">参数传递：</label>
                <div class="form-box">
                    <select id="argstype" class="form-inp select">
                        <option value="req" selected="selected">REQUEST</option>
                        <option value="get">GET</option>
                        <option value="post">POST</option>
                    </select>
                    <input type="text" id="args" class="form-inp text">
                </div>
            </div>

            <div class="form-group row_c3" style="display: none">
                <label class="form-label" style="width: 120px">参数传递：</label>
                <div class="form-box">
                    <select id="sql_argstype" class="form-inp select">
                        <option value="req" selected="selected">REQUEST</option>
                        <option value="get">GET</option>
                        <option value="post">POST</option>

                    </select>
                    <input type="text" id="sql_args" class="form-inp text"> <span>多个用,隔开</span>
                </div>
            </div>

            <div class="form-group row_c3" style="display: none">
                <label class="form-label" style="width: 120px">SQL查询语句：</label>
                <div class="form-box">
                    <textarea id="sql" class="form-inp textarea" style="height:100px;width:480px"></textarea>
                </div>
            </div>

            <div class="form-group row_c3" style="display: none">
                <label class="form-label" style="width: 120px">结果字段名：</label>
                <div class="form-box">
                    <input type="text" id="field" class="form-inp text">
                </div>
            </div>

            <div class="form-group row_c4" style="display: none">
                <label class="form-label" style="width: 120px">函数名：</label>
                <div class="form-box">
                    <span><input type="text" id="func" class="form-inp ltext"> （写全 命名空间）</span>
                    <p class="field-tips check">函数名格式如 \app\test::func</p>
                </div>
            </div>


            <div class="form-group" style="position:fixed; bottom: 0px; width: 100%; background:#f1f1f1;">
                <div class="form-submit" style="padding: 10px; text-align: right; float:right;">
                    <input id="submit_btn" type="button" class="form-btn submit" value="确定"/>
                    <input id="close_btn" type="button" class="form-btn back" value="关闭"/>
                </div>
            </div>


        </form>
    </div>
</div>
</body>
<script>
    window.readyYeeDialog = function (assign, win, elem) {

        var setType = function (type) {
            if (type == 1) {
                $('.row_c1').show();
                $('.row_c2').hide();
                $('.row_c3').hide();
                $('.row_c4').hide();
            }
            if (type == 2) {
                $('.row_c1').hide();
                $('.row_c2').show();
                $('.row_c3').hide();
                $('.row_c4').hide();
            }
            if (type == 3) {
                $('.row_c1').hide();
                $('.row_c2').hide();
                $('.row_c3').show();
                $('.row_c4').hide();
            }
            if (type == 4) {
                $('.row_c1').hide();
                $('.row_c2').hide();
                $('.row_c3').hide();
                $('.row_c4').show();
            }
        }
        Yee.loader('yee-layer', function () {
            $(':input[name=type]').on('click', function () {
                var type = $(':input[name=type]:checked').val();
                setType(type);
            });
            $('#close_btn').on('click', function () {
                if (window.YeeDialog) {
                    window.closeYeeDialog();
                }
            });
            $('#submit_btn').on('click', function () {
                var data ={};
                var type = $(':input[name=type]:checked').val();

                if (type == 1) {
                    data['inner'] = $('#inner').val();
                } else if (type == 2) {
                    var argstype = $('#argstype').val();
                    var args = $('#args').val();
                    if (args == '' || !/^\w+(:[abfis])?$/.test(args)) {
                        layer.alert('参数格式不正确');
                        return;
                    }
                    data[argstype] = args;
                } else if (type == 3) {
                    var argstype = $('#sql_argstype').val();
                    var args = $('#sql_args').val();
                    if (args != '' && !/^(\w+(:[abfis])?)(,\w+(:[abfis])?)*$/.test(args)) {
                        layer.alert('参数格式不正确');
                        return;
                    }
                    data[argstype] = args;
                    var sql = $('#sql').val();
                    if (sql == '') {
                        layer.alert('SQL查询语句没有填写');
                        return;
                    }
                    data['sql'] = sql;
                    var field = $('#field').val();
                    if (field == '' || !/^\w+$/.test(field)) {
                        layer.alert('结果字段名不正确');
                        return;
                    }
                    data['field'] = field;
                } else if (type == 4) {
                    var func = $('#func').val();
                    if (func == '' || !/^(\\\w+)+::\w+$/.test(func)) {
                        layer.alert('函数名称不正确');
                        return;
                    }
                    data['func'] = func;
                } else {
                    return;
                }

                if (window.YeeDialog) {
                    var code = JSON.stringify(data);
                    window.success( {code:'@' + code});
                    window.closeYeeDialog();
                }
            });
            (function (assign) {
                if (!assign) {
                    return;
                }
                var m = String(assign).match(/^@(\{.*\})$/);
                if (m) {
                    try {
                        var data = JSON.parse(m[1]);
                        if (data['inner']) {
                            $('#inner').val(data['inner']);
                            $('#type1').trigger('click');
                            setType(1);
                            return;
                        }
                        else if (data['sql'] && data['field'] && (data['get'] || data['post'] || data['req'])) {
                            $('#sql').val(data['sql']);
                            $('#field').val(data['field']);
                            if (data['get']) {
                                $('#sql_argstype').val('get');
                                $('#sql_args').val(data['get']);
                            }
                            else if (data['post']) {
                                $('#sql_argstype').val('post');
                                $('#sql_args').val(data['post']);
                            }
                            else {
                                $('#sql_argstype').val('req');
                                $('#sql_args').val(data['req']);
                            }
                            $('#type3').trigger('click');
                            setType(3);
                            return;
                        }
                        else if ((data['get'] || data['post'] || data['req'])) {
                            if (data['get']) {
                                $('#argstype').val('get');
                                $('#args').val(data['get']);
                            }
                            else if (data['post']) {
                                $('#argstype').val('post');
                                $('#args').val(data['post']);
                            }
                            else {
                                $('#argstype').val('req');
                                $('#args').val(data['req']);
                            }
                            $('#type2').trigger('click');
                            setType(2);
                            return;
                        }
                        else if (data['func']) {
                            $('#func').val(data['func']);
                            $('#type4').trigger('click');
                            setType(4);
                            return;
                        }
                    } catch (e) {
                    }
                }

            })(assign);
        });
    };


</script>
</html>
<!--{/literal}-->