{extends file='InstallLayout.tpl'}
{block name='content'}
    {include file='../widget/includeBox.tpl'}
    <h2>数据库配置</h2>
    <div class="content">
        {call fn=formPanel fields=$form->getViewFields()}
    </div>
{/block}