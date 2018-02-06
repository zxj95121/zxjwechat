layui.form.on('submit(submit)', function(data){
	$.post('user_group_add_submit.html',data.field,function(callback){
		switch(parseInt(callback.status)){
			case 1:
				msgs('添加成功',0,1,function(){
					history.back();
				});
				break;
			case -1:
				msgs('登录失效',0,5,function(){
					top.location.href="/windows/Admin/login";
				});
				break;
			default:
				msgs('添加失败',0,5);
				break;
		}
	},'json');
	return false;
});
$('.layui-back').click(function(){
	history.back();
});