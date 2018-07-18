<?php

namespace app\tool\libs;

use beacon\Console;
use beacon\DB;
use beacon\Route;
use beacon\Utils;

class MakeForm implements MakeInterface
{
    private $frow = null;
    private $fieldList = null;
    private $namespace = null;
    private $use = [];
    private $out = [];
    private $className = '';
    private $shortClassName = '';
    private $defTab = null;

    private static $remove = ['id', 'name', 'isEditTips', 'editTips', 'formId', 'isEditDataVal', 'editDataVal', 'editDataValMsg', 'dbtype', 'dblen', 'dbpoint', 'dbcomment', 'sort', 'valueFuncArgs', 'valueFuncSql', 'valueFuncField'];
    private static $bool = ['dbfield', 'close', 'viewClose', 'offEdit', 'hideBox', 'viewAsterisk', 'dataValOff'];
    private static $integer = ['viewMerge'];
    private static $array = ['dataVal', 'dataValMsg', 'dataValGroup', 'names', 'dynamic'];


    public function __construct(int $formId = 0, string $namespace = null)
    {
        $this->frow = DB::getRow('select * from @pf_tool_form where id=?', $formId);
        if ($this->frow == null) {
            throw new \Exception('生成错误');
        }
        $this->fieldList = DB::getList('select * from @pf_tool_field where formId=? order by sort asc', $formId);
        $this->namespace = $namespace;
        $this->createClass();
    }

    public function addUse(string $name)
    {
        $this->use[$name] = $name;
    }


