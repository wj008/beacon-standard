<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/23
 * Time: 18:47
 */

namespace app\tool\libs;


use beacon\DB;
use beacon\Utils;

class MakeController implements MakeInterface
{
    private $lrow = null;
    private $frow = null;
    private $namespace = null;
    private $appspace = null;
    private $use = [];
    private $out = [];
    private $classFullName = '';
    private $className = '';

    public function __construct(int $listId = 0, string $namespace = null)
    {
        $this->lrow = DB::getRow('select * from @pf_tool_list where id=?', $listId);
        if ($this->lrow == null) {
            throw new \Exception('生成错误');
        }
        $this->namespace = $namespace;
        $this->createClass();
    }

    public function addUse(string $name)
    {
        $this->use[$name] = $name;
    }

    //创建控制器的初始化方法
    private function createInitializeMethod()
    {
        $assignGlobalItem = [];
        if (Utils::isJsonString($this->lrow['assign'])) {
            $assignList = json_decode($this->lrow['assign'], true);
            foreach ($assignList as $assign) {
                if (isset($assign['global']) && $assign['global']) {
                    $assignGlobalItem[] = $assign;
                }
            }
        }
        if (count($assignGlobalItem) > 0) {
            $this->out[] = '';
            foreach ($assignGlobalItem as $assign) {
                if (!empty($assign['key']) && !empty($assign['param'])) {
                    $this->out[] = '    protected $' . $assign['key'] . ' = null;';
                }
                if (isset($assign['useSets']) && $assign['useSets'] && !empty($assign['setsSql'])) {
                    $this->out[] = '    protected $' . $assign['key'] . 'Row = null;';
                }
            }
            $this->out[] = '';
            $this->out[] = '    //初始化方法';
            $this->out[] = '    public function initialize(){';
            $this->out[] = '        parent::initialize();';
            foreach ($assignGlobalItem as $assign) {
                if (!empty($assign['key']) && !empty($assign['param'])) {
                    $this->out[] = '        $this->' . $assign['key'] . ' = $this->param(' . var_export(trim($assign['param']), true) . ');';
                }
                if (!empty($assign['tips'])) {
                    $this->out[] = '        if( empty( $this->' . $assign['key'] . ' ) ){';
                    $this->out[] = '            $this->error(' . var_export(trim($assign['tips']), true) . ');';
                    $this->out[] = '        }';
                }
                if (isset($assign['useSets']) && $assign['useSets'] && !empty($assign['setsSql'])) {
                    $this->addUse('beacon\DB');
                    $this->out[] = '        $this->' . $assign['key'] . 'Row = DB::getRow(' . var_export(trim($assign['setsSql']), true) . ',$this->' . $assign['key'] . ');';
                    if (!empty($assign['setsTips'])) {
                        $this->out[] = '        if( $this->' . $assign['key'] . 'Row == null ){';
                        $this->out[] = '            $this->error(' . var_export(trim($assign['setsTips']), true) . ');';
                        $this->out[] = '        }';
                    }
                    $this->out[] = '        $this->assign(' . var_export(trim($assign['key']), true) . ',$this->' . $assign['key'] . 'Row);';
                } else {
                    $this->out[] = '        $this->assign(' . var_export(trim($assign['key']), true) . ',$this->' . $assign['key'] . ');';
                }
            }
            $this->out[] = '    }';
        }
    }

