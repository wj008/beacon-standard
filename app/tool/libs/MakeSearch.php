<?php

namespace app\tool\libs;

use beacon\Console;
use beacon\DB;
use beacon\Route;
use beacon\Utils;

class MakeSearch implements MakeInterface
{
    private $lrow = null;
    private $fieldList = null;
    private $namespace = null;
    private $use = [];
    private $out = [];
    private $className = '';
    private $shortClassName = '';
    private $defTab = null;

    private static $remove = ['id', 'name', 'listId', 'isEditDataVal', 'editDataVal', 'editDataValMsg', 'dbtype', 'dblen', 'dbpoint', 'dbcomment', 'sort', 'tbWhere', 'tbWhereType'];
    private static $bool = ['dbfield', 'close', 'viewClose', 'offEdit', 'hideBox', 'viewAsterisk', 'dataValOff'];
    private static $integer = ['viewMerge'];
    private static $array = ['dataVal', 'dataValMsg', 'dataValGroup', 'names'];

    public function __construct(int $listId = 0, string $namespace = null)
    {
        $this->lrow = DB::getRow('select * from @pf_tool_list where id=?', $listId);
        if ($this->lrow == null) {
            throw new \Exception('生成错误');
        }
        $this->fieldList = DB::getList('select * from @pf_tool_search where listId=? order by sort asc', $listId);
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
            if ($key == 'default' && !empty($value)) {
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

            if (empty($value)) {
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
            $this->namespace = trim($this->lrow['namespace'], '\\');
        } else {
            $temp = str_replace('/', '\\', $this->namespace);
            $this->namespace = trim($temp, '\\');
        }
        $className = 'Zero' . $this->lrow['key'] . 'Search';
        $this->shortClassName = $className;
        $this->namespace = $this->namespace . '\\zero\\form';
        $this->className = $this->namespace . '\\' . $className;
        $this->addUse('beacon\Form');
        $this->out[] = "class {$className} extends Form";
        $this->out[] = '{';
        $this->out[] = '    public $title=' . var_export($this->lrow['title'], true) . ';';
        $this->out[] = '    public $caption=' . var_export($this->lrow['caption'], true) . ';';
        $this->out[] = '    public $viewUseTab=true;';
        $this->out[] = '    public $viewTabs=[\'base\'=>\'基础搜索\',\'senior\'=>\'高级搜索\'];';

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
        $code[] = '* ' . $this->lrow['title'];
        $code[] = '* Created by Beacon AI Tool.';
        $code[] = '* User: wj008';
        $code[] = '* Date: ' . date('Y/m/d');
        $code[] = '* Time: ' . date('H:i:s');
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

    public static function make(int $listId = 0)
    {
        $maker = new MakeSearch($listId);
        $path = Utils::path(ROOT_DIR, $maker->getNamespace());
        Utils::makeDir($path);
        $code = $maker->getCode();
        file_put_contents(Utils::path($path, $maker->getShortClassName() . '.php'), $code);
    }

}