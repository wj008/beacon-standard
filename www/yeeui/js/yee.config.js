Yee.config = {
    version: (function () {
        return '1.0.2';
        //return new Date().getTime();
    }()),
    paths: {
        'json': (function () {
            return (!!window.JSON) ? '' : 'json2.js'
        })(),
        'jquery-cookie': 'jquery.cookie.js',
        'jquery-ui': '../jquery-ui/jquery-ui.min.js',
        'layer': (function () {
            if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                return '../layer_mobile/layer.js';
            }
            return '../layer/layer.js';
        })(),
        'yee-layer': 'yee.layer.js',
        'yee-validate': 'yee.validate-3.0.0.js',
        'yee-confirm': 'yee.confirm.js',
        'yee-ajaxlink': 'yee.ajaxlink.js',
        'yee-ajaxform': 'yee.ajaxform.js',
        'yee-editbox': 'yee.editbox.js',
        'yee-xheditor': 'yee.xheditor.js',
        'yee-date': 'yee.date.js',
        'yee-upfile': 'yee.upfile.js',
        'yee-upimage': 'yee.upimage.js',
        'yee-tabs': 'yee.tabs.js',
        'yee-dynamic': 'yee.dynamic.js',
        'yee-list': 'yee.list.js',
        'yee-pagebar': 'yee.pagebar.js',
        'yee-dialog': 'yee.dialog.js',
        'yee-searchform': 'yee.searchform.js',
        'yee-linkage': 'yee.linkage-1.2.0.js',
        'yee-upimggroup': 'yee.upimggroup.js'
    },
    depends: {
        'jquery-ui': ['css!../jquery-ui/custom.css'],
        'yee-layer': ['layer'],
        'yee-upfile': (function () {
            if (typeof FormData == 'function') {
                return ['yee-layer', 'html5-upload.js'];
            }
            return ['yee-layer', 'frame-upload.js'];
        }()),
        'yee-qiniu': (function () {
            if (typeof FormData == 'function') {
                return ['yee-layer', 'html5-upload.js'];
            }
            return ['yee-layer', 'frame-upload.js'];
        }()),
        'yee-upimage': (function () {
            if (typeof FormData == 'function') {
                return ['yee-layer', 'html5-upload.js'];
            }
            return ['yee-layer', 'frame-upload.js'];
        }()),
        'yee-upimggroup': ['yee-upfile'],
        'yee-confirm': ['yee-layer'],
        'yee-ajaxlink': ['yee-layer', 'json'],
        'yee-ajaxform': ['yee-layer'],
        'yee-editbox': ['yee-layer'],
        'yee-searchform': ['yee-layer'],
        'yee-validate': ['yee-layer'],
        'yee-list': ['yee-layer', 'vue.min.js'],
        'yee-date': ['../laydate/laydate.js'],
        'yee-dialog': ['yee-layer'],
        'yee-linkage': ['json'],
        'yee-dynamic': ['json'],
        'yee-xheditor': ['../xheditor/xheditor-1.2.2.min.js', '../xheditor/xheditor_lang/zh-cn.js'],
        'yee-datatable': ['vue.min.js', 'css!../css/yee-datatable.css'],
        'yee-plugin': ['base64.min.js', 'yee-layer'],
        'yee-markdown': ['../editormd/editormd.min.js', 'css!../editormd/css/editormd.css'],
        'yee-txupload': ['yee-layer', '//imgcache.qq.com/open/qcloud/js/vod/sdk/ugcUploader.js'],
    }
};