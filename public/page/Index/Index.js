Win10.onReady(function () {
    //设置壁纸
    Win10.setBgUrl({
        main: '/ui/win-ui/img/wallpapers/main.jpg',
        mobile: '/ui/win-ui/img/wallpapers/mobile.jpg',
    });
    Win10.setAnimated([
        'animated flip',
        'animated bounceIn',
    ], 0.01);
});
function chooseAccount(){
	Win10.closeAll();
	Win10.openUrl("/windows/Account/choose_account.html","<img class='icon' src='/ui/win-ui/img/icon/win10.png'/>选择公众号");
}
$.post('/windows/Account/this_account.html', {}, function(callback) {
    switch(parseInt(callback.status)){
        case 1:
            $('#change-account').text(callback.data);
        break;
        case -1:
            msgs('登录失效',0,5,function(){
                top.location.href="/windows/Admin/login";
            });
            break;
        default:
            $('#change-account').text('未选择公众号');
            break;
    }
});