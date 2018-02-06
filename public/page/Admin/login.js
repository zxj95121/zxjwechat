layui.form.on('submit(submit)', function(data){
	$.post('login_submit.html',data.field,function(callback){
		switch(parseInt(callback.status)){
			case 1:
				msgs('登录成功',0,1,function(){
					top.location.href="/windows/Index/index";
				});
				break;
			default:
				msgs(callback.message,0,5);
				break;
		}
	},'json');
});