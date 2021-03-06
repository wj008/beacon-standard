{function fn=nextbox box=null}
    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}{if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
{else}
    <div class="form-inline" id="row_{$box->boxId}">
        <label class="form-label inline">{$box->label}：</label>
        <span style="margin-right: 10px">{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
    </div>
{/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
{function fn=prevbox box=null}{if $box->prev}{call fn=prevbox box=$box->prev}{/if}{if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
{else}
    <div class="form-inline" id="row_{$box->boxId}">
        <label class="form-label inline">{$box->label}：</label>
        <span style="margin-right: 10px">{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
    </div>
{/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
<div id="row_{$field->boxId}">
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
</div>
</div>
