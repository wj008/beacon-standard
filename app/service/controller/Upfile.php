<?php

namespace app\service\controller;

use app\service\lib\ImageCrop;
use beacon\Config;
use beacon\Console;
use beacon\Controller;
use beacon\Request;
use beacon\Uploader;
use beacon\Utils;

class Upfile extends Controller
{
    //校验尺寸
    private function checkSize($path, $strictWidth, $strictHeight, $strictType)
    {
        $img = getimagesize(Utils::path($path));
        if ($img && isset($img[1])) {
            if ($strictType == 0) {
                if ($img[0] != $strictWidth || $img[1] != $strictHeight) {
                    throw new \Exception('图片尺寸不符，要求的尺寸是' . $strictWidth . 'x' . $strictHeight . ',上传的尺寸为：' . $img[0] . 'x' . $img[1]);
                }
            } elseif ($strictType == 1) {
                if ($img[1] <= 0 && $img[0] <= 0) {
                    throw new \Exception('未能够获取上传图片的尺寸，可能图片已损坏');
                }
                if ($strictWidth <= 0 && $strictHeight <= 0) {
                    throw new \Exception('给定的尺寸高宽不符');
                }
                $org = $img[0] / $img[1];
                $new = $strictWidth / $strictHeight;
                if ($img[0] < $strictWidth || $img[1] < $strictHeight) {
                    throw new \Exception('图片尺寸比例不符，要求的尺寸比例' . $strictWidth . ':' . $strictHeight . ',上传的尺寸为：' . $img[0] . 'x' . $img[1]);
                }
                if (abs($org - $new) > 0.01) {
                    throw new \Exception('图片尺寸比例不符，要求的尺寸比例' . $strictWidth . ':' . $strictHeight . ',上传的尺寸为：' . $img[0] . 'x' . $img[1]);
                }
            } else if ($strictType == 2) {
                if ($img[1] <= 0 && $img[0] <= 0) {
                    throw new \Exception('未能够获取上传图片的尺寸，可能图片已损坏');
                }
                if ($strictWidth <= 0 && $strictHeight <= 0) {
                    throw new \Exception('给定的尺寸高宽不符');
                }
                $org = $img[0] / $img[1];
                $new = $strictWidth / $strictHeight;
                if (abs($org - $new) > 0.01) {
                    throw new \Exception('图片尺寸比例不符，要求的尺寸比例' . $strictWidth . ':' . $strictHeight . ',可上传的尺寸为：' . $img[0] . 'x' . $img[1]);
                }
            }
        } else {
            throw new \Exception('未能够获取上传图片的尺寸，可能图片已损坏');
        }
    }

    //通用上传 兼容xheditor
    public function indexAction(Request $request)
    {
        $request->setContentType('json');
        if (!isset($_SERVER['DOCUMENT_ROOT'])) {
            $_SERVER['DOCUMENT_ROOT'] = Utils::path(ROOT_DIR, 'www');
        }
        $config = Config::get('upload.*');
        $catSizes = $request->param('catSizes:s', '');
        $strictSize = $request->param('strictSize:s', '');
        //严格要求图片比例
        try {
            $upload = new Uploader('filedata', $config);
            $upload->saveFile();
            if ($upload->getState() != 'SUCCESS') {
                $this->error($upload->getState());
            }
            $files = $upload->getFileInfo();
            //如果严格尺寸
            if (!empty($strictSize)) {
                if (preg_match('@^(\d+)x(\d+)(?:\:(\d))?$@', trim($strictSize), $match)) {
                    $strictWidth = intval($match[1]);
                    $strictHeight = intval($match[2]);
                    $strictType = isset($match[3]) ? intval($match[3]) : 0;
                    foreach ($files as $file) {
                        if ($file['isImage']) {
                            $this->checkSize($file['filePath'], $strictWidth, $strictHeight, $strictType);
                        }
                    }
                }
            }

            if (!empty($catSizes)) {
                $temp = explode(',', $catSizes);
                $sizes = [];
                foreach ($temp as $item) {
                    if (preg_match('@^(\d+)(?:x(\d+))?(?:\:(\d))?$@', trim($item), $match)) {
                        $sizes[] = [
                            'width' => intval($match[1]),
                            'height' => isset($match[2]) ? intval($match[2]) : intval($match[1]),
                            'mode' => isset($match[3]) ? intval($match[3]) : 1,
                        ];
                    }
                }
                foreach ($files as $file) {
                    if ($file['isImage']) {
                        foreach ($sizes as $size) {
                            ImageCrop::catSize($file['filePath'], $size['width'], $size['height'], $size['mode']);
                        }
                    }
                }
            }
            $msg = array();
            $msg['url'] = $files[0]['url'];
            $msg['localName'] = $files[0]['fileName'];
            $msg['orgName'] = $files[0]['name'];
            $msg['files'] = [];
            foreach ($files as $file) {
                $msg['files'][] = [
                    'url' => $file['url'],
                    'localName' => $file['fileName'],
                    'orgName' => $file['name'],
                ];
            }
            $this->success("上传成功", $msg);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function testAction()
    {
        $info = [];
        $info[] = '<form action="/service/upfile" enctype="multipart/form-data" method="post">';
        $info[] = '图片尺寸：<input type="text" name="catSizes"> 逗号隔开 如 100x100,200,200x300<br>';
        $info[] = '严格要求尺寸：<input type="text" name="strictSize"> 逗号隔开 如 100x100:1<br>';
        $info[] = '上传文件：<input type="file" name="filedata" multiple="multiple"><br>';
        $info[] = '<input type="submit" value="Upload"></form>';
        return join('', $info);
    }
}