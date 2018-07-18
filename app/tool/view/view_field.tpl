<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>详情内页字段管理</title>
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
            <a href="{url ctl='toolList'}" class="s-back"><i class="icofont icofont-reply"></i></a>&nbsp;&nbsp; 工具-详情内页字段
        </div>
        <div class="search fr">
            <a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>
            <a id="add-btn" href="{url act='add' viewId=$viewId}" class="yee-btn add"><i class="icofont icofont-ui-add"></i>新增字段</a>
        </div>
    </div>

    <div class="yeeui-search">
        <div style="text-align: right; float:left;">
            <form id="searchform" yee-module="searchform" data-bind="#list">
                <div class="form-box">
                    <label class="form-label">字段名称：</label>
                    <span><input name="name" class="form-inp text" type="text"/></span>
                </div>
                <div class="form-box">
                    <input class="form-btn blue" value="查询" type="submit"/>
                    <input class="form-btn normal" value="重置" type="reset"/>
                    <input type="hidden" id="viewId" name="listId" value="{$viewId}">
                </div>
            </form>
        </div>
        <div style="text-align: right; float:right;">
            <a id="add-btn" href="{url ctl='toolView' act='index'}" class="yee-btn show">返回列表</a>
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
                <th width="40" data-order="id">ID</th>
                <th width="200" align="left" data-order="name">字段名称</th>
                <th width="300">字段类型</th>
                <th width="30" align="left" data-order="sort">排序</th>
                <th width="180" data-fixed="right">操作</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="rs in list">
                <td align="center" v-html="rs.id"></td>
                <td v-html="rs.label"></td>
                <td v-html="rs.type"></td>
                <td v-html="rs._sort"></td>
                <td class="opt-btns" v-html="rs._operate"></td>
            </tr>
            <tr v-if="list.length==0">
                <td colspan="1000"> 没有任何数据信息....</td>
            </tr>
            </tbody>

        </table>
    </div>
</div>

{literal}
    <script>
        $('#list').on('change', function (ev, source) {
            $('.reload').on('success', function (ev) {
                $('#list').trigger('reload');
            });
        });
    </script>
{/literal}
</body>
</html>