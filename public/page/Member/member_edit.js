$.post('member_edit_data.html',{member_id:$.url.param('member_id')},function(callback){
	switch(parseInt(callback.status)){
		case 1:
			var data=callback.data;
			$(":input[name=nickname]").val(data.nickname);
			$(":input[name=openid]").val(data.openid);
			$(":input[name=remark]").val(data.remark);
			$("#avatar").attr('src',data.avatar);
			var groups=data.groupdata;
			var optionstr='';
			for(var group in groups){
				optionstr+='<option value="'+groups[group].id+'">'+groups[group].name+'</option>';
			}
			$("select[name=groupid]").html(optionstr);
			$("select[name=groupid]").val(data.groupid);
			$(":input[name=gender][value="+data.gender+"]").attr("checked","checked");
			layui.form.render();
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
			
			break;
	}
},'json');
layui.form.on('submit(submit)', function(data){
	$.post('member_edit_submit.html',data.field,function(callback){
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
				msgs('修改失败',0,5);
				break;
		}
	},'json');
	return false;
});
$('.layui-back').click(function(){
	history.back();
});