    //创建加载配置的方法
    private function createZeroLoadMethod()
    {
        $className = $this->lrow['key'];
        $zeroConfig = [];
        if ($this->frow == null) {
            $this->frow = DB::getRow('select * from @pf_tool_form where id=?', $this->lrow['formId']);
            if (!$this->frow) {
                throw new \Exception('没选择对应的表单模型');
            }
        }
        $frow = $this->frow;
        //actionForm
        //如果是插件
        if ($frow['extMode'] == 1) {
            $formClassName = 'Zero' . $frow['key'] . 'Plugin';
        } else {
            $formClassName = 'Zero' . $frow['key'] . 'Form';
        }
        $zeroConfig['actionForm'] = $this->appspace . '\\zero\\form\\' . $formClassName;
        $srow = DB::getRow('select count(1) as c from @pf_tool_search where listId=?', $this->lrow['id']);
        if ($srow && $srow['c'] > 0) {
            $zeroConfig['searchForm'] = $this->appspace . '\\zero\\form\\Zero' . $className . 'Search';
            MakeSearch::make($this->lrow['id']);
        }
        $zeroConfig['tbname'] = '@pf_' . trim($frow['tbName']);
        if ($this->lrow['usePageList']) {
            $zeroConfig['usePageList'] = true;
            $zeroConfig['pageSize'] = intval($this->lrow['pageSize']);
        }
        if ($this->lrow['useCustomTemplate']) {
            $zeroConfig['template'] = $this->lrow['template'];
            $zeroConfig['templateHack'] = $this->lrow['templateHack'];
            if (empty($zeroConfig['template'])) {
                $zeroConfig['template'] = 'Zero' . $className . '.tpl';
            }
            if (empty($zeroConfig['templateHack'])) {
                $zeroConfig['templateHack'] = 'Zero' . $className . '.hack.tpl';
            }
        } else {
            $zeroConfig['template'] = 'Zero' . $className . '.tpl';
            $zeroConfig['templateHack'] = 'Zero' . $className . '.hack.tpl';
        }
        //列表中附加的原始字段
        $strOrgFields = trim($this->lrow['orgFields']);
        if (!empty($strOrgFields)) {
            $orgFields = explode(',', $strOrgFields);
            try {
                $dbFieldList = DB::getFields($zeroConfig['tbname']);
                $dbFields = [];
                foreach ($dbFieldList as $item) {
                    if (!empty($item['Field'])) {
                        $dbFields[$item['Field']] = 1;
                    }
                }
            } catch (\Exception $exception) {
                $dbFields = [];
            }
            $temp = [];
            foreach ($orgFields as $item) {
                $item = trim($item);
                if (isset($dbFields[$item])) {
                    $temp[] = $item;
                }
            }
            if (count($temp) > 0) {
                $zeroConfig['listOrgFields'] = $temp;
            }
        }
        //加载函数代码
        $this->out[] = '';
        $this->out[] = '    //为ZeroController所需的配置信息';
        $this->out[] = '    protected function zeroLoad(){ ';
        $this->out[] = '        return ' . Helper::export($zeroConfig, '        ') . ';';
        $this->out[] = '    }';
        return $zeroConfig;
    }

