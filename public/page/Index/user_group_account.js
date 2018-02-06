// function account_index_init(){
// 	if(typeof(model)=='undefined'){
// 		model={};
// 	}
// 	model.account={};
// 	model.account.index={
// 		page:1,
// 		pagesize:10,

// 		get_page:function(){

// 			$.post('account_list.html',{page:model.account.index.page,pagesize:model.account.index.pagesize},function(callback){

// 			},'json');

// 		},
// 	}
// 	return model.account.index;
// }
// var model_account_index=account_index_init();
// model_account_index.get_page();
function setPageData(pagedata){
	$.post('account_list.html',{page:pagedata.curr,pagesize:pagedata.limit,id:$.url.param('id')},function(callback){
		switch(parseInt(callback.status)){
			case 1:
				try{
					data=callback.data.data;
					checked=callback.data.checked;
					var str='';
					for(var cache in  data){
						switch(parseInt(data[cache].account_type)){
							case 2:
								var account_type='认证订阅号';
								break;
							case 3:
								var account_type='普通服务号';
								break;
							case 4:
								var account_type='认证服务号';
								break;
							default :
								var account_type='普通订阅号';
						}
						switch($.inArray(parseInt(data[cache].id),checked)){
							case -1: 
								var checkedstr='';
								break;
							default:
								var checkedstr='checked';
						}
						str+='<div class="layui-row"><div class="layui-col-md5"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><img src="'+data[cache].avatar+'" class="layui-nav-img"></li><li class="layui-nav-item"><a >'+account_type+'</a></li><li class="layui-nav-item"><a >'+data[cache].name+'</a></li></ul></div><div class="layui-col-md4"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><a>&nbsp;'+data[cache].description+'</a></li></ul></div><div class="layui-col-md3"><ul class="layui-nav layui-bg-gray"><li class="layui-nav-item"><div class="layui-input-block"><input class="choose-account" lay-filter="choose-account" data-id="'+data[cache].id+'" type="checkbox" '+checkedstr+' name="switch" lay-skin="switch"></div></li></ul></div></div>';
					}
					$('#account-list-data').html('');
					$('#account-list-data').html(str);
					layui.form.render('checkbox');
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
$.post('account_list_count.html',{},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			layui.laypage.render({
				elem: 'account-list-page',
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

layui.form.on('switch(choose-account)', function(data){
	var dataid=$(data.elem).attr('data-id');
	if(data.elem.checked){
		var result =1;	
	}else{
		var result=0;
	}
	$.post('chooseAccount.html',{groupid:$.url.param('id'),id:dataid,result:result},function(callback){
		switch(parseInt(callback.status)){
			case 1:
				msgs('修改成功',0,1);
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
$('.layui-back').click(function(){
	history.back();
});