(function ($, Yee, layer) {

    Yee.extend('div', 'plugin', function (element, option) {
        var Index = 0;
        var qem = $(element);
        var template = Base64.decode(option.source);
        option.minSize = parseInt(option['minSize'] || 0);
        option.maxSize = parseInt(option['maxSize'] || 1000);
        Index = parseInt(option.index || '0');
        var addbtn = qem.find('a.plugin-add');
        var layout = qem.find('.plugin-content');

        var addItem = this.addItem = function () {
            Index++;
            if (layout.find('.plugin-item').length >= option.maxSize) {
                layer.alert('至多不可超过 ' + option.maxSize + ' 项。');
                return null;
            }
            var code = template.replace(/@@index@@/g, 'a' + Index);
            var item = $(code).appendTo(layout);
            if (layout.find('.plugin-item').length > option.minSize) {
                qem.find('.plugin-del').show();
            }
            updateIndex();
            qem.triggerHandler('addItem', [item]);
            Yee.update(item);
            return item;
        };

        var updateIndex = this.updateIndex = function () {
            var len = layout.find('.plugin-item').length;
            layout.find('.plugin-item').each(function (idx, el) {
                if (idx == 0) {
                    $(el).find('a.plugin-upsort').hide();
                } else {
                    $(el).find('a.plugin-upsort').show();
                }
                if (idx == len - 1) {
                    $(el).find('a.plugin-dnsort').hide();
                } else {
                    $(el).find('a.plugin-dnsort').show();
                }
                $(el).find('.plugin-index').text(idx + 1);
            });
            if(option.maxSize>0 && len>=option.maxSize){
                $('.plugin-add').hide();
            }else{
                $('.plugin-add').show();
            }
        }
        addbtn.on('click', addItem);
        //删除
        qem.on('click', 'a.plugin-del', function () {
            if (layout.find('.plugin-item').length <= option.minSize) {
                layer.alert('至少需要保留 ' + option.minSize + ' 项。');
                return;
            }
            var delitem = $(this).parents('.plugin-item:first');
            layer.confirm('确定要删除该行了吗？', function (idx) {
                delitem.remove();
                updateIndex();
                if (layout.find('.plugin-item').length <= option.minSize) {
                    qem.find('a.plugin-del').hide();
                }
                layer.close(idx);
            });
        });
        //插入
        qem.on('click', 'a.plugin-insert', function () {
            var item = addItem();
            var ttemp = $(this).parents('.plugin-item:first');
            if (item) {
                ttemp.before(item);
            }
            updateIndex();
        });
        //上移
        qem.on('click', 'a.plugin-upsort', function () {
            var ttemp = $(this).parents('.plugin-item:first');
            var utemp = ttemp.prev('.plugin-item');
            if (utemp.length > 0) {
                utemp.before(ttemp);
            }
            updateIndex();
            return false;
        });
        //下移
        qem.on('click', 'a.plugin-dnsort', function () {
            var ttemp = $(this).parents('.plugin-item:first');
            var dtemp = ttemp.next('.plugin-item');
            if (dtemp.length > 0) {
                dtemp.after(ttemp);
            }
            updateIndex();
            return false;
        });
        //如果存在至少行数
        if (option['minSize']) {
            if (layout.find('.plugin-item').length < option['minSize']) {
                for (var i = 0; i < option['minSize']; i++) {
                    addItem();
                    if (layout.find('.plugin-item').length >= option['minSize']) {
                        break;
                    }
                }
            }
        }
        updateIndex();
    });
})(jQuery, Yee, layer);