    //创建查询条件的方法
    private function createZeroWhereMethod($zeroConfig)
    {
        $zeroSelectorData = [];
        $tbWhereTemps = [];
        if (Utils::isJsonString($this->lrow['tbWhere'])) {
            $tbWhereTemps = json_decode($this->lrow['tbWhere'], true);
        }
        $searchFields = DB::getList('select `name`,tbWhere,tbWhereType from @pf_tool_search where listId=?', $this->lrow['id']);
        foreach ($searchFields as $field) {
            $field['tbWhere'] = trim($field['tbWhere']);
            if (!empty($field['tbWhere'])) {
                if (empty($field['name'])) {
                    $tbWhereTemps[] = ['sql' => $field['tbWhere'], 'param' => null, 'type' => intval($field['tbWhereType'])];
                } else {
                    $tbWhereTemps[] = ['sql' => $field['tbWhere'], 'param' => $field['name'], 'type' => intval($field['tbWhereType'])];
                }
            }
        }
        if (count($tbWhereTemps) > 0) {
            $temps = [];
            foreach ($tbWhereTemps as $item) {
                if ($item['param'] === '' || $item['param'] === null) {
                    $item['type'] = -1;
                    $item['param'] = null;
                } else {
                    $params = explode(',', $item['param']);
                    foreach ($params as $idx => $param) {
                        $params[$idx] = trim($param);
                    }
                    $item['param'] = $params;
                }
                $temps[] = $item;
            }
            $tbWhereTemps = $temps;
        }
        //生成 where查询行数
        $this->out[] = '';
        $this->addUse('beacon\SqlSelectInterface');
        $this->out[] = '    //为ZeroController所需的条件查询';
        $this->out[] = '    protected function zeroWhere(SqlSelectInterface $selector){ ';
        $useSearchForm=false;
        if (count($tbWhereTemps) > 0 && !empty($zeroConfig['searchForm'])) {
            $this->addUse('beacon\Form');
            $this->out[] = '          //从搜索表单获取数据';
            $this->out[] = '        $search = Form::instance(' . var_export(trim($zeroConfig['searchForm']), true) . ');';
            $this->out[] = '        $vals = $search->autoComplete(\'param\');';
            $useSearchForm=true;
        }
        foreach ($tbWhereTemps as $item) {
            if (empty($item['sql'])) {
                continue;
            }
            if ($item['param'] !== null && count($item['param']) > 1) {
                $this->out[] = '        $args = [];';
                foreach ($item['param'] as $keyname) {
                    $keyname = trim($keyname);
                    if (empty($keyname)) {
                        continue;
                    }
                    $argname = $keyname;
                    if (preg_match('@^(.*):([abfis])@', $keyname, $m)) {
                        $argname = $m[1];
                    }
                    $temp_value = '$temp_value';
                    if (preg_match('@^\w+$@', $argname)) {
                        $temp_value = '$' . $argname;
                    }
                    if($useSearchForm) {
                        $this->out[] = '        ' . $temp_value . ' = isset($vals[' . var_export($argname, true) . ']) ? $vals[' . var_export($argname, true) . '] : $this->param(' . var_export($keyname, true) . ');';
                    }else{
                        $this->out[] = '        ' . $temp_value . ' = $this->param(' . var_export($keyname, true) . ');';
                    }
                    $this->out[] = '        $args[] = is_array(' . $temp_value . ') ? \'\' : ' . $temp_value . ';';
                }

                if ($item['type'] == -1) {
                    $this->out[] = '        $selector->where(' . var_export($item['sql'], true) . ', $args);';
                } else {
                    if (isset($args[0])) {
                        $this->out[] = '        if(isset($args[0])){';
                        $this->out[] = '            $selector->search(' . var_export($item['sql'], true) . ', $args[0] , ' . var_export($item['type'], true) . ');';
                        $this->out[] = '        }';
                    }
                }

            } elseif ($item['param'] !== null && count($item['param']) == 1) {
                $keyname = trim($item['param'][0]);
                if (empty($keyname)) {
                    continue;
                }
                $argname = $keyname;
                if (preg_match('@^(.*):([abfis])@', $keyname, $m)) {
                    $argname = $m[1];
                }
                $temp_value = '$temp_value';
                if (preg_match('@^\w+$@', $argname)) {
                    $temp_value = '$' . $argname;
                }
                if($useSearchForm) {
                    $this->out[] = '        ' . $temp_value . '  = isset($vals[' . var_export($argname, true) . ']) ? $vals[' . var_export($argname, true) . '] : $this->param(' . var_export($keyname, true) . ');';
                }
                else{
                    $this->out[] = '        ' . $temp_value . '  =  $this->param(' . var_export($keyname, true) . ');';
                }
                $this->out[] = '        ' . $temp_value . '  = is_array(' . $temp_value . ' ) ? \'\' : ' . $temp_value . ' ;';
                if ($item['type'] == -1) {
                    $this->out[] = '        $selector->where(' . var_export($item['sql'], true) . ', ' . $temp_value . ' );';
                } else {
                    $this->out[] = '        $selector->search(' . var_export($item['sql'], true) . ', ' . $temp_value . ' , ' . var_export($item['type'], true) . ');';
                }
            } elseif ($item['param'] === null || count($item['param']) == 0) {
                $this->out[] = '        $selector->where(' . var_export($item['sql'], true) . ');';
            }
            $this->out[] = '';
        }
        $this->out[] = '        return $selector;';
        $this->out[] = '    }';
        return $zeroSelectorData;
    }

