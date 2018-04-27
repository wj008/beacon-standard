{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='icon' rs=null}{if $rs.icon}<i class="{$rs.icon}"></i>{/if}{/hack}
{hack fn='title' rs=null}{$rs.title|raw}{/hack}
{hack fn='url' rs=null}{$rs.url}{/hack}
{hack fn='_sort' rs=null}<input class="form-inp small tc snumber" name="sort" value="{$rs.sort}" yee-module="editbox" data-href="{url act='editSort' id=$rs.id}"/>{/hack}
{hack fn='_allow' rs=null}{if $rs.allow}<span class="ifont green">&#xed27;</span>{else}<span class="ifont">&#xed2b;</span>{/if}{/hack}
{hack fn='_operate' rs=null}
{if $rs.create}<a href="{url act='add' pid=$rs.id}" class="yee-btn small"><i class="icofont icofont-ui-add"></i>添加子项</a>{/if}
    <a href="{url act='edit' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
    <a href="{url act='delete' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该菜单了吗？" class="yee-btn small del reload"><i class="icofont icofont-bin"></i>删除</a>
{/hack}