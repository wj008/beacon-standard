<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Beacon Installer</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
    {literal}
        <style>
            html {
                background: #333;
            }

            body {
                background: #333;
                margin: 0;
                padding: 0;
                font-size: 12px;
                font-family: "微软雅黑", Verdana, Geneva, sans-serif;
            }

            .ratl {
                -webkit-border-top-left-radius: 4px;
                -moz-border-top-left-radius: 4px;
                -ms-border-top-left-radius: 4px;
                -o-border-top-left-radius: 4px;
                border-top-left-radius: 4px;
            }

            .ratr {
                -webkit-border-top-right-radius: 4px;
                -moz-border-top-right-radius: 4px;
                -ms-border-top-right-radius: 4px;
                -o-border-top-right-radius: 4px;
                border-top-right-radius: 4px;
            }

            .rabl {
                -webkit-border-bottom-left-radius: 4px;
                -moz-border-bottom-left-radius: 4px;
                -ms-border-bottom-left-radius: 4px;
                -o-border-bottom-left-radius: 4px;
                border-bottom-left-radius: 4px;
            }

            .rabr {
                -webkit-border-bottom-right-radius: 4px;
                -moz-border-bottom-right-radius: 4px;
                -ms-border-bottom-right-radius: 4px;
                -o-border-bottom-right-radius: 4px;
                border-bottom-right-radius: 4px;
            }

            ul.step {
                margin: 20px;
            }

            ul.step li {
                font-size: 1.1em;
                line-height: 30px;
                list-style: decimal;
            }

            ul.step li.active {
                color: #F60;
            }

            span.yes {
                color: #090;
            }

            span.no {
                color: #A00;
            }

            .main-layout {
                margin: 100px auto;
                width: 800px;
                height: 540px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.8);
            }

            .main-title {
                height: 40px;
                background: #00A2CA;
                position: relative;
                box-shadow: 0 2px 1px rgba(0, 0, 0, 0.1);
                line-height: 40px;
                padding: 15px;
                font-size: 2em;
                color: #fff;
            }

            .form-area {
                height: 470px;
                background: #F8F8F8;
                padding: 0 15px;
            }

            .form-left {
                float: left;
                height: 390px;
                width: 180px;
                padding-top: 30px;
            }

            .form-right {
                height: 390px;
                width: 580px;
                float: right;
            }

            .form-right h2 {
                margin: 0px;
                padding: 0px;
                font-size: 1.2em;
                line-height: 40px;
                height: 40px;
            }

            .form-right .content {
                background: #FFF;
                border: solid 1px #eee;
                padding: 15px;
                height: 350px;
                overflow-y: auto;
                line-height: 22px;
            }

            .foot {
                width: 760px;
                height: 30px
            }

            .foot-left {
                height: 30px;
                float: left;
                color: #CCC;
                line-height: 30px;
                padding: 10px 0 0 15px;
            }

            .foot-right {
                height: 30px;
                float: right;
                line-height: 30px;
                padding: 10px 0 0 15px;
            }

            .form-group .form-label {
                text-align: right;
                vertical-align: top;
                width: 150px;
            }
        </style>
    {/literal}
</head>
<body>
<div class="main-layout">
    <div class="main-title">Beacon基础框架 1.0 安装</div>
    <form method="post" yee-module="ajaxform validate">
        <div class="form-area">
            <div class="form-left">
                <ul class="step">
                    <li {if $this->route('act')=='index'}class="active"{/if}>软件说明</li>
                    <li {if $this->route('act')=='check'}class="active"{/if}>安装环境检测</li>
                    <li {if $this->route('act')=='database'}class="active"{/if}>配置数据库</li>
                </ul>
            </div>
            <div class="form-right">
                {block name='content'}

                {/block}
            </div>
            <div style="clear:both;"></div>
            <div class="foot">
                <div class="foot-left">
                    wj008 (叶子 26029682@qq.com)
                </div>
                <div class="foot-right">
                    {block name='btn'}
                        <input type="submit" class="form-btn submit" value="确定安装"/>
                    {/block}
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>