    //创建查询器的方法
    private function createZeroSelectorMethod()
    {
        $zeroSelectorData = [];
        if ($this->frow == null) {
            $this->frow = DB::getRow('select * from @pf_tool_form where id=?', $this->lrow['formId']);
            if (!$this->frow) {
                throw new \Exception('没选择对应的表单模型');
            }
        }
        $frow = $this->frow;
        $zeroSelectorData['tbname'] = '@pf_' . trim($frow['tbName']);
        if ($this->lrow['tbNameAlias']) {
            $zeroSelectorData['tbnameAlias'] = $this->lrow['tbNameAlias'];
        }
        if (empty($this->lrow['useSqlTemplate'])) {
            if (!empty($this->lrow['tbField'])) {
                $zeroSelectorData['tbField'] = $this->lrow['tbField'];
            }
            if (Utils::isJsonString($this->lrow['tbJoin'])) {
                $this->lrow['tbJoin'] = json_decode($this->lrow['tbJoin'], true);
            }
            if (!empty($this->lrow['tbJoin'])) {
                $temps = [];
                foreach ($this->lrow['tbJoin'] as $item) {
                    $temps[] = "{$item['join']} `{$item['tbname']}` {$item['alias']} on {$item['on']}";
                }
                $zeroSelectorData['tbJoin'] = $temps;
            }
        }
        if (empty($this->lrow['useSqlTemplate']) && !empty($this->lrow['tbOrder'])) {
            $zeroSelectorData['tbOrder'] = $this->lrow['tbOrder'];
        }
        if (!empty($this->lrow['useSqlTemplate'])) {
            $zeroSelectorData['sqlTemplate'] = $this->lrow['sqlTemplate'];
        }

        $this->out[] = '';
        $this->out[] = '    //为ZeroController所需的自动查询器';
        $this->out[] = '    protected function zeroSelector(){ ';
        if (!empty($zeroSelectorData['sqlTemplate'])) {
            $this->addUse('beacon\SqlTemplate');
            $this->out[] = '        $param = $this->param();';
            $this->out[] = '        $selector = new SqlTemplate(' . var_export(trim($zeroSelectorData['sqlTemplate']), true) . ', $param);';
        } else {
            $this->addUse('beacon\SqlSelector');
            $tbnameAlias = isset($zeroSelectorData['tbnameAlias']) ? trim($zeroSelectorData['tbnameAlias']) : null;
            if (empty($tbnameAlias)) {
                $this->out[] = '        $selector = new SqlSelector(' . var_export(trim($zeroSelectorData['tbname']), true) . ');';
            } else {
                $this->out[] = '        $selector = new SqlSelector(' . var_export(trim($zeroSelectorData['tbname']), true) . ' , ' . var_export($tbnameAlias, true) . ');';
            }

            $tbField = isset($zeroSelectorData['tbField']) ? trim($zeroSelectorData['tbField']) : null;
            if (!empty($tbField)) {
                $this->out[] = '        $selector->field(' . var_export($tbField, true) . ');';
            }

            $tbJoin = isset($zeroSelectorData['tbJoin']) ? $zeroSelectorData['tbJoin'] : [];
            //join 表
            if (count($tbJoin) > 0) {
                foreach ($zeroSelectorData['tbJoin'] as $item) {
                    $item = trim($item);
                    if (!empty($item)) {
                        $this->out[] = '        $selector->join(' . var_export($item, true) . ');';
                    }
                }
            }
            $tbOrder = isset($zeroSelectorData['tbOrder']) ? trim($zeroSelectorData['tbOrder']) : null;
            if (!empty($tbOrder)) {
                $this->out[] = '        $selector->order(' . var_export($tbOrder, true) . ');';
            }

            $sortItem = [];
            if (Utils::isJsonString($this->lrow['fields'])) {
                $fields = json_decode($this->lrow['fields'], true);
                foreach ($fields as $field) {
                    if (!empty($field['orderName'])) {
                        $sortItem[] = $field['orderName'];
                    }
                }
            }
            //排序
            if (!empty($sortItem)) {
                $this->out[] = '        //自动按列设置排序';
                $this->out[] = '        $temp_sort = $this->param(\'sort:s\');';
                $this->out[] = '        if (preg_match(\'@^(\w+)_(asc|desc)$@\', $temp_sort, $match)) {';
                $this->out[] = '            if (in_array($match[1], $this->zero[\'sortItem\'])) {';
                $this->out[] = '                $selector->order($match[1] . \' \' . $match[2]);';
                $this->out[] = '            }';
                $this->out[] = '        }';
            }
        }
        $this->out[] = '        $this->zeroWhere($selector);';
        $this->out[] = '        return $selector;';
        $this->out[] = '    }';

    }

