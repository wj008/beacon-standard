<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>列表搜索字段</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
<body>
<div class="yeeui-content">
    <div class="yeeui-optbtns">
        <div class="fl caption">
            <a href="{url ctl='toolList'}" class="s-back"><i class="icofont icofont-reply"></i></a>&nbsp;&nbsp; 工具-列表搜索字段
        </div>
        <div class="search fr">
            <span> 共 <span id="records_count">0</span> 条记录</span>
            <a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>
            <a id="add-btn" href="{url act='add' listId=$listId}" class="yee-btn add"><i class="icofont icofont-ui-add"></i>新增字段</a>
        </div>
    </div>

    <div class="yeeui-search">
        <div style="text-align: right; float:left;">
            <form id="searchform" yee-module="searchform" data-bind="#list">
                <div class="form-box">
                    <label class="form-label">项目名称：</label>
                    <span><input name="name" class="form-inp text" type="text"/></span>
                </div>
                <div class="form-box">
                    <label class="form-label">类型：</label>
                    <span>{box name="viewTabIndex" class="form-inp text" type="select" header='所有' options=[['base','基本配置'],['senior','高级搜索']]}</span>
                </div>
                <div class="form-box">
                    <input class="form-btn blue" value="查询" type="submit"/>
                    <input class="form-btn normal" value="重置" type="reset"/>
                    <input type="hidden" name="sort">
                    <input type="hidden" id="listId" name="listId" value="{$listId}">
                </div>
            </form>
        </div>
        <div style="text-align: right; float:right;">
            <a href="{url act='selectField' listId=$listId}" class="yee-btn show"
               yee-module="dialog" data-width="1000" data-height="600"
               onsuccess="$('#list').trigger('reload');"
            ><i class="icofont icofont-copy-alt"></i>从表单模型拷贝</a>

            <a id="copy-btn" href="javascript:;" class="yee-btn show"><i class="icofont icofont-copy-alt"></i>拷贝</a>
            <a id="paste-btn" href="javascript:;" class="yee-btn show"><i class="icofont icofont-copy-black"></i>黏贴</a>

            <a id="add-del" href="{url act='delSelect'}"
               yee-module="confirm ajaxlink"
               onsuccess="$('#list').trigger('reload');"
               data-confirm="确定要删除所选条目了吗？"
               data-batch="sel_id" class="yee-btn del" style="margin-right: 20px"><i class="icofont icofont-bin"></i>删除所选</a>
            <a id="add-btn" href="{url ctl='toolList' act='index'}" class="yee-btn show">返回列表</a>
            <a href="{url  ctl='toolList' act='edit' id=$listId}" yee-module="dialog" data-width="1400" data-height="900" class="yee-btn edit"><i class="icofont icofont-edit"></i>编辑</a>
        </div>
        <div class="clear"></div>
    </div>


    <div class="yeeui-list">
        <table id="list" class="yee-datatable" yee-module="datatable"
               data-fill-bottom="80"
               data-auto-load="true"
               data-send-order="sort"
               data-bind-form="#searchform"
               cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <th width="40"><input type="checkbox" class="check-all"></th>
                <th width="40" data-order="id">ID</th>
                <th width="200" align="left" data-order="name">字段名称</th>
                <th width="300" align="left">字段名</th>
                <th width="300">字段类型</th>
                <th width="300">所属TAB</th>
                <th width="30" align="left" data-order="sort">排序</th>
                <th width="180" data-fixed="right">操作</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="rs in list">
                <td align="center" v-html="rs._selbox"></td>
                <td align="center" v-html="rs.id"></td>
                <td v-html="rs.label"></td>
                <td v-html="rs.name"></td>
                <td align="center" v-html="rs.type"></td>
                <td align="center" v-html="rs.viewTabIndex"></td>
                <td v-html="rs._sort"></td>
                <td class="opt-btns" v-html="rs._operate"></td>
            </tr>
            <tr v-if="list.length==0">
                <td colspan="1000"> 没有任何数据信息....</td>
            </tr>
            </tbody>

        </table>

        <div yee-module="pagebar" data-bind="#list" class="yeeui-pagebar">
            <div class="pagebar" v-name="bar"></div>
            <div class="pagebar_info">
                共有信息：<span v-name="count"></span> 页次：<span v-name="page"></span>/<span v-name="page_count"></span> 每页
                <span v-name="page_size"></span>
            </div>
        </div>
    </div>
</div>

{literal left='<@' right='@>'}
    <script>
        $('#list').on('change', function (ev, source) {
            if (source) {
                $('#records_count').text(source.pdata['records_count']);
            }
            $('.reload').on('success', function (ev) {
                $('#list').trigger('reload');
            });
        });

        Yee.loader('yee-layer', function () {

            var copyFunc = function () {
                var boxs = $('.check-item:checked');
                var vals = [];
                boxs.each(function (idx, item) {
                    vals.push($(item).val());
                });
                if (vals.length == 0) {
                    layer.alert('复制失败，没有勾选任何选项');
                    return;
                }
                if (window.clipboardData) {
                    window.clipboardData.setData('text', JSON.stringify({cptype: 'search_field', fids: vals}));
                    layer.msg('复制成功');
                } else if (window.localStorage) {
                    window.localStorage.setItem('copyText', JSON.stringify({cptype: 'search_field', fids: vals}));
                    layer.msg('复制成功');
                } else {
                    layer.msg('浏览器不支持复制');
                }
            };
            var pasteFunc = function () {
                var data = null;
                if (window.clipboardData) {
                    data = window.clipboardData.getData('text');
                } else if (window.localStorage) {
                    data = window.localStorage.getItem('copyText');
                }
                if (data == null) {
                    return;
                }
                try {
                    data = JSON.parse(data);
                    if (data['cptype'] && data['cptype'] == 'search_field' && data['fids']) {
                        var listId = $('#listId').val();
                        data['listId'] = listId;
                        var idx = layer.confirm('确定要黏贴字段了吗？', function () {
                            $.post('<@url act="copy"@>', data, function (ret) {
                                if (ret && ret.status) {
                                    layer.msg(ret.message, {icon: 1, time: 1000});
                                    $('#list').trigger('reload');
                                } else {
                                    layer.msg(ret.error, {icon: 0, time: 2000});
                                }
                                layer.close(idx);
                            });
                        });
                    }
                } catch (e) {

                }
            }
            $('#copy-btn').on('click', copyFunc);
            $('#paste-btn').on('click', pasteFunc);

        });
        window.readyYeeDialog = function () {
            $('.yeeui-content').removeClass('yeeui-content').addClass('yeeui-dialog');
        }
    </script>
{/literal}
</body>
</html>