{function fn=nextbox box=null}{if $box->prev}{call fn=prevbox box=$box->prev}{/if}{if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
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
<div class="form-group" id="row_{$field->boxId}">
    <label class="form-label">{$field->label}：</label>
    <div class="form-plugin inline">
        {foreach from=$form->getViewFields() item=box}
            <div class="form-inline" id="row_{$box->boxId}">
                <label class="form-label inline">{$box->label}：</label>
                <span style="margin-right: 10px">{if $box->prev}{call fn=prevbox box=$box->prev}{/if}{box field=$box}{if $box->next}{call fn=nextbox box=$box->next}{/if}
                    {if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}</span>
            </div>
        {/foreach}
        <span id="{$field->boxId}-validation"></span>
    </div>
</div>
