{function fn=nextbox box=null}
    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}
    {if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
    {else}
        <div class="form-inline" id="row_{$box->boxId}">
            <label class="form-label inline">{$box->label}：</label>
            <span style="margin-right: 10px">{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
        </div>
    {/if}
    {if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
{function fn=prevbox box=null}
    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}
    {if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
    {else}
        <div class="form-inline" id="row_{$box->boxId}">
            <label class="form-label inline">{$box->label}：</label>
            <span style="margin-right: 10px">{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
        </div>
    {/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
{hack fn='plugin-layer' field=null form=null lastIndex='0' content='' code=''}
    <div class="form-group" id="row_{$field->boxId}" yee-module="plugin"
         data-index="{$lastIndex}"{if $field->dataMinSize} data-min-size="{$field->dataMinSize}"{/if}{if $field->dataMaxSize} data-max-size="{$field->dataMaxSize}"{/if} data-source="{$code}">
        <div class="form-plugin">
            <label class="form-label">{$field->label}：</label>
            <div class="form-box"><a href="javascript:;" class="form-inp button plugin-add"><i class="icofont icofont-plus-circle"></i>新增行</a>{if $field->tips}<span class="field-tips">{$field->tips}</span>{/if} <span id="{$field->boxId}-validation"></span></div>
        </div>
        <div class="plugin-content">{$content|raw}</div>
    </div>
{/hack}
{hack fn='plugin-item' field=null form=null index='@@index@@'}
    <div class="form-item plugin-item" style="margin-bottom: 3px; padding-left: 0; padding-right: 0;">
        <div class="form-inline" style="background:#f8f8f8; display: block;">
            <label class="form-label" style="text-align: left;">&nbsp;&nbsp;&nbsp;第 <span class="plugin-index red2" style="font-size: 18px;"></span> 项</label>
            <div class="form-box">
                {if $field->viewShowRemoveBtn}<a href="javascript:;" class="form-inp button plugin-del"><i class="icofont icofont-minus-circle"></i>移除</a>{/if}
                {if $field->viewShowInsertBtn}<a href="javascript:;" class="form-inp button plugin-insert"><i class="icofont icofont-puzzle"></i>插入</a>{/if}
                {if $field->viewShowSortBtn}
                    <a href="javascript:;" class="form-inp button plugin-upsort"><i class="icofont icofont-long-arrow-up"></i>上移</a>
                    <a href="javascript:;" class="form-inp button plugin-dnsort"><i class="icofont icofont-long-arrow-down"></i>下移</a>
                {/if}
            </div>
        </div>
        {foreach from=$form->getViewFields() item=box}
            <div class="form-group" id="row_{$box->boxId}">
                <label class="form-label">{if $box->type!='button'}{$box->label}：{/if}</label>
                <div class="form-box">
                    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}
                    {if $box->type=='textarea'}
                        <div style="margin-bottom: 3px">{box field=$box}</div>
                    {else}
                        {box field=$box}
                    {/if}
                    {if $box->next}{call fn=nextbox box=$box->next}{/if}
                    {if $box->dataVal}<span id="{$box->boxId}-validation"></span>{/if}
                    {if $box->tips}<p class="field-tips {$box->type}">{$box->tips}</p>{/if}
                </div>
            </div>
        {/foreach}
        {foreach from=$form->getHideBox() item=value key=name}
            <input type="hidden" name="{$name}" value="{$value}"/>
        {/foreach}
    </div>
{/hack}
