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
// 						str+='<div class="layui-row"><div class="layui-col-md5"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><img src="'+data[cache].avatar+'" class="layui-nav-img"></li><li class="layui-nav-item"><a >'+account_type+'</a></li><li class="layui-nav-item"><a >'+data[cache].name+'</a></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].description+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="member_group.html">用户分组</a></li><li class="layui-nav-item"><a href="member_user.html">用户管理</a></li></ul></div></div>';
// 					}
// 					$('#account-list-data').html('');
// 					$('#account-list-data').html(str);
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