    //创建获取数据条数函数的方法
    private function createZeroCountBySqlTemplateMethod()
    {
        $zeroSelectorData = [];
        if ($this->lrow['sqlCountTemplate']) {
            $zeroSelectorData['sqlCountTemplate'] = $this->lrow['sqlCountTemplate'];
        }
        //生成 where查询行数
        if (!empty($zeroSelectorData['sqlCountTemplate'])) {
            $this->out[] = '';
            $this->addUse('beacon\SqlSelectInterface');
            $this->addUse('beacon\SqlTemplate');
            $this->addUse('beacon\DB');
            $this->out[] = '    //为ZeroController 模板查询数量';
            $this->out[] = '    protected function zeroCountBySqlTemplate(){ ';
            $this->out[] = '        $param = $this->param();';
            $this->out[] = '        $selector = new SqlTemplate(' . var_export(trim($zeroSelectorData['sqlCountTemplate']), true) . ', $param);';
            $this->out[] = '        $this->zeroWhere($selector);';
            $this->out[] = '        return DB::getOne($selector->createSql());';
            $this->out[] = '    }';
        }
    }

    //创建index的方法
    private function createIndexActionModel()
    {
        $assignLinstItem = [];
        if (Utils::isJsonString($this->lrow['assign'])) {
            $assignList = json_decode($this->lrow['assign'], true);
            foreach ($assignList as $assign) {
                if (!isset($assign['global']) || empty($assign['global'])) {
                    $assignLinstItem[] = $assign;
                }
            }
        }
        $this->out[] = '';
        $this->out[] = '    //公开 indexAction 方法';
        $this->out[] = '    public function indexAction(){';
        if (count($assignLinstItem) > 0) {
            foreach ($assignLinstItem as $assign) {
                if (!empty($assign['key']) && !empty($assign['param'])) {
                    $this->out[] = '        $' . $assign['key'] . ' = $this->param(' . var_export(trim($assign['param']), true) . ');';
                }
                if (!empty($assign['tips'])) {
                    $this->out[] = '        if( empty( $' . $assign['key'] . ' ) ){';
                    $this->out[] = '            $this->error(' . var_export(trim($assign['tips']), true) . ');';
                    $this->out[] = '        }';
                }
                if (isset($assign['useSets']) && $assign['useSets'] && !empty($assign['setsSql'])) {
                    $this->addUse('beacon\DB');
                    $this->out[] = '        $' . $assign['key'] . 'Row = DB::getRow(' . var_export(trim($assign['setsSql']), true) . ',$' . $assign['key'] . ');';
                    if (!empty($assign['setsTips'])) {
                        $this->out[] = '        if( $' . $assign['key'] . 'Row == null ){';
                        $this->out[] = '            $this->error(' . var_export(trim($assign['setsTips']), true) . ');';
                        $this->out[] = '        }';
                    }
                    $this->out[] = '        $this->assign(' . var_export(trim($assign['key']), true) . ',$' . $assign['key'] . 'Row);';
                } else {
                    $this->out[] = '        $this->assign(' . var_export(trim($assign['key']), true) . ',$' . $assign['key'] . ');';
                }
            }
        }
        $this->out[] = '        return parent::indexAction();';
        $this->out[] = '    }';
    }

