$(".openUrl").click(function(){
    var url=$(this).attr("win-url");
    var title=$(this).attr("win-title");
    if(title){
    	title="<img class='icon' src='/ui/win-ui/img/icon/win10.png'/>"+title;
    }
    if(url){
        Win10.openUrl(url,title);
    }
});
// $(".closeWindow").click(function(){
//     Win10.exit();
// });
$(".win-event").click(function(event) {
	eval($(this).attr('win-event'));
});
