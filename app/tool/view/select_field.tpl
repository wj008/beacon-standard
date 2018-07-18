<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>表单字段管理</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
<body>
<div class="yeeui-dialog">

    <div class="yeeui-search">
        <div style="text-align: right; float:left;">
            <form id="searchform" yee-module="searchform" data-bind="#list">
                <div class="form-box">
                    <label class="form-label">项目名称：</label>
                    <span><input name="name" class="form-inp text" type="text"/></span>
                </div>

                <div class="form-box">
                    <input class="form-btn blue" value="查询" type="submit"/>
                    <input class="form-btn normal" value="重置" type="reset"/>
                    <input type="hidden" name="sort">
                    <input type="hidden" id="formId" name="formId" value="{$formId}">
                </div>
            </form>
        </div>
        <div style="text-align: right; float:right;">

            <a id="copy-select" href="{url act='copySelect' listId=$listId}"
               yee-module="ajaxlink"
                    {literal}  onsuccess="if(window.YeeDialog){ window.success();window.closeYeeDialog();}"  {/literal}
               data-batch="sel_id" class="yee-btn edit"><i class="icofont icofont-copy-black"></i>选择复制字段</a>

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
                <th width="200" align="left" data-order="name">字段标题</th>
                <th width="200">字段名</th>
                <th width="300">字段类型</th>
                <th width="80" align="left" data-order="sort">排序</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="rs in list">
                <td align="center" v-html="rs._selbox"></td>
                <td align="center" v-html="rs.id"></td>
                <td v-html="rs.label"></td>
                <td align="center" v-html="rs.name"></td>
                <td align="center" v-html="rs.type"></td>
                <td v-html="rs.sort"></td>
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

{literal left='<@' right='@>'}
    <script>
        $('#list').on('change', function (ev, source) {
            if (source) {
                $('#recordsCount').text(source.pdata['recordsCount']);
            }
            $('.reload').on('success', function (ev) {
                $('#list').trigger('reload');
            });
        });
    </script>
{/literal}
</body>
</html>