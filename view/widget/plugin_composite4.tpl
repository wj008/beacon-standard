{function fn=nextbox box=null}
    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}
    {if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
    {else}
        <div class="form-inline" id="row_{$box->boxId}">
            <label class="form-label inline">{$box->label}：</label>
            <span style="margin-right: 10px">{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
        </div>
    {/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
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
        <label class="form-label">{$field->label}：</label>
        <div class="plugin-content">{$content|raw}</div>
        <div class="form-plugin">
            <a href="javascript:;" class="form-inp button plugin-add"><i class="icofont icofont-plus-circle"></i>新增行</a> <span id="{$field->boxId}-validation"></span>
        </div>
    </div>
{/hack}
{hack fn='plugin-item' field=null form=null index='@@index@@'}
    <div class="form-item plugin-item">
        {foreach from=$form->getViewFields() item=box}
            <div class="form-inline" id="row_{$box->boxId}">
                <label class="form-label inline">{$box->label}：</label>
                <span style="margin-right: 10px">
                    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}{box field=$box}{if $box->next}{call fn=nextbox box=$box->next}{/if}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
            </div>
        {/foreach}
        <div class="form-inline" style="margin-right: 10px">
            <a href="javascript:;" class="form-inp button plugin-del"><i class="icofont icofont-minus-circle"></i>移除</a>
            {if $field->viewShowInsertBtn}<a href="javascript:;" class="form-inp button plugin-insert"><i class="icofont icofont-puzzle"></i>插入</a>{/if}
            {if $field->viewShowSortBtn}
                <a href="javascript:;" class="form-inp button plugin-upsort"><i class="icofont icofont-long-arrow-up"></i>上移</a>
                <a href="javascript:;" class="form-inp button plugin-dnsort"><i class="icofont icofont-long-arrow-down"></i>下移</a>
            {/if}
        </div>
        {foreach from=$form->getHideBox() item=value key=name}
            <input type="hidden" name="{$name}" value="{$value}"/>
        {/foreach}
    </div>
{/hack}
