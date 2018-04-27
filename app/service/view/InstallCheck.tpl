{extends file='InstallLayout.tpl'}
{block name='content'}
    <h2>检查文件权限</h2>
    <div class="content">
        {$info|raw}
    </div>
{/block}

{block name='btn'}
    <input type="button" class="form-btn back" onclick="window.location='/service/install/index';" value="上一步"/>
    {if $ok}
        <input type="button" class="form-btn submit" onclick="window.location='/service/install/database';" value="下一步"/>
    {else}
        <input type="button" class="form-btn submit" onclick="window.location='/service/install/database';" value="下一步"/>
        <input type="button" class="form-btn submit" onclick="window.location='/service/install/check';" value="重新检查"/>
    {/if}
{/block}