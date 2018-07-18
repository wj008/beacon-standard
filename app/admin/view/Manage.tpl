{extends file='layoutDataTable.tpl'}
{block name="title"}管理员{/block}
{block name="caption"} 管理员-账号管理{/block}

{block name='listHead'}
    <div class="yeeui-optbtns">
        <div class="fl caption">
            管理员-账号管理
        </div>
        <div class="search fr">
            <span> 共 <span id="records_count">0</span> 条记录</span>
            <a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>
            <a id="add-del" href="{url act='selDel'}"
               yee-module="confirm ajaxlink"
               onsuccess="$('#list').trigger('reload');"
               data-confirm="确定要删除所选条目了吗？"
               data-batch="sel_id" class="yee-btn del"><i class="icofont icofont-bin"></i>删除所选</a>
            <a id="add-btn" href="{url act='add'}" class="yee-btn add"><i class="icofont icofont-ui-add"></i>添加账号</a>
        </div>
    </div>
    {*
    <div class="yeeui-tabs">
        <ul>
            <li class="curr"><a href="#">测试1</a></li>
            <li><a href="#">测试2</a></li>
            <li><a href="#">测试3</a></li>
            <li><a href="#">测试4</a></li>
        </ul>
    </div>
    *}
{/block}
{block name='listSearch'}
    <div class="yeeui-search">
        <form id="searchform" yee-module="searchform" data-bind="#list">
            <div class="form-box">
                <label class="form-label"><em></em>用户名/真实姓名：</label>
                <span><input name="name" class="form-inp text" type="text"/></span>
            </div>
            <div class="form-box">
                <input class="form-btn blue" value="查询" type="submit"/>
                <input class="form-btn normal" value="重置" type="reset"/>
                <input type="hidden" name="sort">
                <a class="form-btn normal senior-btn" onclick="$('.yeeui-search').toggleClass('senior')">高级搜索<i></i></a>
            </div>
        </form>
    </div>
{/block}
{block name='listTable'}
    <table id="list" cellspacing="0" cellpadding="0" border="0" class="yee-datatable" yee-module="datatable" data-bind-form="#searchform"
           data-auto-load="true"
           data-send-order="sort"
    >
        <thead>
        <tr>
            <th width="40"><input type="checkbox" class="check-all"></th>
            <th width="40" data-order="id">ID</th>
            <th width="150" align="left" data-order="name">账号名称</th>
            <th width="150" align="left" data-order="realname">真实姓名</th>
            <th width="100" class="sort down">电子邮箱</th>
            <th width="80" data-order="type">类型</th>
            <th width="80">状态</th>
            <th width="180">上次登录时间</th>
            <th width="180">上次登录IP</th>
            <th width="180" data-fixed="right">操作</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="rs in list">
            <td align="center" v-html="rs._selbox"></td>
            <td v-html="rs.id"></td>
            <td v-html="rs.name"></td>
            <td v-html="rs.realname"></td>
            <td v-html="rs.email"></td>
            <td align="center" v-html="rs.type"></td>
            <td align="center" v-html="rs.islock"></td>
            <td align="center" v-html="rs.lasttime"></td>
            <td align="center" v-html="rs.lastip"></td>
            <td class="opt-btns" v-html="rs._operate"></td>
        </tr>
    </table>
{/block}