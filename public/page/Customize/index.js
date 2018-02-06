$(function(){

    $('.addLevel1, .addLevel2').click(function(){
        var html = $('#layui-form1').html();
        var level = $(this).attr('class').match(/addLevel[1-2]{1}$/)[0].substr(8);
        if (level == 2) {
            var title = '添加二级菜单';
            var count = $('#menu2List li').length;
            if (count >= 5) {
                layer.msg('二级菜单不能超过5个');
                return 0;
            }
        } else {
            var title = '添加一级菜单';
            var count = $('#menu1List li').length;
            if (count >= 3) {
                layer.msg('一级菜单不能超过3个');
                return 0;
            }
        }

        var layerOpen = layer.open({
            type: 1 //此处以iframe举例
            , title: '添加一级菜单'
            , area: ['80%', '500px']
            , shade: 0
            , maxmin: true
            , offset: [ //为了演示，随机坐标
                '40px'
            ]
            , content: html
            , btn: ['确认添加'] //只是为了演示
            , yes: function () {
                console.log('yes');
                var name = $('input[name="name-menu1"]').eq(1).val();
                var type = $('select[name="type-menu1"] option:selected').eq(1).val();
                var url = $('input[name="url-menu1"]').eq(1).val();

                if (level == 2) {
                    var mid = $('#menu1List li[class*="active"]').attr('mid');
                } else {
                    var mid = 0;
                }

                console.log(level);
                console.log(mid);
                $.ajax({
                    url: 'addMenu'+level+'.html',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        name: name,
                        type: type,
                        url: url,
                        mid: mid
                    },
                    success: function(data) {
                        if (data.errcode == 0) {
                            console.log('添加成功');
                        } else {
                            console.log(data.reason);
                        }

                        $('#menu'+level+'List').append('<li class="list-group-item" mid="'+data.id+'"> <span > '+name+'</span > <div> <svg height="32" class="octicon octicon-trashcan" viewBox="0 0 12 16" version="1.1" width="24" aria-hidden="true"> <path fill-rule="evenodd" d="M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z"></path> </svg<svg height="32" class="octicon octicon-trashcan" viewBox="0 0 12 16" version="1.1" width="24" aria-hidden="true"> <path fill-rule="evenodd" d="M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z"></path> </svg> </div > </li >');
                        layer.close(layerOpen);
                    }
                })
            }

            , zIndex: layer.zIndex //重点1
            , success: function (layero) {
                layer.setTop(layero); //重点2
            }
        });
    })
    
    /* select 一级菜单框 */
    $(document).on('change', '.type-menu1', function(){
        var type = $(this).find('option:selected').val();
        if (type != 2) {
            $(this).parents('.input-group').next().hide();
        } else {
            $(this).parents('.input-group').next().show();
        }
    })

    /* 点击一级菜单 */
    $('#menu1List li').click(function(e){
        $('#menu1List li[class*="active"]').removeClass('active');
        $(this).addClass('active');
        var mid = $(this).attr('mid');

        $('#menu2List li').remove();
        $.each(menu2, function(i, n) {
            if (n.pid == mid) {
                if (n.type == 2){
                    var url_key = n.url; 
                } else {
                    var url_key = '发送消息';
                }
                $('#menu2List').append('<li class="list-group-item" mid="' + n.id + '"> <span > ' + n.name + ' / ' + url_key +'</span > <div class="deleteMenu"> <svg height="32" class="octicon octicon-trashcan" viewBox="0 0 12 16" version="1.1" width="24" aria-hidden="true"> <path fill-rule="evenodd" d="M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z"></path> </svg<svg height="32" class="octicon octicon-trashcan" viewBox="0 0 12 16" version="1.1" width="24" aria-hidden="true"> <path fill-rule="evenodd" d="M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z"></path> </svg> </div > </li >');
            }
        })
    })

    /* 删除自定义菜单按钮 */
    $(document).on('click', '.deleteMenu', function(e){
        var dom = $(this).parents('li');
        var mid = dom.attr('mid');
        var id = $(this).parents('.list-group').attr('id');

        // layer.confirm('删除一级菜单，二级菜单也将一并删除，确认删除吗？', {
        layer.confirm('确认删除菜单吗？', {
            btn: ['确认', '取消'] //可以无限个按钮
            , btn3: function (index, layero) {
                layer.close(index);
            }
        }, function (index, layero) {
            layer.close(index);
            $.ajax({
                url: 'deleteMenu.html',
                type: 'post',
                dataType: 'json',
                data: {
                    mid: mid
                },
                success: function(data) {
                    if (data.errcode == 0) {
                        // console.log(data);
                        //在这里对js变量的值没处理，当然不处理也不影响效果。
                        layer.msg('删除成功', {'time': 1500});
                        if (id == 'menu1List') {
                            // var that = $('#menu1List').find('li[mid="'+ mid +'"]');
                            if (dom.hasClass('active')) {
                                $('#menu2List li').remove();
                            }
                            dom.remove();
                        } else {
                            dom.remove();
                        }
                        
                    } else {
                        console.log(data.reason);
                    }
                }
            })
        });
    })

    /* 发布按钮 */
    $('#publishDiv button').click(function(){
        $.ajax({
            url: 'ajaxShowMenu.html',
            type: 'post',
            dataType: 'json',
            data: {
            },
            success: function (data) {
                console.log(data);
                if (data.errcode == 0) {
                    layer.msg('发布成功!');
                }
            }
        })
    })
})