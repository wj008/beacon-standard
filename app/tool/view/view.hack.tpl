{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='title' rs=null}{$rs.title}{/hack}
{hack fn='caption' rs=null}{$rs.caption}{/hack}
{hack fn='action' rs=null}{$rs.action}{/hack}
{hack fn='_operate' rs=null}
    <a href="{url ctl='tool_view_field' viewId=$rs.id}" class="yee-btn small show"><i class="icofont icofont-list"></i>字段管理</a>
    <a href="{url act='edit' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
    <a href="{url act='del' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该项目了吗？" class="yee-btn small del reload"><i class="icofont icofont-bin"></i>删除</a>
{/hack}