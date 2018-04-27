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
<body>
<div class="yeeui-caption">{block name="caption"}{/block}</div>
<div class="yeeui-content">
    {block name='list_head'}{/block}

    {block name='list_search'}{/block}

    <div class="yeeui-list">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="yeeui-list-table">
            <thead>
            <tr>
                {block name='table_ths'}{/block}
            </tr>
            </thead>
            <tbody id="list" yee-module="list">
            {block name='table_tds'}{/block}
            </tbody>
            <tfoot>
            {block name='allopts'}{/block}
            </tfoot>
        </table>
        {block name='pagebar'}
            {if $pdata}
                <div yee-module="pagebar" data-bind="#list" data-info="{json_encode($pdata)}" class="yeeui-pagebar">
                    <div class="pagebar" v-name="bar"></div>
                    <div class="pagebar_info">
                        共有信息：<span v-name="count"></span> 页次：<span v-name="page"></span>/<span v-name="page_count"></span> 每页
                        <span v-name="page_size"></span>
                    </div>
                </div>
            {/if}
        {/block}
    </div>

</div>
{block name='foot'}{/block}

{literal}
    <script>
        $('#list').on('change', function (ev, source) {
            if (source) {
                $('.pdata-records-count').text(source.pdata.recordsCount);
            }
            $('.reload').on('success', function (ev, data) {
                $('#list').trigger('reload');
            });
        });
        $('#list').trigger('change');
    </script>
{/literal}
</body>
</html>