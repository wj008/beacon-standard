{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='key' rs=null}{$rs.key}{/hack}
{hack fn='title' rs=null}{$rs.title}{/hack}
{hack fn='extMode' rs=null}{$rs.extMode|select:[0=>'普通模式',1=>'<span class="blue">插件模式</span>',2=>'<span class="org">分类层级</span>',3=>'普通模式']|raw}{/hack}
{hack fn='tbName' rs=null}@pf_{$rs.tbName}{/hack}
{hack fn='_operate' rs=null}
    <a href="{url ctl='tool_field' formId=$rs.id}" class="yee-btn small show"><i class="icofont icofont-list"></i>字段管理</a>
    <a href="{url act='code' id=$rs.id}" class="yee-btn small edit" target="_blank"><i class="icofont icofont-code"></i>代码</a>
    <a href="{url ctl='test' act='index' formId=$rs.id}" class="yee-btn small edit" target="_blank"><i class="icofont icofont-paint"></i>测试</a>
    <a href="{url act='add' copyid=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-ui-add"></i>克隆</a>
    <a href="{url act='edit' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
    <a href="{url act='del' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该项目了吗？" class="yee-btn small del reload"><i class="icofont icofont-bin"></i>删除</a>
{/hack}