// $.post('member_edit_data.html',{member_id:$.url.param('member_id')},function(callback){
// 	switch(parseInt(callback.status)){
// 		case 1:
// 			var data=callback.data;
// 			$(":input[name=name]").val(data.name);
// 			$(":input[name=id]").val(data.id);
// 			$(":input[name=original]").val(data.original);
// 			$(":input[name=appid]").val(data.appid);
// 			$(":input[name=appsecret]").val(data.appsecret);
// 			$(":input[name=token]").val(data.token);
// 			$(":input[name=encodingaeskey]").val(data.encodingaeskey);
// 			$("textarea[name=description]").val(data.description);
// 			$("select[name=account_type]").val(data.account_type);
// 			layui.form.render('select');
// 			break;
// 		case -1:
// 			msgs('登录失效',0,5,function(){
// 				top.location.href="/windows/Admin/login";
// 			});
// 			break;
// 		case -101:
// 			msgs(callback.message,0,5,function(){
// 				top.chooseAccount();
// 			});
// 			break;	
// 		default:
			
// 			break;
// 	}
// },'json');
layui.form.on('submit(submit)', function(data){
	$.post('member_group_add_submit.html',data.field,function(callback){
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