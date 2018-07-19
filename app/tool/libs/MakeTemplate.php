<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/23
 * Time: 18:47
 */

namespace app\tool\libs;


use beacon\Console;
use beacon\DB;
use beacon\Utils;

class MakeTemplate
{
    private $lrow = null;
    private $out = [];
    private $hack = [];

    public function __construct(int $listId = 0)
    {
        $this->lrow = DB::getRow('select * from @pf_tool_list where id=?', $listId);
        if ($this->lrow == null) {
            throw new \Exception('生成错误');
        }
        $this->createTemplate();
    }

    private function createTemplate()
    {
        if (empty($this->lrow['baseLayout'])) {
            $this->out[] = '{extends file=\'layoutDataTable.tpl\'}';
        } else {
            $this->out[] = '{extends file=\'' . $this->lrow['baseLayout'] . '\'}';
        }
        if ($this->lrow['title']) {
            $this->out[] = "{block name='title'}{$this->lrow['title']}{/block}";
        }
        if (!empty($this->lrow['information'])) {
            $this->out[] = "{block name='information'}<div class='yeeui-information'>{$this->lrow['information']}</div>{/block}";
        }
        if (!empty($this->lrow['attention'])) {
            $this->out[] = "{block name='attention'}<div class='yeeui-attention'>{$this->lrow['attention']}</div>{/block}";
        }
        $this->createListTab();
        $this->createListHead();
        $this->createListSearch();
        $this->createListTable();
        $this->createPagebar();
        $this->createHead();
        $this->createFoot();
    }

    private function createListTab()
    {

        if (!$this->lrow['viewUseTab']) {
            return;
        }
        $tabItems = [];
        if (Utils::isJsonString($this->lrow['viewTabs'])) {
            $tabItems = json_decode($this->lrow['viewTabs'], true);
        }
        $this->out[] = '';
        $this->out[] = "{block name='ListTab'}";
        $this->out[] = '<div class="yeeui-tabs">';
        $this->out[] = '<ul yee-module="tablink">';
        foreach ($tabItems as $idx => $item) {
            if ($item['useCode']) {
                $this->out[] = $item['code'];

            } else {
                if (!empty($item['url']) && ($item['url'][0] == '~' || $item['url'][0] == '^') && isset($item['url'][1]) && $item['url'][1] == '/') {
                    $this->out[] = "<li{if \$this->get('tabIndex:i',0)=={$idx}} class=\"curr\"{/if}><a href=\"{url path=" . var_export($item['url'], true) . "}\" data-tab-index='{$idx}'>" . htmlspecialchars($item['name']) . "</a></li>";
                } else {
                    $this->out[] = "<li{if \$this->get('tabIndex:i',0)=={$idx}} class=\"curr\"{/if}>";
                    $this->out[] = "<a href=\"{$item['url']}\" data-tab-index='{$idx}'>" . htmlspecialchars($item['name']) . "</a>";
                    $this->out[] = "</li>";
                }
            }
        }
        $this->out[] = '</ul>';

        if (!empty($this->lrow['viewTabRight'])) {
            $this->out[] = '<div  class="yeeui-tab-right">';
            $this->out[] = $this->lrow['viewTabRight'];
            $this->out[] = '</div>';
        }

        $this->out[] = '</div>';
        $this->out[] = "{/block}";
    }

    private function createListHead()
    {
        $this->out[] = '';
        $this->out[] = "{block name='listHead'}";
        $this->out[] = '<div class="yeeui-optbtns">';
        $this->out[] = ' <div class="fl caption">' . $this->lrow['caption'] . '</div>';
        $this->out[] = '<div class="fr"><span> 共 <span id="recordsCount">0</span> 条记录</span>';
        $this->out[] = '<a id="refresh-btn" href="javascript:window.location.reload()" title="刷新" style="margin-right: 20px" class="yee-refresh"><i class="icofont icofont-refresh"></i>刷新</a>';
        $topBtns = [];
        if (Utils::isJsonString($this->lrow['topBtns'])) {
            $topBtns = json_decode($this->lrow['topBtns'], true);
        }
        $btnCode = [];
        foreach ($topBtns as $btn) {
            $btnCode[] = $btn['code'];
        }
        $btnHtml = join("\n", $btnCode);
        if ($btnHtml) {
            $this->out[] = $btnHtml;
        }
        $this->out[] = '</div></div>';
        $this->out[] = "{/block}";
    }