    private function exportField($field, $isEdit = false)
    {
        if (isset($field['extendAttrs'])) {
            $extendAttrs = json_decode($field['extendAttrs'], true);
            if ($field) {
                $type = $field['type'];
                $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
                if (class_exists($typeClass) && is_callable($typeClass . '::exportField')) {
                    call_user_func_array($typeClass . '::exportField', [$this, &$field, $extendAttrs]);
                }
            }
            unset($field['extendAttrs']);
        }
        //添加BOX属性
        if (isset($field['boxAttrs'])) {
            $boxAttrs = json_decode($field['boxAttrs'], true);
            foreach ($boxAttrs as $item) {
                $field['box' . Utils::toCamel($item['name'])] = $item['value'];
            }
            unset($field['boxAttrs']);
        }
        //自定义属性
        if (isset($field['customAttrs'])) {
            $customAttrs = json_decode($field['customAttrs'], true);
            foreach ($customAttrs as $item) {
                $nkey = lcfirst(Utils::toCamel($item['name']));
                switch ($item['type']) {
                    case 'int';
                        $field[$nkey] = intval($item['value']);
                        break;
                    case 'float';
                        $field[$nkey] = floatval($item['value']);
                        break;
                    case 'bool';
                        $field[$nkey] = boolval($item['value']);
                        break;
                    case 'array';
                        $value = $item['value'];
                        if (empty($value)) {
                            $value = null;
                        } elseif (Utils::isJsonString($value)) {
                            $value = json_decode($value, true);
                        }
                        $field[$nkey] = $value;
                        break;
                    default:
                        $field[$nkey] = $item['value'];
                        break;
                }
            }
            unset($field['customAttrs']);
        }
        $out = [];
        $out[] = '                ' . var_export($field['name'], true) . ' => [';
        if (!$isEdit && $this->defTab && empty($field['viewTabIndex'])) {
            $field['viewTabIndex'] = $this->defTab;
        }
        if (!$isEdit && ((!$field['dbfield']) || $field['dbtype'] == 'null')) {
            $field['notSave'] = true;
        }
        if (isset($field['dbtype'])) {
            switch ($field['dbtype']) {
                case 'int':
                    $field['varType'] = 'integer';
                    break;
                case 'tinyint':
                    $field['varType'] = 'bool';
                    break;
                case 'float':
                case 'decimal':
                case 'double':
                    $field['varType'] = 'float';
                    break;
                default:
                    $field['varType'] = 'string';
                    break;
            }
        }

        foreach ($field as $key => $value) {
            if (in_array($key, self::$remove)) {
                continue;
            }
            if (in_array($key, self::$bool)) {
                $value = boolval($value);
            }
            if (in_array($key, self::$integer)) {
                $value = intval($value);
            }
            if (in_array($key, self::$array)) {
                if (empty($value)) {
                    $value = null;
                } elseif (Utils::isJsonString($value)) {
                    $value = json_decode($value, true);
                }
            }

            if ($key == 'default' && !is_array($value) && !($value === null || $value === '')) {
                if ($value[0] == '@') {
                    $data = substr($value, 1);
                    if (Utils::isJsonString($data)) {
                        $temp = json_decode($data, 1);
                        //直接从传值获取
                        if ((isset($temp['req']) || isset($temp['get']) || isset($temp['post'])) && !isset($temp['sql']) && !isset($temp['field'])) {
                            $value = new CodeItem();
                            $value->addUse('beacon\Request');
                            $code = 'Request::instance()';
                            if (!empty($temp['post'])) {
                                $code .= '->post(' . var_export($temp['post'], true) . ')';
                            } elseif (!empty($temp['get'])) {
                                $code .= '->get(' . var_export($temp['get'], true) . ')';
                            } elseif (!empty($temp['req'])) {
                                $code .= '->param(' . var_export($temp['req'], true) . ')';
                            } else {
                                $code = 'null';
                            }
                            $value->setCode($code);
                        } elseif ((isset($temp['req']) || isset($temp['get']) || isset($temp['post'])) && isset($temp['sql']) && isset($temp['field'])) {
                            $code = [];
                            $code[] = 'function(){';
                            $optionsMethod = '';
                            $optionsParam = '';
                            if (isset($temp['get'])) {
                                $optionsMethod = 'get';
                                $optionsParam = $temp['get'];
                            } elseif (isset($temp['post'])) {
                                $optionsMethod = 'post';
                                $optionsParam = $temp['post'];
                            } elseif (isset($temp['req'])) {
                                $optionsMethod = 'param';
                                $optionsParam = $temp['req'];
                            }
                            if (!empty($optionsMethod) && !empty($optionsParam)) {
                                $param = explode(',', $optionsParam);
                                $code[] = '    $param=[];';
                                $code[] = '    $req=Request::instance();';
                                foreach ($param as $item) {
                                    $code[] = '    $param[]= $req->' . $optionsMethod . '(' . var_export(trim($item), true) . ');';
                                }
                                $code[] = '    $row = DB::getRow(' . var_export(trim($temp['sql']), true) . ',$param);';
                            } else {
                                $code[] = '    $row = DB::getRow(' . var_export(trim($temp['sql']), true) . ');';
                            }
                            $okey = trim($temp['field']);
                            if (!empty($okey)) {
                                $code[] = '    $value = ($row == null || !isset($row[' . var_export($okey, true) . '])) ? null : $row[' . var_export($okey, true) . '];';
                            } else {
                                $code[] = '    $row = $row == null ? [] : array_values($row);';
                                $code[] = '    $value = isset($row[0]) ? null : $row[0];';
                            }
                            $code[] = '    return $value;';
                            $code[] = '}';

                            $value = new CodeItem();
                            $value->addUse('beacon\Request');
                            $value->addUse('beacon\DB');
                            $value->setCode(join("\n", $code));
                        } elseif (isset($temp['func'])) {
                            if (is_callable($temp['func'])) {
                                $value = new CodeItem();
                                $code = 'function(){ if(is_callable(' . var_export($temp['func'], true) . ')){return ' . $temp['func'] . '();} return null;}';
                                $value->setCode($code);
                            } else {
                                $value = null;
                            }
                        } elseif (isset($temp['inner'])) {

                            if ($temp['inner'] == 'date') {
                                $value = new CodeItem();
                                $code = 'function(){ return date(\'Y-m-d\');}';
                                $value->setCode($code);
                            } elseif ($temp['inner'] == 'datetime') {
                                $value = new CodeItem();
                                $code = 'function(){ return date(\'Y-m-d H:i:s\');}';
                                $value->setCode($code);
                            } elseif ($temp['inner'] == 'maxsort') {
                                $value = new CodeItem();
                                $code = 'function(){ return $this->maxSort();}';
                                $value->setCode($code);
                            } elseif ($temp['inner'] == 'minsort') {
                                $value = new CodeItem();
                                $code = 'function(){ return $this->minSort();}';
                                $value->setCode($code);
                            } else {
                                $value = null;
                            }
                        }

                    }
                }
            }

            if ($key == 'default') {
                if (($value === null || $value === '')) {
                    continue;
                }
            } elseif (empty($value)) {
                continue;
            }

            $key = Utils::camelToAttr($key);
            if ($value instanceof CodeItem) {
                $code = $value->getCode();
                $code = join("\n                    ", explode("\n", $code));
                $out[] = '    ' . var_export($key, true) . ' => ' . $code . ',';
                foreach ($value->use as $xit) {
                    $this->addUse($xit);
                }
            } else {
                if (is_array($value)) {
                    $out[] = '    ' . var_export($key, true) . ' => ' . Helper::export($value, '                        ') . ',';
                } else {
                    $out[] = '    ' . var_export($key, true) . ' => ' . var_export($value, true) . ',';
                }
            }
        }
        $out[] = '],';
        return $out;
    }

