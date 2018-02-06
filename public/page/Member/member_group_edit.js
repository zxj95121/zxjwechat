$.post('member_group_edit_data.html',{groupid:$.url.param('groupid')},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			var data=callback.data;
			$(":input[name=groupname]").val(data.name);
			$(":input[name=id]").val(data.id);
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
			msgs(callback.message,0,5,function(){
				history.back();
			});
			break;
	}
},'json');

layui.form.on('submit(submit)', function(data){
	$.post('member_group_edit_submit.html',data.field,function(callback){
		switch(parseInt(callback.status)){
			case 1:
				msgs('修改成功',0,1,function(){
					history.back();
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
	return false;
});
$('.layui-back').click(function(){
	history.back();
});