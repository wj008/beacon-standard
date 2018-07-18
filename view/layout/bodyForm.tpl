<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$form->title}</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
<body>
{include file='../widget/includeBox.tpl'}
<div class="yeeui-caption">{if $form->viewNotBack==false && !empty($this->getReferrer())}<a href="{$this->getReferrer()}" class="s-back"><i class="icofont icofont-reply"></i></a>&nbsp;&nbsp;{/if}{$form->caption}</div>
<div class="yeeui-content">
    <div class="yeeui-form">
        {if $form->viewUseTab}
            <div class="yeeui-tabs">
                <ul yee-module="tabs">
                    {foreach from=$form->viewTabs item=text key=name}
                        <li data-bind-name="{$name}"><a href="javascript:void(0);">{$text}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        <form method="post" yee-module="validate{if $form->useAjax} ajaxform{/if}" {if isset($form->validateMode) && $form->validateMode>0} data-validate-mode="{$form->validateMode}"{/if}>
            <div class="form-panel">
                {if $form->viewUseTab}
                    {if isset($form->viewDescription)&&$form->viewDescription}
                        <div class="form-description">{$form->viewDescription|raw}</div>{/if}
                    {foreach from=$form->viewTabs item=text key=name}
                        <div class="panel-content" name="{$name}">
                            {call fn=formPanel fields=$form->getViewFields($name)}
                        </div>
                    {/foreach}
                {else}
                    <div class="panel-title">
                        <i class="icofont icofont-pencil-alt-3"></i>
                        <h3>{$form->getType()|select:['add'=>'新增','edit'=>'编辑']}{$form->title}</h3>
                    </div>
                    <div class="panel-content">
                        {if isset($form->viewDescription)&&$form->viewDescription}
                            <div class="form-description">{$form->viewDescription|raw}</div>{/if}
                        {call fn=formPanel fields=$form->getViewFields()}
                    </div>
                {/if}
                <div class="form-submit">
                    <label class="form-label"></label>
                    <div class="form-box">
                        <input type="submit" class="form-btn submit" value="提交"/>
                        {if $form->viewNotBack==false && !empty($this->getReferrer())}<input type="hidden" name="__BACK__" value="{$this->getReferrer()}"/>{/if}
                        {foreach from=$form->getHideBox() item=value key=name}
                            <input type="hidden" name="{$name}" value="{$value}"/>
                        {/foreach}
                        {if $form->viewNotBack==false && !empty($this->getReferrer())}<a href="{$this->getReferrer()}" class="form-btn back">返回</a>{/if}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{if $form->viewScript}
    <script>
        {$form->viewScript|raw}
    </script>
{/if}
{literal}
    <script>
        window.readyYeeDialog = function () {
            var notBack = !($(':input[name="__BACK__"]').val() || '');
            $('html').addClass('yeeui-form-dialog');
            if (notBack) {
                var btn = $('.form-btn.back').text('关闭');
                btn.on('click', function () {
                    window.closeYeeDialog();
                });
            }
            $('form').on('success', function () {
                window.success();
                if (notBack) {
                    window.closeYeeDialog();
                }
            });
        }
    </script>
{/literal}
</body>
</html>