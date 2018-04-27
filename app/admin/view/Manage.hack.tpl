{hack fn='_selbox' rs=null}<input type="checkbox" class="check-item" name="sel_id" value="{$rs.id}">{/hack}
{hack fn='id' rs=null}{$rs.id}{/hack}
{hack fn='name' rs=null}{$rs.name}{/hack}
{hack fn='realname' rs=null}{$rs.realname}{/hack}
{hack fn='email' rs=null}{$rs.email}{/hack}
{hack fn='type' rs=null}{$rs.type|select:[1=>'后台管理员',2=>'普通管理员'],'其他管理员'}{/hack}
{hack fn='islock' rs=null}{$rs.islock|equal:1,'锁定','正常'}{/hack}
{hack fn='lasttime' rs=null}{$rs.lasttime|date_format:'Y-m-d H:i:s'}{/hack}
{hack fn='lastip' rs=null}{$rs.lastip}{/hack}
{hack fn='_operate' rs=null}
    <a href="{url act='edit' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>
{if $rs.id != 1}
    <a href="{url act='del' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该账号了吗？" onsuccess="$('#list').trigger('reload');" class="yee-btn small del"><i class="icofont icofont-bin"></i>删除</a>
{/if}
{/hack}
