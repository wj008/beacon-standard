(function ($, Yee, layer) {


    var Index = 0;

    function YeeDatatable(qem, option) {
        Index++;
        var self = this;
        //获取表单id
        this.id = (function () {
            var id = qem.attr('id') || 'yee_dt_' + Index;
            if (!qem.attr('id')) {
                qem.attr('id', id);
            }
            return id;
        })();
        //获取存储名称
        this.tbname = (window.location.pathname + '') + '/t_' + this.id;
        this.cacheData = {};
        //加载本地存储数据
        var loadCache = function () {
            if (window.localStorage && window.localStorage.datatableCacheData) {
                try {
                    self.cacheData = JSON.parse(window.localStorage.datatableCacheData);
                    var tableCache = self.cacheData[self.tbname] || {};
                    var ths = qem.find('thead th');
                    qem.width(tableCache.w);
                    for (var i = 0; i < ths.length; i++) {
                        if (tableCache['th' + i]) {
                            $(ths.get(i)).width(tableCache['th' + i]);
                        }
                    }
                } catch (e) {
                }
            }
        }
        //添加外围元素---
        qem.wrap('<div class="yee-datatable-layer"></div>');
        this.table_layer = qem.parent('.yee-datatable-layer');
        qem.wrap('<div class="yee-datatable-fixed"></div>');
        this.table_fixed = qem.parent('.yee-datatable-fixed');
        qem.wrap('<div class="yee-datatable-scroll"></div>');
        this.table_scroll = qem.parent('.yee-datatable-scroll');
        this.table_scroll.height(1);
        $('<div class="yee-datatable-header"></div>').prependTo(this.table_layer);

        var leftThs = qem.find('thead th');
        if (option['leftFixed']) {
            var len = parseInt(option['leftFixed']);
            for (var i = 0; i < leftThs.length; i++) {
                if (len == 0) {
                    break;
                }
                var th = leftThs.eq(i);
                th.data('fixed', 'left');
                len--;
            }
        }
        if (option['rightFixed']) {
            var len = parseInt(option['rightFixed']);
            for (var i = leftThs.length - 1; i >= 0; i--) {
                if (len == 0) {
                    break;
                }
                var th = leftThs.eq(i);
                th.data('fixed', 'right');
                len--;
            }
        }

        var idDispley = false;
        var leftFixed = null;
        var rightFixed = null;

        var hasLeftFixed = false;
        var hasRightFixed = false;
        var send = function (sendOption, prams, func) {
            sendOption = $.extend(option, sendOption || {});
            var args = Yee.parseUrl(sendOption.url);
            args.path = args.path || window.location.pathname;
            for (var key in prams) {
                args.prams[key] = prams[key];
            }
            $.ajax({
                type: sendOption.method,
                url: args.path,
                data: args.prams,
                cache: false,
                dataType: 'json',
                success: function (ret) {
                    //拉取数据成功
                    if (ret.status === true) {
                        if (sendOption.showMsg && layer && ret.message && typeof (ret.message) === 'string') {
                            layer.msg(ret.message, {icon: 1, time: 1000});
                        }
                        if (ret.data) {
                            func(ret.data);
                        }
                    }
                    //拉取数据错误
                    if (ret.status === false) {
                        if (sendOption.showMsg && layer && ret.error && typeof (ret.error) === 'string') {
                            layer.msg(ret.error, {icon: 0, time: 2000});
                        }
                        qem.triggerHandler('error', [ret]);
                    }
                }
            });
        };

        var app = new Vue({
            el: qem.find('tbody').get(0),
            data: {
                list: []
            },
            methods: {
                toggleChildren: function (item) {
                    item.show = !item.show;
                    if (item['id']) {
                        if (item.show) {
                            send({}, {'pid': item.id}, function (data) {
                                item.child = data.list;
                                app.$nextTick(function () {
                                    Yee.update(qem);
                                });
                            });
                        }
                    }
                },
            }
        });

        var order = function () {
            var that = $(this);
            var name = that.data('order');
            qem.find('thead th').not(that).removeClass('down').removeClass('up');
            if (leftFixed) {
                leftFixed.find('thead th').not(that).removeClass('down').removeClass('up');
            }
            if (rightFixed) {
                rightFixed.find('thead th').not(that).removeClass('down').removeClass('up');
            }
            if (that.is('.down')) {
                that.removeClass('down').addClass('up');
                qem.triggerHandler('order', [{name: name, order: 1}]);
            } else {
                that.removeClass('up').addClass('down');
                qem.triggerHandler('order', [{name: name, order: -1}]);
            }
        }

        var bindLeftFixedEvent = function () {
            //接管高亮
            var ltrs = leftFixed.find('tr');
            ltrs.on('mouseenter', function () {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('mouseenter');
                }
            });
            ltrs.on('mouseleave', function () {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('mouseleave');
                }
            });
            ltrs.on('click', function () {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('click');
                }
            });
            qem.find('tr').each(function (idx, elem) {
                var lftr = leftFixed.find('tr').eq(idx);
                this._yee_datatable_bind_left = lftr;
                lftr.get(0)._yee_datatable_bind_org = $(this);
                lftr.width($(this).width());
            });
            leftFixed.find(':input.check-all').on('click', function () {
                var table = $(this).parents('table:first');
                table.find('.check-item').prop('checked', $(this).prop('checked'));
            });
            Yee.update(leftFixed);
        }

        var bindRightFixedEvent = function () {
            //接管高亮
            var ltrs = rightFixed.find('tr');
            ltrs.on('mouseenter', function () {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('mouseenter');
                }
            });

            ltrs.on('mouseleave', function () {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('mouseleave');
                }
            });

            ltrs.on('click', function (ev) {
                if (this._yee_datatable_bind_org) {
                    this._yee_datatable_bind_org.trigger('click');
                }
            });
            qem.find('tr').each(function (idx, elem) {
                var qtr = $(this);
                var lftr = rightFixed.find('tr').eq(idx);
                this._yee_datatable_bind_right = lftr;
                lftr.get(0)._yee_datatable_bind_org = $(this);
                lftr.width(qtr.width());
                var temtd = qtr.find('td:first');
                lftr.find('td:first').height(temtd.height() + 1);
            });
            if (qem.outerWidth() < self.table_fixed.width()) {
                rightFixed.hide();
            } else {
                rightFixed.show();
            }
            rightFixed.find(':input.check-all').on('click', function () {
                var table = $(this).parents('table:first');
                table.find('.check-item').prop('checked', $(this).prop('checked'));
            });
            Yee.update(rightFixed);
        }

        this.updateLeftFixed = function () {
            if (leftFixed == null) {
                return;
            }
            var tbody = leftFixed.find('tbody').empty();
            var ltrs = qem.find('tbody tr');
            for (var n = 0; n < ltrs.length; n++) {
                tbody.append('<tr></tr>');
            }
            var lfths = qem.find('thead th');
            for (var i = 0; i < lfths.length; i++) {
                var th = lfths.eq(i);
                if (th.data('fixed') == 'left') {
                    var ttr = tbody.children('tr');
                    for (var n = 0; n < ltrs.length; n++) {
                        var ltd = ltrs.eq(n).children('td').eq(i);
                        ttr.eq(n).append(ltd.clone());
                    }
                }
            }
            bindLeftFixedEvent();
        }

        this.updateRightFixed = function () {
            if (rightFixed == null) {
                return;
            }
            var tbody = rightFixed.find('tbody').empty();
            var ltrs = qem.find('tbody tr');
            for (var n = 0; n < ltrs.length; n++) {
                tbody.append('<tr></tr>');
            }
            var lfths = qem.find('thead th');
            for (var i = 0; i < lfths.length; i++) {
                var th = lfths.eq(i);
                if (th.data('fixed') == 'right') {
                    var ttr = tbody.children('tr');
                    for (var n = 0; n < ltrs.length; n++) {
                        var ltd = ltrs.eq(n).children('td').eq(i);
                        ttr.eq(n).append(ltd.clone());
                    }
                }
            }
            bindRightFixedEvent();
        }

        this.createLeftFixed = function () {
            if (!hasLeftFixed || leftFixed) {
                return;
            }
            var self = this;
            leftFixed = qem.clone().empty();
            var thead = $('<thead><tr></tr></thead>').appendTo(leftFixed);
            var tbody = $('<tbody></tbody>').appendTo(leftFixed);
            leftFixed.removeAttr('id');
            leftFixed.removeAttr('yee-module');
            leftFixed.addClass('fixed-left');
            leftFixed.appendTo(self.table_layer);
            leftFixed.width('auto');
            var lfths = qem.find('thead th');
            var ltrs = qem.find('tbody tr');
            for (var n = 0; n < ltrs.length; n++) {
                tbody.append('<tr></tr>');
            }
            var inc = 1;
            for (var i = 0; i < lfths.length; i++) {
                var th = lfths.eq(i);
                if (th.data('fixed') == 'left') {
                    var xth = th.clone();
                    thead.children('tr').append(xth);
                    xth.width(th.width() + inc);
                    inc = 0;
                    th.get(0)._yee_datatable_bind_left = xth;
                    if (th.data('order')) {
                        xth.on('click', order);
                    }
                    var ttr = tbody.children('tr');
                    for (var n = 0; n < ltrs.length; n++) {
                        var ltd = ltrs.eq(n).children('td').eq(i);
                        ttr.eq(n).append(ltd.clone());
                    }
                }
            }
            bindLeftFixedEvent();
        }
        this.createRightFixed = function () {
            if (!hasRightFixed || rightFixed) {
                return;
            }
            var self = this;
            rightFixed = qem.clone().empty();
            rightFixed.removeAttr('id');
            rightFixed.removeAttr('yee-module');
            var thead = $('<thead><tr></tr></thead>').appendTo(rightFixed);
            var tbody = $('<tbody></tbody>').appendTo(rightFixed);
            rightFixed.addClass('fixed-right')
            rightFixed.appendTo(self.table_layer);
            rightFixed.width('auto');
            var lfths = qem.find('thead th');
            var ltrs = qem.find('tbody tr');
            for (var n = 0; n < ltrs.length; n++) {
                tbody.append('<tr></tr>');
            }
            var inc = 1;
            for (var i = 0; i < lfths.length; i++) {
                var th = lfths.eq(i);
                if (th.data('fixed') == 'right') {
                    var xth = th.clone();
                    xth.width(th.width() + inc);
                    inc = 0;
                    thead.children('tr').append(xth);
                    if (th.data('order')) {
                        xth.on('click', order);
                    }
                    var ttr = tbody.children('tr');
                    for (var n = 0; n < ltrs.length; n++) {
                        var ltd = ltrs.eq(n).children('td').eq(i);
                        ttr.eq(n).append(ltd.clone());
                    }
                }
            }

            bindRightFixedEvent();
        }

        var bindEvent = function () {
            var trs = qem.find('tbody tr').removeClass('selected');
            trs.hover(function () {
                $(this).addClass('hover');
                if (this._yee_datatable_bind_left) {
                    this._yee_datatable_bind_left.addClass('hover');
                }
                if (this._yee_datatable_bind_right) {
                    this._yee_datatable_bind_right.addClass('hover');
                }
            }, function () {
                $(this).removeClass('hover');
                if (this._yee_datatable_bind_left) {
                    this._yee_datatable_bind_left.removeClass('hover');
                }
                if (this._yee_datatable_bind_right) {
                    this._yee_datatable_bind_right.removeClass('hover');
                }
            });
            trs.on('click', function (ev) {
                trs.filter('.selected').removeClass('selected');
                $(this).addClass('selected');
                if (this._yee_datatable_bind_left) {
                    leftFixed.find('tr').filter('.selected').removeClass('selected');
                    this._yee_datatable_bind_left.addClass('selected');
                }
                if (this._yee_datatable_bind_right) {
                    rightFixed.find('tr').filter('.selected').removeClass('selected');
                    this._yee_datatable_bind_right.addClass('selected');
                }
            });
        }

        var updataResizeBar = function () {
            var ths = qem.find('thead th');
            var inc = 1;
            for (var i = 0; i < ths.length; i++) {
                var th = $(ths.get(i));
                if (th.get(0)._yee_datatable_bind_left) {
                    th.get(0)._yee_datatable_bind_left.width(th.width() + inc);
                    inc = 0;
                }
                if (th.data('noResize') || th.data('fixed')) {
                    continue;
                }
                if (!self.cacheData[self.tbname]) {
                    self.cacheData[self.tbname] = {};
                }
                self.cacheData[self.tbname]['th' + i] = th.width();
                if (th[0].yee_rc) {
                    var oleft = th.offset().left + th.outerWidth() - 5;
                    th[0].yee_rc.offset({left: oleft});
                }
            }
        }

        this.source = function (source) {
            if (source.list && source.list instanceof Array) {
                app.list = source.list;
            } else if (source instanceof Array) {
                app.list = source;
            }
            app.$nextTick(function () {
                if (!idDispley) {
                    self.display();
                } else {
                    self.updateLeftFixed();
                    self.updateRightFixed();
                }
                bindEvent();
                updataResizeBar();
                var cbox = qem.find(':input.check-all');
                if (cbox.length > 0) {
                    cbox.prop('checked', false);
                }
                setTimeout(function () {
                    Yee.update(qem);
                    qem.triggerHandler('change', [source]);
                }, 10);
            });
        };

        this.display = function () {
            if (idDispley) {
                return;
            }
            idDispley = true;
            if (option.resize) {
                qem.addClass('resize');
                self.table_fixed.addClass('resize');
                loadCache();
            }
            var ths = qem.find('thead th');
            var cache = {
                left: 0,
                pageX: 0,
                width: 0,
                tb_width: 0,
                mover: null,
                th: null,
            };

            function movefunc(ev) {
                if (cache.mover == null) {
                    return;
                }
                var left = (ev.pageX - cache.pageX);
                qem.width(cache.tb_width + left);
                var scroll_width = self.table_scroll.width();
                if (cache.tb_width + left > scroll_width) {
                    self.table_scroll.width(cache.tb_width + left);
                }
                cache.th.width(cache.width + left);
                self.cacheData[self.tbname] = {
                    w: qem.width()
                }
                updataResizeBar();
                if (window.localStorage) {
                    window.localStorage.datatableCacheData = JSON.stringify(self.cacheData);
                }
                if (rightFixed) {
                    if (qem.outerWidth() <= self.table_fixed.width() + 2) {
                        rightFixed.hide();
                    } else {
                        rightFixed.show();
                    }
                }
            }

            //处理高度--
            if (option && option.fillBottom !== null) {
                $(window).resize(function () {
                    var wheight = $(window).height() - self.table_layer.offset().top - option.fillBottom;
                    self.table_layer.height(wheight);
                });
                setInterval(function () {
                    var wheight = $(window).height() - self.table_layer.offset().top - option.fillBottom;
                    self.table_layer.height(wheight);
                }, 1000);

                setTimeout(function () {
                    $(window).resize();
                }, 10);
            }
            var owidth = 0;
            for (var i = 0; i < ths.length; i++) {
                var th = $(ths.get(i));
                owidth += th.outerWidth();
                //排序
                if (th.data('order')) {
                    $('<i></i>').appendTo(th);
                    th.addClass('order');
                    th.on('click', order);
                }
                if (option.resize) {
                    //固定列
                    if (th.data('fixed')) {
                        if (th.data('fixed') == 'right') {
                            hasRightFixed = true;
                        } else {
                            hasLeftFixed = true;
                        }
                        continue;
                    }
                    //拖动
                    if (th.data('noResize')) {
                        continue;
                    }
                    var rc = $('<div class="rc-handle"></div>').appendTo(self.table_fixed);
                    th[0].yee_rc = rc;
                    var oleft = th.offset().left + th.outerWidth() - 5;
                    rc.offset({left: oleft});
                    rc.on('mousedown', {th: th}, function (ev) {
                        var rc = $(this);
                        var th = ev.data.th;
                        var offset = rc.offset();
                        cache.pageX = ev.pageX;
                        cache.left = offset.left;
                        cache.width = th.width();
                        cache.tb_width = qem.width();
                        cache.mover = rc;
                        cache.th = th;
                        qem.addClass('rc-not-select');
                        // self.table_scroll.width(cache.tb_width + 10);
                        $(document).on('mousemove', movefunc);
                    });
                    rc.on('mouseup', function (ev) {
                        $(document).off('mousemove', movefunc);
                        qem.removeClass('rc-not-select');
                        cache.left = 0;
                        cache.width = 0;
                        cache.tb_width = 0;
                        cache.pageX = 0;
                        cache.mover = null;
                        cache.th = null;
                        self.table_scroll.css('width', qem.width());
                    });
                }
            }

            qem.find(':input.check-all').on('click', function () {
                var table = $(this).parents('table:first');
                table.find('.check-item').prop('checked', $(this).prop('checked'));
            });

            if (option.resize) {
                $(document).on('mouseup', function (ev) {
                    $(document).off('mousemove', movefunc);
                    qem.removeClass('rc-not-select');
                    cache.left = 0;
                    cache.width = 0;
                    cache.tb_width = 0;
                    cache.pageX = 0;
                    cache.mover = null;
                    cache.th = null;
                    self.table_scroll.css('width', 'auto');
                });
                qem.width(owidth);
            }
            self.table_fixed.css('width', '100%');
            self.createLeftFixed();
            self.createRightFixed();
            if (rightFixed) {
                if (qem.outerWidth() <= self.table_fixed.width() + 2) {
                    rightFixed.hide();
                } else {
                    rightFixed.show();
                }
            }
            $(window).on('resize', function () {
                if (!rightFixed) {
                    return;
                }
                if (qem.outerWidth() <= self.table_fixed.width() + 2) {
                    rightFixed.hide();
                } else {
                    rightFixed.show();
                }
            });
            bindEvent();
            setTimeout(function () {
                updataResizeBar();
            }, 30);
        };
    }

    Yee.extend('table', 'datatable', function (elem, option) {

        var qem = $(elem);
        option = $.extend({method: 'get', showMsg: false, autoLoad: false, sendOrder: false, bindForm: null, fillBottom: null}, option);
        if (!option.url) {
            option.autoUrl = 1;
            if (/\/$/.test(String(window.location.pathname))) {
                option.url = window.location.pathname + 'index.json' + window.location.search;
            } else {
                option.url = window.location.pathname + '.json' + window.location.search;
            }
        }
        var datatable = new YeeDatatable(qem, option);
        var lastSendOption = null;
        var bind = option.bind || null;
        var send = function (sendOption) {
            //防止误触双击
            sendOption = $.extend(option, sendOption || {});
            lastSendOption = sendOption;
            var query = sendOption.url;
            var args = Yee.parseUrl(sendOption.url);
            if (option.autoUrl == 1 && typeof(window.history.replaceState) != 'undefined') {
                var thisUrl = Yee.toUrl({path: args.path.replace(/\.json$/i, ''), prams: args.prams});
                window.history.replaceState(null, document.title, thisUrl);
            }
            args.path = args.path || window.location.pathname;
            if (qem.triggerHandler('before', [sendOption]) === false) {
                return;
            }
            $.ajax({
                type: sendOption.method,
                url: args.path,
                data: args.prams,
                cache: false,
                dataType: 'json',
                success: function (ret) {
                    if (qem.triggerHandler('back', [ret]) === false) {
                        return;
                    }
                    //拉取数据成功
                    if (ret.status === true) {
                        if (sendOption.showMsg && layer && ret.message && typeof (ret.message) === 'string') {
                            layer.msg(ret.message, {icon: 1, time: 1000});
                        }
                        if (ret.data) {
                            qem.triggerHandler('source', [ret.data, query || ""]);
                            if (bind != null) {
                                $(bind).triggerHandler('source', [ret.data, query || ""]);
                            }
                        }
                        qem.triggerHandler('success', [ret]);
                    }
                    //拉取数据错误
                    if (ret.status === false) {
                        if (sendOption.showMsg && layer && ret.error && typeof (ret.error) === 'string') {
                            layer.msg(ret.error, {icon: 0, time: 2000});
                        }
                        qem.triggerHandler('error', [ret]);
                    }
                }
            });
        };

        if (option.sendOrder) {
            qem.on('order', function (ev, data) {
                var opt = lastSendOption || option;
                var args = Yee.parseUrl(opt.url);
                var val = data.name + '_' + (data.order == 1 ? 'asc' : 'desc');
                args.prams[option.sendOrder] = val;
                opt.url = Yee.toUrl(args);
                if (option.bindForm) {
                    $(option.bindForm).find(':input[name="' + option.sendOrder + '"]').val(val);
                }
                send(opt);
            });
        }
        qem.on('source', function (ev, source) {
            var data = qem.triggerHandler('filter', [source]);
            if (data !== void 0) {
                source = data;
            }
            if (source.list && source.list instanceof Array) {
                for (var i = 0; i < source.list.length; i++) {
                    var item = qem.triggerHandler('filterItem', [source.list[i]]);
                    if (item !== void 0) {
                        source.list[i] = item;
                    }
                }
            } else if (source instanceof Array) {
                for (var i = 0; i < source.length; i++) {
                    var item = qem.triggerHandler('filterItem', [source[i]]);
                    if (item !== void 0) {
                        source[i] = item;
                    }
                }
            }
            datatable.source(source);
        });
        qem.on('load', function (ev, sendOption) {
            if (sendOption == null) {
                sendOption = option;
            }
            else if (typeof(sendOption) == 'string') {
                sendOption = {url: sendOption};
            }
            send(sendOption);
        });
        qem.on('reload', function (ev, showMsg) {
            var sendOption = $.extend(lastSendOption, {showMsg: showMsg});
            send(sendOption);
        });
        qem.on('reset', function (ev, showMsg) {
            var sendOption = $.extend(option, {showMsg: showMsg});
            send(sendOption);
        });
        $(function () {
            if (option.autoLoad) {
                qem.triggerHandler('load');
            } else {
                datatable.display();
            }
        });


    });

})(jQuery, Yee, typeof(layer) == 'undefined' ? null : layer);