function tokenGen() {
	var letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	var token = '';
	for(var i = 0; i < 32; i++) {
		var j = parseInt(Math.random() * (31 + 1));
		token += letters[j];
	}
	return token;
}
function EncodingAESKeyGen() {
	var letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	var token = '';
	for(var i = 0; i < 43; i++) {
		var j = parseInt(Math.random() * 61 + 1);
		token += letters[j];
	}
	return token;
}
$('.layui-back').click(function(){
	history.back();
});