    private function createListSearch()
    {
        $listBtns = (isset($this->lrow['selectBtns']) ? $this->lrow['selectBtns'] : []);
        if (Utils::isJsonString($listBtns)) {
            $listBtns = json_decode($listBtns, true);
        }
        $code = [];
        foreach ($listBtns as $btn) {
            $code[] = $btn['code'];
        }
        $this->out[] = '';
        $this->out[] = "{block name='listSearch'}";
        $this->out[] = "{if isset(\$search)}";
        $this->out[] = '{function fn=searchItem box=null}{if $box->prev}{call fn=searchItem box=$box->prev}{/if}';
        $this->out[] = '<label class="form-label">{if isset($box->label[0]) && $box->label[0]!=\'!\'}{$box->label}：{/if}{box field=$box}</label>';
        $this->out[] = '{if $box->next}{call fn=searchItem box=$box->next}{/if}{/function}';

        $this->out[] = '<div class="yeeui-search">';
        if (!empty($code)) {
            $this->out[] = '<div style="text-align: left; float:left;">';
        }
        $this->out[] = '<form id="searchform" yee-module="searchform" data-bind="#list">';
        $this->out[] = '<div class="form-box">';
        $this->out[] = '{foreach from=$search->getViewFields(\'base\') item=box}';
        $this->out[] = '{call fn=searchItem box=$box}';
        $this->out[] = "{/foreach}";
        $this->out[] = '</div>';

        $this->out[] = '{assign $seniorItem=$search->getViewFields(\'senior\')}';
        $this->out[] = '{if count($seniorItem)}';
        $this->out[] = '<div class="senior-item">';
        $this->out[] = '{foreach from=$seniorItem item=box}';
        $this->out[] = '<div class="form-box" style="display: block;">';
        $this->out[] = '{if $box->prev}{call fn=searchItem box=$box->prev}{/if}';
        $this->out[] = '<label class="form-label">{if isset($box->label[0]) && $box->label[0]!=\'!\'}{$box->label}：{/if}{box field=$box}</label>';
        $this->out[] = '{if $box->next}{call fn=searchItem box=$box->next}{/if}';
        $this->out[] = '</div>';
        $this->out[] = "{/foreach}";
        $this->out[] = '</div>';
        $this->out[] = '{/if}';
        $this->out[] = '<div class="form-box">';
        $this->out[] = '<input class="form-btn blue" value="查询" type="submit"/>';
        $this->out[] = '<input class="form-btn normal" value="重置" type="reset"/><input type="hidden" name="sort">';
        $this->out[] = ' {foreach from=$search->getHideBox() item=value key=name}';
        $this->out[] = '<input type="hidden" name="{$name}" value="{$value}"/>';
        $this->out[] = '{/foreach}';
        $this->out[] = '{if count($seniorItem)}';
        $this->out[] = '<a class="form-btn normal senior-btn" onclick="$(\'.yeeui-search\').toggleClass(\'senior\')">高级搜索<i></i></a>';
        $this->out[] = '{/if}';
        $this->out[] = '</div>';

        $this->out[] = '</form>';
        if (!empty($code)) {
            $this->out[] = '</div>';
            $this->out[] = '<div style="text-align: right; float:right;">';
            $this->out[] = join("\n", $code);
            $this->out[] = '</div><div class="clear"></div>';
        }
        $this->out[] = '</div>';
        $this->out[] = '{/if}';
        $this->out[] = "{/block}";

    }


