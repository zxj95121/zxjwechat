function setPageData(pagedata){
	$.post('member_user_list.html',{page:pagedata.curr,pagesize:pagedata.limit},function(callback){
		switch(parseInt(callback.status)){
			case 1:
				try{
					data=callback.data;
					var str='';
					for(var cache in  data){
						var subscribe=data[cache].subscribe==1 ? '已关注' : '未关注';
						switch(parseInt(data[cache].gender)){
							case 1:
								var gender='男';
								break;
							case 2:
								var gender='女';
								break;
							default:
								var gender='未获取';
						}
						str+='<div class="layui-row"><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><img src="'+data[cache].avatar+'" class="layui-nav-img"></li><li class="layui-nav-item"><a >'+data[cache].nickname+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item">'+data[cache].openid+'<a></a></li></ul></div><div class="layui-col-md2"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a >'+gender+'</a></li></ul></div><div class="layui-col-md2"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+subscribe+'</a></li></ul></div><div class="layui-col-md2"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a href="member_edit?member_id='+data[cache].openid+'">用户管理</a></li></ul></div></div>';
					}
					$('#member-user-list-data').html('');
					$('#member-user-list-data').html(str);
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
$.post('member_user_count.html',{},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			layui.laypage.render({
				elem: 'member-user-list-page',
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


$('.layui-back').click(function(){
	history.back();
});
$('#synchMember').click(function(){
	var loading=layer.load(3,{shade:0.4});
	$.post('synchMember.html',{},function(callback){
		switch(parseInt(callback.status)){
			case 1:

			break;
			case -1:
				msgs('登录失效',0,5,function(){
					top.location.href="/windows/Admin/login";
				});
				break;
			default:

				break;
		}
		layer.close(loading);
	},'json');
});