<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{block name="title"}{/block}</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
{literal}
    <style>
        #main-layout::-webkit-scrollbar, #main-select::-webkit-scrollbar { /*滚动条整体部分，其中的属性有width,height,background,border等（就和一个块级元素一样）（位置1）*/
            width: 8px;
            height: 8px;
            border-radius: 10px;
        }

        #main-layout::-webkit-scrollbar-track, #main-select::-webkit-scrollbar-track {
            background: #f6f6f6;
            border-radius: 2px;
        }

        #main-layout::-webkit-scrollbar-thumb, #main-select::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 2px;
        }

        #main-layout::-webkit-scrollbar-thumb:hover, #main-select::-webkit-scrollbar-thumb:hover {
            background: #a4a4a4;
        }

        #main-layout::-webkit-scrollbar-corner, #main-select::-webkit-scrollbar-corner {
            background: #f6f6f6;
        }
    </style>
{/literal}
<body>
<div class="yeeui-content yeeui-dialog">
    {block name='listHead'}{/block}
    <div class="clear"></div>

    <div style="width: 100%;">
        <div id="main-layout" style="float:left; width:calc(100% - 344px); overflow-y: auto;  border: 1px silver solid; padding: 5px; background:#fff;">

            {block name='ListTab'}{/block}
            {block name='listSearch'}{/block}
            <div class="yeeui-list">
                {block name='listTable'}{/block}
                {block name='pagebar'}{/block}
            </div>

        </div>

        <div id="main-select-split" class="yeeui-select-split" style="float:left;width: 40px; height:100%;">

        </div>

        <div id="main-select" style="width: 280px; float: right; overflow-y:scroll;  vertical-align: top;border: 1px silver solid;padding: 5px;background:#fff; ">
            <table id="main-table" class="yeeui-select-table" border="0" width="100%" style="background:#fff;">
                <tr>
                    <th>已选选项</th>
                </tr>
                <tr v-for="rs in select">
                    <td>
                        <a href="javascript:;" :data-value="rs.value" @click="removeTodo(rs.value)"> <i style="font-size: 14px" class="icofont icofont-close-circled"></i> </a>
                        <span v-text="'['+rs.value+'] '+rs.text"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="form-submit" style="position: fixed; right: 0; bottom: 0; margin: 0px; padding: 10px 0; text-align: right;">

        <input id="select-btn" type="button" class="form-btn submit" value="确定"/>
        <a id="close-btn" href="javascript:;" style="margin-right: 10px" class="form-btn back">关闭</a>

    </div>
</div>
{block name='foot'}{/block}
{literal}
    <script>
        var assignList = null;
        var app = null;

        var updateHeight = function () {
            var winH = $(window).height();
            if (winH < 100) {
                window.setTimeout(updateHeight, 500);
            }
            var height = winH - 120;
            $('#main-layout').height(height);
            $('#main-select').height(height);
            $('#main-select-split').height(height);
        }
        $(window).resize(function () {
            updateHeight();
        });
        updateHeight();

        $('#close-btn').on('click', function () {
            if (window.YeeDialog) {
                window.closeYeeDialog();
            }
        });

        $('#select-btn').on('click', function () {
            if (window.YeeDialog) {
                var values = [];
                var texts = [];
                for (var i = 0, len = app.select.length; i < len; i++) {
                    values.push(app.select[i].value);
                    texts.push({value: app.select[i].value, text: app.select[i].text});
                }
                window.success({value: JSON.stringify(values), text: texts});
                window.closeYeeDialog();
            }
        });


        $('#list').on('change', function (ev, source) {
            if (source) {
                $('#recordsCount').text(source.pdata['recordsCount']);
            }
            $('.reload').on('success', function (ev) {
                $('#list').trigger('reload');
            });
            if (app) {
                for (var i = 0, len = app.select.length; i < len; i++) {
                    var rs = app.select[i];
                    $(':checkbox.check-item[value=' + rs.value + ']').prop('checked', true);
                }
            }
        });

        Yee.loader('vue.min.js', function () {
            app = new Vue({
                data: {select: {}},
                el: '#main-table',
                methods: {
                    removeTodo: function (index) {
                        var data = [];
                        for (var i = 0, len = app.select.length; i < len; i++) {
                            var rs = app.select[i];
                            if (rs.value != index) {
                                data.push(rs);
                            }
                        }
                        this.select = data;
                        this.$nextTick(function () {
                            $(':checkbox.check-item[value=' + index + ']').prop('checked', false);
                        });
                    }
                }
            });
            if (assignList) {
                app.select = assignList;
            }
        });


        function setItem(box, checked) {
            var val = box.val();
            var text = box.data('text');
            if (!checked) {
                var data = [];
                for (var i = 0, len = app.select.length; i < len; i++) {
                    var rs = app.select[i];
                    if (rs.value != val) {
                        data.push(rs);
                    }
                }
                app.select = data;
            } else {

                var data = [];
                for (var i = 0, len = app.select.length; i < len; i++) {
                    var rs = app.select[i];
                    if (rs.value != val) {
                        data.push(rs);
                    }
                }
                data.push({value: val, text: text});
                app.select = data;
            }
        }

        $('#list').on('click', '.check-item', function () {
            var box = $(this);
            setItem(box, box.prop('checked'));
        });

        $('#list').on('click', '.check-all', function () {
            var allbtn = $(this);
            var checked = allbtn.prop('checked');
            $('.check-item').each(function () {
                var box = $(this);
                setItem(box, checked);
            });
        });

        window.readyYeeDialog = function (assign) {
            assignList = assign.text || [];
            if (app) {
                app.select = assignList;
            }
        }
    </script>
{/literal}
</body>
</html>