    private function createListTable()
    {

        $thCode = [];
        $tdCode = [];
        $useOrder = false;
        $index = 0;
        $keyname = '_' . $index;
        //选择项
        if ($this->lrow['useSelect']) {
            $thCode[] = '<th width="40"><input type="checkbox" class="check-all"></th>';
            $tdCode[] = '<td width="40" align="center" v-html="rs.' . $keyname . '"></td>';
            $this->hack[] = "{hack fn='{$keyname}' rs=null}" . '<input type="checkbox" class="check-item" name="sel_id" value="{$rs.id}">{/hack}';
            $index++;
            $keyname = '_' . $index;
        }
        //表单列表
        if (Utils::isJsonString($this->lrow['fields'])) {
            $fields = json_decode($this->lrow['fields'], true);
            $len = count($fields);
            foreach ($fields as $idx => $field) {
                $thAttr = [];
                $tdAttr = [];
                if (!empty($field['orderName'])) {
                    $useOrder = true;
                    $thAttr[] = 'data-order="' . $field['orderName'] . '"';
                }
                if (!empty($field['thAlign'])) {
                    $thAttr[] = 'align="' . $field['thAlign'] . '"';
                }
                if (!empty($field['thWidth'])) {
                    $thAttr[] = 'width="' . $field['thWidth'] . '"';
                    if ($this->lrow['useTwoLine'] && $idx + 1 != $len) {
                        $tdAttr[] = 'width="' . $field['thWidth'] . '"';
                    }
                }
                if (!empty($field['thAttrs'])) {
                    $thAttr[] = $field['thAttrs'];
                }
                if (!empty($field['tdAlign'])) {
                    $tdAttr[] = 'align="' . $field['tdAlign'] . '"';
                }
                if (!empty($field['tdAttrs'])) {
                    $tdAttr[] = $field['tdAttrs'];
                }
                if (isset($field['keyname'])) {
                    $field['keyname'] = trim($field['keyname']);
                    if (!empty($field['keyname'])) {
                        $keyname = $field['keyname'];
                    }
                }
                if ($this->lrow['useTwoLine'] && $idx + 1 == $len) {
                    $tdAttr[] = 'colspan="1000"';
                }
                $tdAttr[] = 'v-html="rs.' . $keyname . '"';
                $thAttr = join(' ', $thAttr);
                $tdAttr = join(' ', $tdAttr);
                $thCode[] = '<th ' . $thAttr . '>' . (isset($field['title']) ? $field['title'] : '') . '</th>';
                $tdCode[] = '<td ' . $tdAttr . '></td>';
                $this->hack[] = "{hack fn='{$keyname}' rs=null}" . (isset($field['code']) ? $field['code'] : '') . '{/hack}';
                $index++;
                $keyname = '_' . $index;
            }
        }

        $sedCode = '';
        if ($this->lrow['useTwoLine']) {
            $sedCode = array_pop($tdCode);
            array_pop($thCode);
        }

        //操作项
        if (!empty($this->lrow['thTitle'])) {
            $field = $this->lrow;
            $thAttr = [];
            $tdAttr = [];
            if (!empty($field['thAlign'])) {
                $thAttr[] = 'align="' . $field['thAlign'] . '"';
            }
            if (!empty($field['thWidth'])) {
                $thAttr[] = 'width="' . $field['thWidth'] . '"';
                if ($this->lrow['useTwoLine']) {
                    $tdAttr[] = 'width="' . $field['thWidth'] . '"';
                }
            }
            if (!empty($field['thAttrs'])) {
                $thAttr[] = $field['thAttrs'];
            }
            if (!empty($field['tdAlign'])) {
                $tdAttr[] = 'align="' . $field['tdAlign'] . '"';
            }
            if (!empty($field['tdAttrs'])) {
                $tdAttr[] = $field['tdAttrs'];
            }
            $tdAttr[] = 'v-html="rs.' . $keyname . '"';
            $thAttr = join(' ', $thAttr);
            $tdAttr = join(' ', $tdAttr);
            $thCode[] = '<th ' . $thAttr . '>' . (isset($field['thTitle']) ? $field['thTitle'] : '') . '</th>';
            $tdCode[] = '<td ' . $tdAttr . '></td>';
            $listBtns = (isset($field['listBtns']) ? $field['listBtns'] : []);
            if (Utils::isJsonString($listBtns)) {
                $listBtns = json_decode($listBtns, true);
            }
            $code = [];
            foreach ($listBtns as $btn) {
                $code[] = $btn['code'];
            }
            $this->hack[] = "{hack fn='{$keyname}' rs=null}" . (join("\n", $code)) . '{/hack}';
        }
        //生成表格
        $this->out[] = '';
        $this->out[] = "{block name='listTable'}";
        $table = [];
        $table[] = '<table id="list" cellspacing="0" cellpadding="0" border="0" class="yee-datatable" yee-module="datatable" data-bind-form="#searchform"';
        $table[] = ' data-auto-load="true"';
        if (!empty($this->lrow['listResize']) && $this->lrow['listResize'] == 1) {
            $table[] = ' data-resize="true"';
            if (!empty($this->lrow['leftFixed'])) {
                $table[] = ' data-left-fixed="' . $this->lrow['leftFixed'] . '"';
            }
            if (!empty($this->lrow['rightFixed'])) {
                $table[] = ' data-right-fixed="' . $this->lrow['rightFixed'] . '"';
            }
        }
        if ($useOrder) {
            $table[] = '  data-send-order="sort"';
        }
        $table[] = '>';
        $this->out[] = join('', $table);
        $this->out[] = '<thead><tr>';
        $this->out[] = join("\n", $thCode);
        $this->out[] = '</tr></thead>';

        $this->out[] = '<tbody>';
        if ($this->lrow['useTwoLine']) {
            $this->out[] = '<template v-for="rs in list">';
            $this->out[] = '<tr><td colspan="1000" style="padding: 0;"><table class="yee-intable" cellspacing="0" cellpadding="0" border="0">';
            $this->out[] = '<tr  class="first-line">';
            $this->out[] = join("\n", $tdCode);
            $this->out[] = '</tr>';
            $this->out[] = '<tr class="second-line">';
            $this->out[] = $sedCode;
            $this->out[] = '</tr>';
            $this->out[] = '</table></td></tr>';
            $this->out[] = '</template>';
        } else {
            $this->out[] = '<tr v-for="rs in list">';
            $this->out[] = join("\n", $tdCode);
            $this->out[] = '</tr>';
        }
        $this->out[] = '<tr v-if="list.length==0"><td colspan="1000"> 没有任何数据信息....</td></tr>';
        $this->out[] = '</tbody>';
        $this->out[] = '</table>';
        $this->out[] = "{/block}";

    }

