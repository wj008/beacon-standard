{function fn=nextbox box=null}
    {if $box->prev}{call fn=prevbox box=$box->prev}{/if}
    {if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
    {else}<span style="margin: 0 5px">{box field=$box}</span>
    {/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
{function fn=prevbox box=null}{if $box->prev}{call fn=prevbox box=$box->prev}{/if}
    {if $box->type=='button'}{box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
    {else}<span style="margin: 0 5px">{box field=$box}</span>
    {/if}{if $box->next}{call fn=nextbox box=$box->next}{/if}
{/function}
<div class="form-group" id="row_{$field->boxId}">
    <label class="form-label">{$field->label}：</label>
    <div class="form-plugin simple">
        {foreach from=$form->getViewFields() item=box}
            <div class="form-inline" id="row_{$box->boxId}">{if $box->prev}{call fn=prevbox box=$box->prev}{/if}
                {box field=$box}{if $box->tips}<span class="field-tips">{$box->tips}</span>{/if}
                {if $box->next}{call fn=nextbox box=$box->next}{/if}
            </div>
        {/foreach}
        <span id="{$field->boxId}-validation"></span>
    </div>
</div>
