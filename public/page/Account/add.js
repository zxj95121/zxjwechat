$('#encodingaeskeyGen').click(function(){
	var tokenV=EncodingAESKeyGen();
	$("#encodingaeskey").val(tokenV);
});
$('#tokenGen').click(function(){
	var tokenV=tokenGen();
	$("#token").val(tokenV);
});
if($("#token").length>0&&$("#token").val()==''){
	(function(){
		var tokenV=tokenGen();
		$("#token").val(tokenV);
	})();
}
if($("#encodingaeskey").length>0&&$("#encodingaeskey").val()==''){
	(function(){
		var tokenV=EncodingAESKeyGen();
		$("#encodingaeskey").val(tokenV);
	})();
}
layui.form.on('submit(submit)', function(data){
	$.post('add_submit.html',data.field,function(callback){
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
