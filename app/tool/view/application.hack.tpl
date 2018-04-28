{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='name' rs=null}{$rs.name}{/hack}
{hack fn='namespace' rs=null}{$rs.namespace}{/hack}
{hack fn='_operate' rs=null}
    <a href="{url act='make' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要重新生成代码了吗？" onSuccess="$('#list').emit('reload');" class="yee-btn small edit"><i class="icofont icofont-bin"></i>重新生成</a>
    <a href="{url act='edit' id=$rs.id}" onSuccess="$('#list').trigger('reload');" yee-module="dialog" data-width="700" data-height="400" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
    <a href="{url act='del' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该项目了吗？" class="yee-btn small del reload"><i class="icofont icofont-bin"></i>删除</a>
{/hack}
{hack fn='_select' rs=null}<a class="yee-btn" href="javascript:;" yee-module="select_dialog" data-value="{$rs.id}" data-text="{$rs.name} ({$rs.namespace})">选择该项</a>{/hack}