$.post('user_groups.html',{},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			var data=callback.data;
			for(var cache in  data){
				$("#groupid").append('<option value="'+data[cache].id+'">'+data[cache].name+'</option>');
			}
			$.post('edit_user_data.html',{id:$.url.param('id')},function(callback){
				switch(parseInt(callback.status)){
					case 1:
						var data=callback.data;
						$(":input[name=username]").val(data.username);
						$(":input[name=id]").val(data.id);
						$(":input[name=password]").val(data.password);
						$("textarea[name=remark]").val(data.remark);
						$("select[name=groupid]").val(data.groupid);
						layui.form.render('select');
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
	$.post('edit_user_submit.html',data.field,function(callback){
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