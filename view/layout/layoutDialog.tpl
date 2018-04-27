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
<div class="yeeui-content yeeui-dialog">
    {block name='listHead'}{/block}
    {block name='ListTab'}{/block}
    {block name='listSearch'}{/block}
    <div class="yeeui-list">
        {block name='listTable'}{/block}
        {block name='pagebar'}{/block}
    </div>
</div>
{block name='foot'}{/block}
{literal}
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