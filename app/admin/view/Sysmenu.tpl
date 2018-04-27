{extends file='layoutDataTable.tpl'}
{block name="title"}系统菜单{/block}
{block name='listHead'}
    <div class="yeeui-optbtns">
        <div class="fl caption">
            系统-菜单管理
        </div>
        <div class="search fr">
            <span> 共 <span id="recordsCount">0</span> 条记录</span>
            <a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>
            <a id="add-del" href="{url act='selDel'}"
               yee-module="confirm ajaxlink"
               onsuccess="$('#list').trigger('reload');"
               data-confirm="确定要删除所选条目了吗？"
               data-batch="sel_id" class="yee-btn del"><i class="icofont icofont-bin"></i>删除所选</a>
            <a id="add-btn" href="{url act='add'}" class="yee-btn add"><i class="icofont icofont-ui-add"></i>添加栏目</a>
        </div>
    </div>
{/block}
{block name='listSearch'}
    <div class="yeeui-search">
        <form id="searchform" yee-module="searchform" data-bind="#list">
            <div class="form-box">
                <label class="form-label"><em></em>类别名称：</label>
                <span><input name="name" class="form-inp text" type="text"/></span>
            </div>
            <div class="form-box">
                <input class="form-btn blue" value="查询" type="submit"/>
                <input class="form-btn normal" value="重置" type="reset"/>
                <input type="hidden" name="sort">
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
            <th width="40">ID</th>
            <th width="40">ICON</th>
            <th align="left">菜单名称</th>
            <th width="100">路径</th>
            <th width="80">排序</th>
            <th width="80">状态</th>
            <th width="250" width="180" data-fixed="right">操作</th>

        </tr>
        </thead>
        <tbody>
        <tr v-for="rs in list">
            <td align="center" v-html="rs.id"></td>
            <td align="center" v-html="rs.icon"></td>
            <td v-html="rs.title"></td>
            <td align="center" v-html="rs.url"></td>
            <td align="center" v-html="rs._sort"></td>
            <td align="center" v-html="rs._allow"></td>
            <td align="center" v-html="rs._operate"></td>
        </tr>
    </table>
{/block}