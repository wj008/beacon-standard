<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>项目管理</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
<body>
<div class="yeeui-dialog">
    <div class="yeeui-optbtns">
        <div class="fl caption">
            工具-项目管理
        </div>
        <div class="search fr">
            <span> 共 <span id="recordsCount">0</span> 条记录</span>
            <a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>
        </div>
    </div>
    <div class="yeeui-search">
        <form id="searchform" yee-module="searchform" data-bind="#list">
            <div class="form-box">
                <label class="form-label">项目名称：</label>
                <span><input name="name" class="form-inp text" type="text"/></span>
            </div>
            <div class="form-box">
                <input class="form-btn blue" value="查询" type="submit"/>
                <input class="form-btn normal" value="重置" type="reset"/>
                <input type="hidden" name="sort">
            </div>
        </form>
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
                <th width="100">选择</th>
                <th width="40" data-order="id">ID</th>
                <th width="200" align="left" data-order="name">项目名称</th>
                <th width="400" align="left">命名空间</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="rs in list">
                <td align="center" class="opt-btns" v-html="rs._select"></td>
                <td align="center" v-html="rs.id"></td>
                <td v-html="rs.name"></td>
                <td v-html="rs.namespace"></td>
            </tr>
            <tr v-if="list.length==0">
                <td colspan="1000"> 没有任何数据信息....</td>
            </tr>
            </tbody>
        </table>

        <div yee-module="pagebar" data-bind="#list" class="yeeui-pagebar">
            <div class="pagebar" v-name="bar"></div>
            <div class="pagebar_info">
                共有信息：<span v-name="count"></span> 页次：<span v-name="page"></span>/<span v-name="pageCount"></span> 每页
                <span v-name="pageSize"></span>
            </div>
        </div>
    </div>
</div>
{literal}
    <script>
        $('#list').on('change', function (ev, source) {
            if (source) {
                $('#recordsCount').text(source.pdata['recordsCount']);
            }
        });
    </script>
{/literal}
</body>
</html>