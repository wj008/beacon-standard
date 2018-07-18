<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/23
 * Time: 3:22
 */

namespace libs\zero;


use beacon\Config;
use beacon\Console;
use beacon\Request;
use beacon\Route;
use beacon\RouteError;
use beacon\Utils;

class Boot
{

    public static function register(string $name, $route = null)
    {
        return Route::register($name, $route);
    }

    public static function run(string $url = null)
    {
        if (defined('DEV_DEBUG') && DEV_DEBUG) {
            error_reporting(E_ALL);
            //程序计时---
            if (isset($_SERVER['REQUEST_URI'])) {
                $t1 = microtime(true);
                register_shutdown_function(function () use ($t1) {
                    $t2 = microtime(true);
                    Console::info('URL:', $_SERVER['REQUEST_URI'], '耗时' . round($t2 - $t1, 3) . '秒');
                });
            }
        }

        $request = Request::instance();
        try {
            $route = Route::get();
            if ($route == null) {
                $url = Route::parse($url);
            }
            $route = Route::get();
            if ($route == null) {
                throw new RouteError('未初始化路由参数,url:' . $url);
            }
            if (empty($route['app'])) {
                throw new RouteError('不存在的路径,url:' . $url);
            }
            if (empty($route['ctl'])) {
                throw new RouteError('不存在的控制器,url:' . $url);
            }
            if (empty($route['act'])) {
                throw new RouteError('不存在的控制器方法,url:' . $url);
            }
            $ctl = Utils::toCamel($route['ctl']);
            $act = Utils::toCamel($route['act']);
            $act = lcfirst($act);
            $appPath = Route::getPath();
            if (empty($appPath)) {
                throw new RouteError('没有设置应用目录,url:' . $url);
            }
            $config = Utils::path($appPath, 'config.php');
            if (file_exists($config)) {
                $cfgData = Config::loadFile($config);
                foreach ($cfgData as $key => $val) {
                    Config::set($key, $val);
                }
            }
            $namespace = Route::getNamespace();
            $class = $namespace . '\\controller\\' . $ctl;
            if (!class_exists($class)) {
                $classZero = $namespace . '\\zero\\controller\\Zero' . $ctl;
                if (!class_exists($classZero)) {
                    throw new RouteError('不存在的控制器:' . $class);
                }
                $class = $classZero;
            }
            try {
                $oReflectionClass = new \ReflectionClass($class);
                $method = $oReflectionClass->getMethod($act . 'Action');
                if ($method->isPublic()) {
                    $params = $method->getParameters();
                    $args = [];
                    if (count($params) > 0) {
                        foreach ($params as $param) {
                            $name = $param->getName();
                            $type = 'any';
                            if (is_callable([$param, 'hasType'])) {
                                if ($param->hasType()) {
                                    $refType = $param->getType();
                                    if ($refType != null) {
                                        if (is_callable([$refType, 'getName'])) {
                                            $type = $refType->getName();
                                        } else {
                                            $type = strval($refType);
                                        }
                                        $type = empty($type) ? 'any' : $type;
                                    }
                                }
                            }
                            if ($type == 'any') {
                                if (is_callable([$param, 'getClass'])) {
                                    $refType = $param->getClass();
                                    if ($refType != null) {
                                        if (is_callable([$refType, 'getName'])) {
                                            $type = $refType->getName();
                                        } else {
                                            $type = strval($refType);
                                        }
                                        $type = empty($type) ? 'any' : $type;
                                    }
                                }
                            }
                            $def = null;
                            //如果有默认值
                            if ($param->isOptional()) {
                                $def = $param->getDefaultValue();
                                if ($type == 'any') {
                                    $type = gettype($def);
                                }
                            }

                            switch ($type) {
                                case 'bool':
                                case 'boolean':
                                    $args[] = $request->param($name . ':b', $def);
                                    break;
                                case 'int':
                                case 'integer':
                                    $val = $request->param($name . ':s', $def);
                                    if (preg_match('@[+-]?\d*\.\d+@', $val)) {
                                        $args[] = $request->param($name . ':f', $def);
                                    } else {
                                        $args[] = $request->param($name . ':i', $def);
                                    }
                                    break;
                                case 'double':
                                case 'float':
                                    $args[] = $request->param($name . ':f', $def);
                                    break;
                                case 'string':
                                    $args[] = $request->param($name . ':s', $def);
                                    break;
                                case 'array':
                                    $args[] = $request->param($name . ':a', $def);
                                    break;
                                case '\beacon\Request':
                                case 'beacon\Request':
                                    $args[] = $request;
                                    break;
                                default :
                                    $args[] = $request->param($name, $def);
                                    break;
                            }
                        }
                    }
                    $example = new $class();
                    if ($request->isAjax()) {
                        $request->setContentType('json');
                    } else {
                        $request->setContentType('html');
                    }
                    if (method_exists($example, 'initialize')) {
                        $example->initialize($request);
                    }
                    $out = $method->invokeArgs($example, $args);
                    if ($request->getContentType() == 'application/json' || $request->getContentType() == 'text/json') {
                        echo json_encode($out, JSON_UNESCAPED_UNICODE);
                        exit;
                    } else {
                        if (is_array($out)) {
                            $request->setContentType('json');
                            echo json_encode($out, JSON_UNESCAPED_UNICODE);
                            exit;
                        } else {
                            if (!empty($out)) {
                                echo $out;
                            }
                        }
                    }
                }
                else {
                    throw new RouteError('未公开方法:' . $act . 'Action');
                }
            } catch (\Error $e) {
                throw $e;
            } catch (\Exception $e) {
                throw $e;
            }
        } catch (RouteError $exception) {
            Route::rethrow($exception);
        } catch (\Exception $exception) {
            Route::rethrow($exception);
        } catch (\Error $error) {
            Route::rethrow($error);
        }
    }

}