    public function createPagebar()
    {
        if ($this->lrow['usePageList']) {
            $this->out[] = '';
            $this->out[] = "{block name='pagebar'}";
            $this->out[] = '<div yee-module="pagebar" data-bind="#list" class="yeeui-pagebar">
    <div class="pagebar" v-name="bar"></div>
    <div class="pagebar_info">
        共有信息：<span v-name="count"></span> 页次：<span v-name="page"></span>/<span v-name="pageCount"></span> 每页
        <span v-name="pageSize"></span>
    </div>
</div>';
            $this->out[] = '{/block}';
        }
    }


    private function createFoot()
    {
        if (!empty($this->lrow['buttomTemplate'])) {
            $this->out[] = '';
            $this->out[] = "{block name='foot'}";
            $this->out[] = $this->lrow['buttomTemplate'];
            $this->out[] = "{/block}";
        }
    }

    private function createHead()
    {
        if (!empty($this->lrow['headTemplate'])) {
            $this->out[] = '';
            $this->out[] = "{block name='head'}";
            $this->out[] = $this->lrow['headTemplate'];
            $this->out[] = "{/block}";
        }
    }


    public function getCode()
    {
        return join("\n", $this->out);
    }

    public function getHackCode()
    {
        $out = [];
        $out[] = join("\n", $this->hack);
        return join("\n", $out);
    }

    public function getKeyName()
    {
        return 'Zero' . $this->lrow['key'];
    }

    public function getPath()
    {
        $path = trim($this->lrow['namespace'], '\\') . '\\zero\\view';
        return Utils::path(ROOT_DIR, $path);
    }

    public static function make(int $listId = 0)
    {
        $maker = new MakeTemplate($listId);
        $path = $maker->getPath();
        Utils::makeDir($path);
        $code = $maker->getCode();
        $hack = $maker->getHackCode();
        file_put_contents(Utils::path($path, $maker->getKeyName() . '.tpl'), $code);
        file_put_contents(Utils::path($path, $maker->getKeyName() . '.hack.tpl'), $hack);
    }

}