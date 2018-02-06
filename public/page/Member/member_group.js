// function setPageData(pagedata){
// 	$.post('/windows/Account/account_list.html',{page:pagedata.curr,pagesize:pagedata.limit},function(callback){
// 		switch(parseInt(callback.status)){
// 			case 1:
// 				try{
// 					data=callback.data;
// 					var str='';
// 					for(var cache in  data){
// 						switch(parseInt(data[cache].account_type)){
// 							case 2:
// 								var account_type='认证订阅号';
// 								break;
// 							case 3:
// 								var account_type='普通服务号';
// 								break;
// 							case 4:
// 								var account_type='认证服务号';
// 								break;
// 							default :
// 								var account_type='普通订阅号';
// 						}
// 						str+='<div class="layui-row"><div class="layui-col-md5"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><img src="'+data[cache].avatar+'" class="layui-nav-img"></li><li class="layui-nav-item"><a >'+account_type+'</a></li><li class="layui-nav-item"><a >'+data[cache].name+'</a></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].description+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="member_group?account_id='+data[cache].id+'">用户分组</a></li><li class="layui-nav-item"><a href="member?account_id='+data[cache].id+'">用户管理</a></li></ul></div></div>';
// 					}
// 					$('#member-group-list-data').html('');
// 					$('#member-group-list-data').html(str);
// 				}catch(err){

// 				}
// 				break;
// 			case -1:
// 				msgs('登录失效',0,5,function(){
// 					top.location.href="/windows/Admin/login";
// 				});
// 				break;
// 			default:

// 				break;
// 		}
		
// 	},'json');
// }
// $.post('/windows/Account/account_list_count.html',{},function(callback){
// 	switch(parseInt(callback.status)){
// 		case 1:
// 			layui.laypage.render({
// 				elem: 'account-list-page',
// 				count: callback.data ,//数据总数
// 				jump: setPageData
// 			});
// 		break;
// 		case -1:
// 			msgs('登录失效',0,5,function(){
// 				top.location.href="/windows/Admin/login";
// 			});
// 			break;
// 		default:

// 			break;
// 	}
// },'json');

$.post('member_group_data.html',{},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			try{
				data=callback.data;
				var str='';
				for(var cache in  data){
					str+='<div class="layui-col-md5"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].name+'</a></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].count+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="member_group_edit?groupid='+data[cache].id+'">修改分组</a></li><li class="layui-nav-item"><a class="member_group_delete" data-id="'+data[cache].id+'">删除分组</a></li></ul></div></div>';
					// str+='<div class="layui-col-md5"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].name+'</a></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].count+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="member_group_edit?groupid='+data[cache].id+'">修改分组</a></li><li class="layui-nav-item"><a href="member_group_user?member_group='+data[cache].id+'">分组用户</a></li></ul></div></div>';
				}
				$('#member-group-list-data').html('');
				$('#member-group-list-data').html(str);
			}catch(err){

			}
		break;
		case -1:
			msgs('登录失效',0,5,function(){
				top.location.href="/windows/Admin/login";
			});
			break;
		case -101:
			msgs(callback.message,0,5,function(){
				top.chooseAccount();
			});
			break;
		default:

			break;
	}
},'json');

$('.layui-back').click(function(){
	history.back();
});
$("#member-group-list-data").delegate('.member_group_delete', 'click', function(event) {
	var groupid=$(this).attr("data-id");
	confirms('确认删除此分组',function(){
		$.post('member_group_delete.html',{id:groupid},function(callback){
			switch(parseInt(callback.status)){
				case 1:
					msgs('删除成功',0,1,function(){
						location.reload();
					});
				break;
				case -1:
					msgs('登录失效',0,5,function(){
						top.location.href="/windows/Admin/login";
					});
					break;
				case -101:
					msgs(callback.message,0,5,function(){
						top.chooseAccount();
					});
					break;
				default:
					msgs(callback.message,0,5);
					break;
			}
		},'json');
	});
});