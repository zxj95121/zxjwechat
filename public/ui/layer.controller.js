function confirms(content,istrue=function(){},isfalse=function(){},msgtime=0,trueicon=1,falseicon=1){
	msgtime=parseInt(msgtime);
	if(!(msgtime>0)){
		msgtime=2000;
	}
	if(typeof istrue!='function'){
		var turemsg=istrue;
		istrue=function(){layer.msg(turemsg, {icon:trueicon,time:msgtime});};
	}
	if(typeof isfalse!='function'){
		var falsemsg=isfalse;
		isfalse=function(){layer.msg(falsemsg, {icon:falseicon,time:msgtime});};
	}
	layer.confirm(content,{btn:['确定','取消']},istrue,isfalse);
}
function msgs(content=false,msgtime=0,icons=1,finished=function(){}){
	msgtime=parseInt(msgtime);
	if(!(msgtime>0)){
		msgtime=2000;
	}
	if(content){
		if(Array.isArray(content)){
			for(var i=0;i<content.length;i++){
				layer.msg(content[i], {icon:icons,time:msgtime},finished);
			}
		}else{
			layer.msg(content, {icon:icons,time:msgtime},finished);
		}
	}
}
function alerts(content,skins,truefunction=function(i){layer.close(i);}){
	layer.alert(content,{skin:skins},truefunction);
}

function showtag(tag,titles=false,shades=false,cancelfunction=function(){},width=false,height=false,skins=false){
	if(shades){
		shades=parseFloat(shades);
	}
	var showtagdata={
	  type: 1,
	  shade: shades,
	  title: titles, 
	  skin:skins,
	  content: tag, 
	  cancel: cancelfunction
	}
	if(width&&height){
		showtagdata['area']=[width,height];
	}
	return layer.open(showtagdata);
}
function tips(tag,content,colors='#000',areas="right",time=false){
	switch(areas){
		case 'left':areas=4;break;
		case 'bottom':areas=3;break;
		case 'right':areas=2;break;
		case 'top':areas=1;break;
	}
	var tipdata={tips: [areas,colors]};
	if(time){
		tipdata['time']=time;
	}
	layer.tips(content, $(tag),tipdata);
}
function maxwindow(openid){
	layer.full(openid);
}
function showpage(url,titles=false,width=true,height=false,scrolls="yes"){
	if(width&&height){
		max=false;
	}else{
		max=true;
		width="10px";
		height="10px";
	}
	var index =layer.open({
	  type: 2,
	  content:[url,scrolls],
	  area: [width,height],
	  title:titles,
	  // maxmin: true
	});
	if(max){
		maxwindow(index);
	}
}
function closes(closeid){
	layer.close(closeid);
}
function showloading(skin=0,shades=false,time=2000){
	var closeid = layer.load(skin, {shade: shades});
	if(time){
		setTimeout(function(){
			closes(closeid);
		},time);
	}
	return closeid;
}
function promptstart(titles=false,type="text",key=""){
	switch(type){
		case "text":type=0;break;
		case "password":type=1;break;
		case "textarea":type=2;break;
		case 0:type=0;break;
		case 2:type=2;break;
		case 1:type=1;break;
		case '1':type=1;break;
		case '2':type=2;break;
		case '0':type=0;break;
		default:type=0;
	}
	layer.prompt({title:titles,formType:type},function(data,closeid){
		sessionStorage.setItem("promptdata"+key,data);
		closes(closeid);
	});
}
function promptend(key=""){
	var promptdata=sessionStorage.getItem("promptdata"+key);
	sessionStorage.removeItem("promptdata"+key);
	return promptdata;
}
function tables(width,height){
	var tablesdata=[];
	for(var i=2;i<arguments.length;i++){
		tablesdata[i-2]={};
		tablesdata[i-2]['title']=arguments[i][0];
		tablesdata[i-2]['content']=arguments[i][1];
	}
	layer.tab({
	  area: [width,height],
	  tab: tablesdata
	});
}