    //创建控制器类
    private function createClass()
    {
        if (empty($this->namespace)) {
            $this->appspace = trim($this->lrow['namespace'], '\\');
        } else {
            $temp = str_replace('/', '\\', $this->namespace);
            $this->appspace = trim($temp, '\\');
        }
        $className = $this->lrow['key'];
        $baseControllerFullName = $this->appspace . '\\controller\\ZeroController';
        if (!empty($this->lrow['baseController'])) {
            if (!preg_match('@\\\\@', $this->lrow['baseController'])) {
                $baseControllerFullName = $this->appspace . '\\controller' . $this->lrow['baseController'];
            } else {
                $baseControllerFullName = $this->lrow['baseController'];
            }
        }
        $this->addUse($baseControllerFullName);
        $temp = explode('\\', $baseControllerFullName);
        $baseController = end($temp);
        $this->className = 'Zero' . $this->lrow['key'];
        $this->namespace = $this->appspace . '\\zero\\controller';
        $this->classFullName = $this->namespace . '\\Zero' . $className;
        $this->out[] = "class Zero{$className} extends " . $baseController;
        $this->out[] = '{';
        $this->createInitializeMethod();
        $zeroConfig = $this->createZeroLoadMethod();
        $this->createZeroWhereMethod($zeroConfig);
        $this->createZeroSelectorMethod();
        $this->createZeroCountBySqlTemplateMethod();
        $this->createIndexActionModel();
        if (Utils::isJsonString($this->lrow['actions'])) {
            $actions = json_decode($this->lrow['actions'], true);
            foreach ($actions as $action) {
                $this->out[] = '';
                $this->out[] = '    //公开 ' . $action . ' 方法';
                $this->out[] = '    public function ' . $action . '(){';
                $this->out[] = '        return parent::' . $action . '();';
                $this->out[] = '    }';
            }
        }
        $this->out[] = '}';
    }

    //获取生成的代码
    public function getCode()
    {
        $code = [];
        $code[] = '<?php';
        $code[] = '';
        $code[] = 'namespace ' . $this->namespace . ';';
        $code[] = '';
        $code[] = '/**';
        $code[] = '* ' . $this->lrow['title'];
        $code[] = '* Created by Beacon AI Tool.';
        $code[] = '* User: wj008';
        $code[] = '* Date: ' . date('Y/m/d');
        $code[] = '* Time: ' . date('H:i:s');
        $code[] = '* 注意：该代码由代码工具生成，不要在此处修改任何代码，将会被覆盖，如要修改请在应用 controller目录中创建同名类并继承该生成类进行调整';
        $code[] = '*/';
        $code[] = '';
        foreach ($this->use as $item) {
            $code[] = 'use ' . $item . ';';
        }
        $code[] = '';
        $code[] = join("\n", $this->out);
        return join("\n", $code);
    }


    public function getClassFullName()
    {
        return $this->classFullName;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public static function make(int $listId = 0)
    {
        MakeSearch::make($listId);
        MakeTemplate::make($listId);
        $maker = new MakeController($listId);
        $path = Utils::path(ROOT_DIR, $maker->getNamespace());
        Utils::makeDir($path);
        $code = $maker->getCode();
        file_put_contents(Utils::path($path, $maker->getClassName() . '.php'), $code);

    }

}