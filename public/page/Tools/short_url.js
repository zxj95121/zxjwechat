layui.form.on('submit(submit)', function(data){
	$.post('short_url_submit.html',data.field,function(callback){
		switch(parseInt(callback.status)){
			case 1:
				var data=callback.data;
				$(":input[name=shorturl]").val(data);
				msgs('转换成功',0,1,function(){});
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
				msgs('转换失败',0,5);
				break;
		}
	},'json');
	return false;
});
$(".layui-back").click(function(){
	var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
	parent.Win10._closeWin(index); //再执行关闭 
});