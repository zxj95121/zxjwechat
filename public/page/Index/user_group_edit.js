$.post('user_group_edit_data.html',{id:$.url.param('id')},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			var data=callback.data;
			$(":input[name=name]").val(data.name);
			$(":input[name=id]").val(data.id);
			$("textarea[name=description]").val(data.description);
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
layui.form.on('submit(submit)', function(data){
	$.post('user_group_edit_submit.html',data.field,function(callback){
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
			default:
				msgs('修改失败',0,5);
				break;
		}
	},'json');
	return false;
});

$('.layui-back').click(function(){
	history.back();
});