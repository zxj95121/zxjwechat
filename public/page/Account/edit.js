$('#encodingaeskeyGen').click(function(){
	var tokenV=EncodingAESKeyGen();
	$("#encodingaeskey").val(tokenV);
});
$('#tokenGen').click(function(){
	var tokenV=tokenGen();
	$("#token").val(tokenV);
});
$.post('edit_data.html',{id:$.url.param('id')},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			var data=callback.data;
			$(":input[name=name]").val(data.name);
			$(":input[name=id]").val(data.id);
			$(":input[name=original]").val(data.original);
			$(":input[name=appid]").val(data.appid);
			$(":input[name=appsecret]").val(data.appsecret);
			$(":input[name=token]").val(data.token);
			$(":input[name=encodingaeskey]").val(data.encodingaeskey);
			$("textarea[name=description]").val(data.description);
			$("select[name=account_type]").val(data.account_type);
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
layui.form.on('submit(submit)', function(data){
	$.post('edit_submit.html',data.field,function(callback){
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
