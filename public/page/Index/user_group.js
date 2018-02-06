
function setPageData(pagedata){
	$.post('user_group_list.html',{page:pagedata.curr,pagesize:pagedata.limit},function(callback){
		switch(parseInt(callback.status)){
			case 1:
				try{
					data=callback.data;
					var str='';
					for(var cache in  data){
						str+='<div class="layui-row"><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><span>'+data[cache].name+'</span></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><span>'+data[cache].description+'</span></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="user_group_account?id='+data[cache].id+'">设置权限</a></li><li class="layui-nav-item"><a href="user_group_edit?id='+data[cache].id+'">修改分组</a></li><li class="layui-nav-item win-event" win-event="deleteGroup('+data[cache].id+','+pagedata.curr+','+pagedata.limit+')"><a>删除分组</a></li></ul></div></div>';
					}
					$('#user-group-list-data').html('');
					$('#user-group-list-data').html(str);
				}catch(err){

				}
				break;
			case -1:
				msgs('登录失效',0,5,function(){
					top.location.href="/windows/Admin/login";
				});
				break;
			default:

				break;
		}
		
	},'json');
}
$.post('user_group_list_count.html',{},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			layui.laypage.render({
				elem: 'user-group-list-page',
				count: callback.data ,//数据总数
				jump: setPageData
			});
		break;
		case -1:
			msgs('登录失效',0,5,function(){
				top.location.href="/windows/Admin/login";
			});
			break;
		default:

			break;
	}
},'json');
$("#user-group-list-data").delegate('.win-event', 'click', function(event){
	eval($(this).attr('win-event'));
});
function deleteGroup(id=0,inpage=0,pagelimit=10){
	var id=parseInt(id);
	var inpage=parseInt(inpage);
	var pagelimit=parseInt(pagelimit);
	confirms('是否删除此分组',function(){
		$.post('user_group_list_delete.html',{id:id,page:inpage,pagesize:pagelimit},function(callback){
			switch(parseInt(callback.status)){
				case 1:
					try{
						data=callback.data;
						var str='';
						for(var cache in  data){
							str+='<div class="layui-row"><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><span>'+data[cache].name+'</span></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><span>'+data[cache].description+'</span></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="user_group_account?id='+data[cache].id+'">设置权限</a></li><li class="layui-nav-item"><a href="user_group_edit?id='+data[cache].id+'">修改分组</a></li><li class="layui-nav-item win-event" win-event="deleteGroup('+data[cache].id+','+inpage+','+pagelimit+')"><a>删除分组</a></li></ul></div></div>';
						}
						$('#user-group-list-data').html('');
						$('#user-group-list-data').html(str);
						msgs('删除成功',0,1,function(){});
					}catch(err){

					}
				break;
				case -1:
					msgs('登录失效',0,5,function(){
						top.location.href="/windows/Admin/login";
					});
					break;
				default:

					break;
			}
		},'json');
	});
	
}