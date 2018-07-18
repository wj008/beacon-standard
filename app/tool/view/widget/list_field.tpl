{hack fn='plugin-layer' field=null form=null lastIndex='0' content='' code=''}
    <div class="form-group" id="row_{$field->boxId}" yee-module="plugin"
         data-index="{$lastIndex}"{if $field->dataMinSize} data-min-size="{$field->dataMinSize}"{/if}{if $field->dataMaxSize} data-max-size="{$field->dataMaxSize}"{/if} data-source="{$code}">
        <label class="form-label">{$field->label}：</label>
        <div class="plugin-content">{$content|raw}</div>
        <div class="form-plugin">
            <a href="javascript:;" class="yee-btn button plugin-add"><i class="icofont icofont-plus-circle"></i>新增行</a>

            <label style="line-height: 20px;  height: 20px; display: inline-block; padding: 2px 7px; border: solid 1px #c1c1c1; border-radius: 4px; margin-left: 10px; color: #777; opacity: 0.9;">
                <input id="select-all-btn" type="checkbox" class="selectCopy"> 全选
            </label>

            <a id="copy-btn" href="javascript:;" class="yee-btn show"><i class="icofont icofont-copy-alt"></i>拷贝</a>
            <a id="paste-btn" href="javascript:;" class="yee-btn show"><i class="icofont icofont-copy-black"></i>黏贴</a>
            <a id="delall-btn" href="javascript:;" class="yee-btn show"><i class="icofont icofont-copy-black"></i>删除选择</a>
            <span id="{$field->boxId}-validation"></span>

        </div>
    </div>
{/hack}

{hack fn='plugin-item' field=null form=null index='@@index@@'}
    <div class="form-item plugin-item">
        <div class="form-inline" style="background:#f8f8f8; display: block;">
            <label class="form-label inline" style="text-align: left;">&nbsp;&nbsp; <input type="checkbox" class="select-item">&nbsp;&nbsp;&nbsp;第 <span class="plugin-index red2" style="font-size: 18px;"></span>
                项&nbsp;&nbsp;&nbsp;</label>
            <a href="javascript:;" class="yee-btn plugin-del"><i class="icofont icofont-minus-circle"></i>移除</a>
            <a href="javascript:;" class="yee-btn plugin-insert"><i class="icofont icofont-puzzle"></i>插入</a>
            <a href="javascript:;" class="yee-btn plugin-upsort"><i class="icofont icofont-long-arrow-up"></i>上移</a>
            <a href="javascript:;" class="yee-btn plugin-dnsort"><i class="icofont icofont-long-arrow-down"></i>下移</a>
        </div>
        <div class="form-plugin intable clearfix">
            <div style="float:left; width:550px">
                <div class="form-group">
                    <label class="form-label" style="width: 60px">标题：</label>
                    <div class="form-box">
                        {box field=$form->getField('title')} 排序：{box field=$form->getField('orderName')  class="form-inp select orderfield"}
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" style="width: 60px">TH属性：</label>
                    <div class="form-box">
                        {box field=$form->getField('thAlign')} {box field=$form->getField('thWidth')} {box field=$form->getField('thAttrs')}
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" style="width: 60px">TD属性：</label>
                    <div class="form-box">
                        {box field=$form->getField('tdAlign')} {box field=$form->getField('tdAttrs')}
                    </div>
                </div>
            </div>
            <div style="float:left; width: 550px">
                <div class="form-group">
                    <label class="form-label" style="width: 60px">字段：</label>
                    <div class="form-box">
                        {box field=$form->getField('field') class="form-inp select dbfield"} 键名：{box field=$form->getField('keyname')}
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" style="width: 60px">复合值：</label>
                    <div class="form-box">
                        {box field=$form->getField('code')}
                    </div>
                </div>
            </div>
            {foreach from=$form->getHideBox() item=value key=name}
                <input type="hidden" name="{$name}" value="{$value}"/>
            {/foreach}
        </div>
    </div>
{/hack}
