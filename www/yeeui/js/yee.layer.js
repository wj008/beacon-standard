var extendLayer = function (layer) {
    //修复移动端调用YEE 时 LayerMobile兼容问题
    if (typeof(layer.open) == 'function') {
        //弹窗
        if (typeof(layer.alert) == 'undefined') {
            layer.alert = function (msg, option, func) {
                var data = {
                    content: msg
                    , btn: '确定'
                }
                if (typeof func == 'function') {
                    data['yes'] = func;
                }
                return layer.open(data);
            }
        }
        //确认
        if (typeof(layer.confirm) == 'undefined') {
            layer.confirm = function (msg, yesfunc, nofunc) {
                var data = {
                    content: msg
                    , btn: ['确认', '取消']
                }
                if (typeof yesfunc == 'function') {
                    data['yes'] = yesfunc;
                }
                return layer.open(data);
            }
        }
        //消息
        if (typeof(layer.msg) == 'undefined') {
            layer.msg = function (msg, option) {
                var data = {
                    content: msg
                    , skin: 'msg'
                }
                if (option && option.time) {
                    data['time'] = Math.round(option.time / 1000);
                } else {
                    data['time'] = 1;
                }
                return layer.open(data);
            }
        }
        //加载--
        if (typeof(layer.load) == 'undefined') {
            layer.load = function (type, option) {
                return layer.open({type: 2});
            }
        }
    }
}

if (window.top !== window) {
    if (window.top.layer) {
        window.layer = window.top.layer;
        extendLayer(window.layer);
    } else if (window.top.Yee) {
        window.top.Yee.loader('layer', function () {
            window.layer = window.top.layer;
            extendLayer(window.layer);
        });
    }
} else {
    extendLayer(window.layer);
}

