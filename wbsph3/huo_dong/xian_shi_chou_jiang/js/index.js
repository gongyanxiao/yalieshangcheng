var myNumber;
var arr = [];

document.getElementById("saveBtn").onclick = function(event) {
	event.preventDefault();
	var BB = self.Blob;
	saveAs(new BB([ "\ufeff" + document.getElementById("list").innerHTML ] // \ufeff防止utf8
																			// bom防止中文乱码
	, {
		type : "text/plain;charset=utf8"
	}), document.getElementById("filename").value);
};

/* 随机所有的code并且不重复 */
function showRandomNum(num) {

	var li = "";
	for (var i = 0; i < total.length; i++) {
		arr[i] = i;
	}
	arr.sort(function() {
		return 0.5 - Math.random();
	});

	for (var i = 0; i < num; i++) {
		var index = arr[i];
		li += '<li>' + total[index].name + '_' + total[index].phone + '</li>';
	}

	$(".prize_list ul").html(li);
}

$(function() {
	$(".start").click(function() {

		if ($("#prize_btn").val() == 0) {
			$("#prize_btn").val(1);

			$(this).find("img").attr("src", "images/prize_stop.png");
			if (num > total.length) {
				num = total.length;
			}
			myNumber = setInterval(function() {
				showRandomNum(num);
			}, 30);

		} else {
			$("#prize_btn").val(0);
			clearInterval(myNumber);
			$(this).find("img").attr("src", "images/prize_start.png");
			zhong_jiang();
		}
	});

	// 回车键控制开始和停止
	$(document).keydown(function(event) {
		var e = event || window.event || arguments.callee.caller.arguments[0];
		if (e && e.keyCode == 13) { // enter 键
			$(".start").click();
		}
	});

	$("#set_grade").change(function() {
		$(".prize_grade span").text($(this).val());
	});

	$("#prizeMoney").keyup(function() {
		$(".prize_grade i").text($(this).val());
	});
});