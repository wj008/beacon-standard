{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='label' rs=null}{$rs.label}{/hack}
{hack fn='type' rs=null}{$rs.type}{/hack}
{hack fn='_sort' rs=null}<input value="{$rs.sort}" name="sort" type="text" class="form-inp snumber" yee-module="integer editbox" data-href="{url act='editSort'}?id={$rs.id}"/>{/hack}
{hack fn='_operate' rs=null}
    <a href="{url act='edit' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
    <a href="{url act='del' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该项目了吗？" class="yee-btn small del reload"><i class="icofont icofont-bin"></i>删除</a>
{/hack}