    private function createClass()
    {
        if (empty($this->namespace)) {
            $this->namespace = trim($this->frow['namespace'], '\\');
        } else {
            $temp = str_replace('/', '\\', $this->namespace);
            $this->namespace = trim($temp, '\\');
        }
        if ($this->frow['extMode'] == 1) {
            $className = 'Zero' . $this->frow['key'] . 'Plugin';
        } else {
            $className = 'Zero' . $this->frow['key'] . 'Form';
        }
        $this->shortClassName = $className;
        $this->namespace = $this->namespace . '\\zero\\form';
        $this->className = $this->namespace . '\\' . $className;
        $this->addUse('beacon\Form');

        $this->out[] = "class {$className} extends Form";
        $this->out[] = '{';
        $this->out[] = '    public $title=' . var_export($this->frow['title'], true) . ';';
        $this->out[] = '    public $caption=' . var_export($this->frow['caption'], true) . ';';
        $this->out[] = '    public $tbname=' . var_export('@pf_' . $this->frow['tbName'], true) . ';';
        $this->out[] = '    public $useAjax=' . var_export(boolval($this->frow['useAjax']), true) . ';';


        if (intval($this->frow['validateMode']) > 0) {
            $this->out[] = '    public $validateMode=' . var_export(intval($this->frow['validateMode']), true) . ';';
        }
        if (!empty($this->frow['description'])) {
            $this->out[] = '    public $viewDescription=' . var_export($this->frow['description'], true) . ';';
        }
        if (!empty($this->frow['information'])) {
            $this->out[] = '    public $viewInformation=' . var_export($this->frow['information'], true) . ';';
        }
        if (!empty($this->frow['attention'])) {
            $this->out[] = '    public $viewAttention=' . var_export($this->frow['attention'], true) . ';';
        }
        if (!empty($this->frow['script'])) {
            $this->out[] = '    public $viewScript=' . var_export($this->frow['script'], true) . ';';
        }
        if (!empty($this->frow['viewNotBack'])) {
            $this->out[] = '    public $viewNotBack=' . var_export(boolval($this->frow['viewNotBack']), true) . ';';
        }
        if (!empty($this->frow['viewTemplate'])) {
            $this->out[] = '    public $viewTemplate=' . var_export(boolval($this->frow['viewTemplate']), true) . ';';
        }
        if ($this->frow['viewUseTab'] == 1) {
            $temp = json_decode($this->frow['viewTabs'], true);
            $tabs = [];
            foreach ($temp as $item) {
                $tabs[$item['key']] = $item['value'];
            }
            if (isset($temp[0])) {
                $this->defTab = $temp[0]['key'];
                $this->out[] = '    public $viewUseTab=' . var_export(boolval($this->frow['viewUseTab']), true) . ';';
                $this->out[] = '    public $viewTabs=' . Helper::export($tabs, '        ') . ';';
            }
        }

        //构建函数代码

        if ($this->frow['isEditDescription'] == 1 || $this->frow['extMode'] != 1) {
            $this->out[] = '';
            $this->out[] = '    public function __construct(string $type = \'\'){';
            $this->out[] = '        parent::__construct($type);';
            $this->out[] = '        if($this->isEdit()){';
            if ($this->frow['isEditDescription'] == 1) {
                $this->out[] = '            $this->viewDescription=' . var_export($this->frow['editDescription'], true) . ';';
            }
            if ($this->frow['extMode'] != 1) {
                $this->addUse('beacon\Request');
                $this->out[] = '            $this->addHideBox(\'id\', Request::instance()->get(\'id:i\', 0));';
            }
            $this->out[] = '        }';
            $this->out[] = '    }';
        }
        //加载函数代码
        $fieldList = $this->fieldList;

        $this->out[] = '';
        $this->out[] = '    protected function load(){';
        $this->out[] = '        return [';
        foreach ($fieldList as $item) {
            $field = self::exportField($item);
            $this->out[] = join("\n                ", $field);
        }
        $this->out[] = '        ];';
        $this->out[] = '    }';

        $efieldList = [];
        foreach ($fieldList as $item) {
            $eitem = [];
            if (!empty(intval($item['isEditTips']))) {
                $eitem['tips'] = $item['editTips'];
            }
            if (!empty(intval($item['isEditDataVal']))) {
                $eitem['dataValMsg'] = $item['editDataValMsg'];
                $eitem['dataVal'] = $item['editDataVal'];
            }
            if (isset($item['isEditDataValGroup']) && !empty(intval($item['isEditDataValGroup']))) {
                $eitem['dataValGroup'] = $item['editDataValGroup'];
            }
            if (!empty($eitem)) {
                $eitem['name'] = $item['name'];
                $efieldList[] = $eitem;
            }
        }
        if (!empty($efieldList)) {
            $this->out[] = '';
            $this->out[] = '    protected function loadEdit(){';
            $this->out[] = '        return [';
            foreach ($efieldList as $item) {
                $field = self::exportField($item, true);
                $this->out[] = join("\n                ", $field);
            }
            $this->out[] = '        ];';
            $this->out[] = '    }';
        }
        //class 闭合
        $this->out[] = '}';
    }

    public function getCode()
    {
        $code = [];
        $code[] = '<?php';
        $code[] = '';
        $code[] = 'namespace ' . $this->namespace . ';';
        $code[] = '';
        $code[] = '/**';
        $code[] = '* ' . $this->frow['title'];
        $code[] = '* Created by Beacon AI Tool.';
        $code[] = '* User: wj008';
        $code[] = '* Date: ' . date('Y/m/d');
        $code[] = '* Time: ' . date('H:i:s');
        $code[] = '* 注意：该代码由工具生成，不要在此处修改任何代码，将会被覆盖，如要修改请在应用 form目录中创建同名类并继承该生成类进行调整';
        $code[] = '*/';
        $code[] = '';
        foreach ($this->use as $item) {
            $code[] = 'use ' . $item . ';';
        }
        $code[] = '';
        $code[] = join("\n", $this->out);
        return join("\n", $code);
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getShortClassName()
    {
        return $this->shortClassName;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public static function make(int $formId = 0)
    {
        $maker = new MakeForm($formId);
        $path = Utils::path(ROOT_DIR, $maker->getNamespace());
        Utils::makeDir($path);
        $code = $maker->getCode();
        file_put_contents(Utils::path($path, $maker->getShortClassName() . '.php'), $code);
    }

}