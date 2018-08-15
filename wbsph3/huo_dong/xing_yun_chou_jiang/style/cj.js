

var nametxt = $('.name');
var phonetxt = $('.phone');
var pcount = total.length;//参加人数
var runing = true;
var td = 10;//共10个名额
var num = -1;//中奖人的索引
var t;
var is_kai_jiang = 0;

//开始停止
function start() {
	if(is_kai_jiang==1) return ;//开过奖后, 不能再点击
	
	if (runing) {
		runing = false;
		$('#btntxt').removeClass('start').addClass('stop');
		$('#btntxt').html('停止');
		if(td>0){
		 startNum();
		}
        else {
                alert("投票结束");
            }
	} else {
		is_kai_jiang = 1;
        runing = true;
//		$('#btntxt').removeClass('stop');//.addClass('start');
		$('#btntxt').html('已开奖');
		stop();
		zd();
	}
}

var interval = 500;
//循环参加名单
function startNum() {
	num = Math.floor(Math.random() * pcount);
	
	nametxt.html(total[num].name);
	phonetxt.html(total[num].phone);
	t = setTimeout(startNum, interval);
	interval = interval/1.2;
}
//停止跳动
function stop() {
	pcount = total.length-1;
	clearInterval(t);
	t = 0;
}

function zd() {
    if(td >0){
		//打印中奖者名单
		$('.list').prepend("<p>"+total[num].name+" -- "+total[num].phone+"</p>");
		  zhong_jiang(total[num].phone);
	      total.splice(num,1); //将已中奖者从数组中"删除",防止二次中奖	
			
		}else {
        alert("投票结束");
	}

td=td-1;
}
