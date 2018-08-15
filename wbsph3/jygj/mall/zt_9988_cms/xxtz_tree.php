<?php 
include "../myphplib/db.php";
include "config/check.php";

/**
 * 获取用户的身份
 * @param unknown $level
 * @return string
 */
function  getShenFen($level){
    if($level==0){
        return "会员";
    }else if($level==1){
        return "初级代理";
    }else if($level==2){
        return "中级代理";
    }else if($level==3){
        return "高级代理";
    }else if($level==4){
        return "运营中心";
    }else if($level==5){
        return "懂事";
    }
}
 $sql="SELECT user_id as id ,user_type, real_name as name,   parent_id as pId , level, user_name   FROM `xbmall_users` ";
 $result=mysql_query($sql);
 while ( $row   = mysql_fetch_array( $result ) ) {
 	 $data['id']=intval($row['id']);
 	 $data['name']=$row['name'];
 	 $data['pId']=intval($row['pId']);
 	 $data['level']=$row['level'];
 	 $sql="SELECT count(0)  FROM `zt_xxtz` where user='".$row['user_name']."' ";
 	 $zuoDanShu = getOne($sql);
     $data['level']=getShenFen($data['level']);
     if(  $row['user_type']){
     	$data['name']=  $data['name']."(虚拟的".$data['level']."),做单数:".$zuoDanShu;
     }else {
         $data['name']=  $data['name']."(".$data['level']."),做单数:".$zuoDanShu;
     }
     $dataes[] = $data;
 }
 
 $json =json_encode($dataes);

?>
<!DOCTYPE html>
<HTML>
<HEAD>
	<TITLE> ZTREE </TITLE>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="/jygj/Public/js/ztree/css/demo.css" type="text/css">
	<link rel="stylesheet" href="/jygj/Public/js/ztree/css/zTreeStyle/zTreeStyle.css" type="text/css">
	<script type="text/javascript" src="/jygj/Public/home/js/jquery-1.7.2.js"></script>
	<script type="text/javascript" src="/jygj/Public/js/ztree/js/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="/jygj/Public/js/ztree/js/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="/jygj/Public/js/ztree/js/jquery.ztree.exedit.js"></script>
	
    <script type="text/javascript" src="/jygj/Public/js/layer/layer.js" ></script>

 
	
	
	<SCRIPT type="text/javascript">
 

		var IDMark_Switch = "_switch",
		IDMark_Icon = "_ico",
		IDMark_Span = "_span",
		IDMark_Input = "_input",
		IDMark_Check = "_check",
		IDMark_Edit = "_edit",
		IDMark_Remove = "_remove",
		IDMark_Ul = "_ul",
		IDMark_A = "_a";
		
		var setting = {
			view: {
				addHoverDom: addHoverDom,
				removeHoverDom: removeHoverDom,
				addDiyDom: addDiyDom
			},
    		data: {
                simpleData: {
                    enable: true
                }
            }
		};

		
		 
	 	    var zNodes =<?php echo $json;?>;
 
  
		function addHoverDom(treeId, treeNode) {
			
			var level=1;
			var temp = treeNode;
			while(temp.getParentNode()!=null){
				level++;
				temp = temp.getParentNode();
			}

			
			var aObj = $("#" + treeNode.tId + IDMark_A);
				if ($("#diyBtn_"+treeNode.id).length>0) return;
				var editStr = "<span style=\"padding-left:15px\" id='diyBtn_space_" +treeNode.id+ "' >当前是第"+level+"层</span><span   id='diyBtn_" +treeNode.id+ "' >去做单</span>";
				editStr =editStr+ "<a id='diyBtn1_" +treeNode.id+ "' href=\"shengJi.html?user="+treeNode.id+"\"   target=\"blank\">升级</a>";
				editStr =editStr+ "<span style='margin-left:10px' id='diyBtn2_" +treeNode.id+ "'>补单</span>";
				editStr =editStr+ "<span style='margin-left:10px' id='diyBtn3_" +treeNode.id+ "'>推荐人开店</span>";
				editStr =editStr+ "<span style='margin-left:10px' id='diyBtn4_" +treeNode.id+ "'>自己开店</span>";
				editStr =editStr+ "<span style='margin-left:10px' id='diyBtn5_" +treeNode.id+ "'>补提现记录</span>";
				aObj.append(editStr);
				var btn = $("#diyBtn_"+treeNode.id);
				if (btn) btn.bind("click", function(){quZuoDan(treeNode.id,treeNode.pass);});

				var buDanBtn = $("#diyBtn2_"+treeNode.id);
				if (buDanBtn) buDanBtn.bind("click", function(){buDan(treeNode.id,treeNode.name);});

				var shangCengKaiDianBtn = $("#diyBtn3_"+treeNode.id);
				if (shangCengKaiDianBtn) shangCengKaiDianBtn.bind("click", function(){shangCengKaiDian(treeNode.getParentNode().id,treeNode.getParentNode().name,treeNode.id,treeNode.name);});


				var shangCengKaiDianBtn = $("#diyBtn4_"+treeNode.id);
				if (shangCengKaiDianBtn) shangCengKaiDianBtn.bind("click", function(){ziJiKaiDian(treeNode.id,treeNode.name);});

				var buTiXianBtn = $("#diyBtn5_"+treeNode.id);
				if (buTiXianBtn) buTiXianBtn.bind("click", function(){buTiXian(treeNode.id,treeNode.name);});
				
			   
		}

		   

		function removeHoverDom(treeId, treeNode) {
			$("#diyBtn_space_"+treeNode.id).unbind().remove();
			$("#diyBtn_"+treeNode.id).unbind().remove();
			$("#diyBtn1_"+treeNode.id).unbind().remove();
			$("#diyBtn2_"+treeNode.id).unbind().remove();
			$("#diyBtn3_"+treeNode.id).unbind().remove();
			$("#diyBtn4_"+treeNode.id).unbind().remove();
			$("#diyBtn5_"+treeNode.id).unbind().remove();
// 			if (treeNode.parentTId && treeNode.getParentNode().id!=1) return;
// 			if (treeNode.id == 15) {
// 				$("#diyBtn1_"+treeNode.id).unbind().remove();
// 				$("#diyBtn2_"+treeNode.id).unbind().remove();
// 			} else {
// 				$("#diyBtn_"+treeNode.id).unbind().remove();
// 				$("#diyBtn_space_" +treeNode.id).unbind().remove();
// 			}
		}

		function addDiyDom(treeId, treeNode) {
// 			alert(treeNode.zi_ding_yi);
// 			if (treeNode.parentNode && treeNode.parentNode.id!=2) return;
// 			var aObj = $("#" + treeNode.tId + IDMark_A);
// 			if (treeNode.id == 21) {
// 				var editStr = "<span class='demoIcon' id='diyBtn_" +treeNode.id+ "' title='"+treeNode.name+"' onfocus='this.blur();'><span class='button icon01'></span></span>";
// 				aObj.append(editStr);
// 				var btn = $("#diyBtn_"+treeNode.id);
// 				if (btn) btn.bind("click", function(){alert("diy Button for " + treeNode.name);});
// 			} else if (treeNode.id == 22) {
// 				var editStr = "<span class='demoIcon' id='diyBtn_" +treeNode.id+ "' title='"+treeNode.name+"' onfocus='this.blur();'><span class='button icon02'></span></span>";
// 				aObj.after(editStr);
// 				var btn = $("#diyBtn_"+treeNode.id);
// 				if (btn) btn.bind("click", function(){alert("diy Button for " + treeNode.name);});
// 			} else if (treeNode.id == 23) {
// 				var editStr = "<select class='selDemo' id='diyBtn_" +treeNode.id+ "'><option value=1>1</option><option value=2>2</option><option value=3>3</option></select>";
// 				aObj.after(editStr);
// 				var btn = $("#diyBtn_"+treeNode.id);
// 				if (btn) btn.bind("change", function(){alert("diy Select value="+btn.attr("value")+" for " + treeNode.name);});
// 			} else if (treeNode.id == 24) {
// 				var editStr = "<span id='diyBtn_" +treeNode.id+ "'>Text Demo...</span>";
// 				aObj.after(editStr);
// 			} else if (treeNode.id == 25) {
// 				var editStr = "<a id='diyBtn1_" +treeNode.id+ "' onclick='alert(1);return false;'>链接1</a>" +
// 					"<a id='diyBtn2_" +treeNode.id+ "' onclick='alert(2);return false;'>链接2</a>";
// 				aObj.after(editStr);
// 			}
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
		
			var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
			var nodes = treeObj.getNodes();
			treeObj.expandAll(true);
			var nodeArr= treeObj.transformToArray(nodes);
		
// 			for(var i=0;i<nodeArr.length;i++)
// 			{
// 			newTree.selectNode(nodeArr[i],true);
// 			  MyNode=treeObj.getSelectedNodes();
// 			  str+="'"+MyNode[0].id+"',";
// 			}
		});

		 function  quZuoDan(user,password){
			
		   document.getElementById("user").value=user;
		   document.getElementById("form1").submit();
		 }

		
		 //给谁补多少钱的单
		 function  buDan(user,userName){
			 layer.open({
	             type: 1
	             , title: "补单" //
	             , area: ['40%', '40%']
	             , shade: 0.8
	             , id: 'LAY_layuipro' //设定一个id，防止重复弹出
	             , resize: true
	             , btn: ['进去看看（5秒）']
	             , btnAlign: 'r'
	             , moveType: 1 //拖拽模式，0或者1
	             , content: "<div style='width:350px;'>" +
	             "<div style='width:320px;margin-left: 3%;' ><p>请输入用户名</p><input id='buDanUser'  type='text' name='buDanUser' value='"+user+"'/>("+userName+")</div>"+
	             "<div style='width:320px;margin-left: 3%;' ><p>请输入做单金额</p><input id='buDanJE'  type='number' name='buDanJE' value='3600'/></div>"+
	             "<div style='width:320px;margin-left: 3%;' ><p>请输入做单时间</p><input id='tjrq'  type='text' name='tjrq' value='2017-11-20 09:09:09'/>" +
	             "<button style='margin-top:5%;' type='button'  onclick='updateAward()'>提交</button></div></div>"
	             , yes: function (layero) {
	                     layer.closeAll();
	             },  end: function () {
	            	   var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
	            	   var hoverNode = treeObj.getNodeByParam("id", user, null);
	            	   removeHoverDom("",hoverNode);
		            }
	         });

		 }

		 //上层给开店
		 function shangCengKaiDian(tuiJianRen,tuiJianRenName, user,userName){
			 layer.open({
	             type: 1
	             , title: "推荐人给开店" //
	             , area: ['40%', '40%']
	             , shade: 0.8
	             , id: 'LAY_layuipro2' //设定一个id，防止重复弹出
	             , resize: true
	             , btn: ['进去看看（5秒）']
	             , btnAlign: 'r'
	             , moveType: 1 //拖拽模式，0或者1
	             , content: "<div style='width:350px;'>" +
	             "<div style='width:320px;margin-left: 3%;'  ><p>推荐人</p><input id='tuiJianRen'  type='text' name='tuiJianRen' value='"+tuiJianRen+"'/>("+tuiJianRenName+")</div>"+
	             "<div style='width:320px;margin-left: 3%;'  ><p>开店人</p><input id='kaiDianUser'  type='text' name='user' value='"+user+"'/>("+userName+")</div>"+
	             "<div style='width:320px;margin-left: 3%;'  ><p>请输入做单时间</p><input id='tjrq'  type='text' name='tjrq' value='2017-11-20 09:09:09'/>" +
	             "<button style='margin-top:5%;' type='button'  onclick='shangCengKaiDianDo()'>给他开店</button></div></div>"
	             , yes: function (layero) {
	                     layer.closeAll();
	             },  end: function () {
	            	   var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
	            	   var hoverNode = treeObj.getNodeByParam("id", user, null);
	            	   removeHoverDom("",hoverNode);
		            }
	         });

		 }

		 function    shangCengKaiDianDo(){
			   var tuiJianRen = $("#tuiJianRen").val();
		       var dianPuUser = $("#kaiDianUser").val();//给谁开店铺
		       var tjrq = $("#tjrq").val();
		        $.ajax({
		            type: "post",
		            url :  "/jygj/mall/zt_9988_cms/xxtz_add.php",
		            dataType:'json',
		            data: {
		                "user":tuiJianRen,
		                "dianPuUser":dianPuUser,
		                "czje":3600,
		                "type":2,
		                "tjrq":tjrq
		            },
		            success: function(data){
			            alert(data.message);
		            }
		        });
		        layer.closeAll();
		 }

		 //自己给开店
		 function ziJiKaiDian(user,userName){
			 layer.open({
	             type: 1
	             , title: "自己给开店" //
	             , area: ['40%', '40%']
	             , shade: 0.8
	             , id: 'LAY_layuipro3' //设定一个id，防止重复弹出
	             , resize: true
	             , btn: ['进去看看（5秒）']
	             , btnAlign: 'r'
	             , moveType: 1 //拖拽模式，0或者1
	             , content: "<div style='width:350px;'>" +
	             "<div style='width:320px;margin-left: 3%;'  ><p>请输入用户名</p><input id='kaiDianUser'  type='text' name='user' value='"+user+"'/>("+userName+")</div>"+
	             "<div style='width:320px;margin-left: 3%;'  ><p>请输入做单时间</p><input id='tjrq'  type='text' name='tjrq' value='2017-11-20 09:09:09'/>" +
	             "<button style='margin-top:5%;' type='button'  onclick='ziJiKaiDianDo()'>自己开店</button></div></div>"
	             , yes: function (layero) {
	                     layer.closeAll();
	             },  end: function () {
	            	   var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
	            	   var hoverNode = treeObj.getNodeByParam("id", user, null);
	            	   removeHoverDom("",hoverNode);
		            }
	         });

		 }
		 function    ziJiKaiDianDo(){
		       var dianPuUser = $("#kaiDianUser").val();//给谁开店铺
		       var tjrq = $("#tjrq").val();
		        $.ajax({
		            type: "post",
		            url :  "/jygj/mall/zt_9988_cms/xxtz_add.php",
		            dataType:'json',
		            data: {
		                "user":dianPuUser,
		                "czje":3600,
		                "type":2,
		                "tjrq":tjrq
		            },
		            success: function(data){
			            alert(data.message);
		            }
		        });
		        layer.closeAll();
		 }
		 
		 //补开提现记录
		 function buTiXian(user,userName){
			 layer.open({
	             type: 1
	             , title: "补开提现记录" //
	             , area: ['40%', '40%']
	             , shade: 0.8
	             , id: 'LAY_layuipro4' //设定一个id，防止重复弹出
	             , resize: true
	             , btn: ['进去看看（5秒）']
	             , btnAlign: 'r'
	             , moveType: 1 //拖拽模式，0或者1
	             , content: "<div style='width:350px;'>" +
	             "<div style='width:320px;margin-left: 3%;'  ><p>请输入用户名</p><input id='tiXianUser'  type='text' name='tiXianUser' value='"+user+"'/>("+userName+")</div>"+
	             "<div style='width:320px;margin-left: 3%;'  ><p>请输入提现时间</p><input id='tjrq'  type='text' name='tjrq' value='2017-11-20 09:09:09'/>" +
	             "<div style='width:320px;margin-left: 3%;' ><p> 请输入提现金额</p><input id='tiXianJE'  type='number' name='tiXianJE' value='2592'/></div>"+
	             "<button style='margin-top:5%;' type='button'  onclick='ziJiKaiDianDo()'>提现</button></div></div>"
	             , yes: function (layero) {
	                     layer.closeAll();
	             },  end: function () {
	            	   var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
	            	   var hoverNode = treeObj.getNodeByParam("id", user, null);
	            	   removeHoverDom("",hoverNode);
		            }
	         });

		 }
		 function    buTiXianDo(){
		       var tiXianUser = $("#tiXianUser").val();//给谁开店铺
		       var tiXianJE = $("#tiXianJE").val();//提现金额
		       var tjrq = $("#tjrq").val();
		        $.ajax({
		            type: "post",
		            url :  "/jygj/mall/zt_9988_cms/xxtz_add.php",
		            dataType:'json',
		            data: {
		                "user":tiXianUser,
		                "money":tiXianJE,
		                "tjrq":tjrq
		            },
		            success: function(data){
			            alert(data.message);
		            }
		        });
		        layer.closeAll();
		 }


		 

		 function updateAward(){
			    var buDanJE = $("#buDanJE").val();
		        var buDanUser = $("#buDanUser").val();
		        var tjrq = $("#tjrq").val();

			    if(buDanJE%3600!=0){
					alert("做单金额必须是3600的整数倍");
					return ;
			    }
		        $.ajax({
		            type: "post",
		            url :  "/jygj/mall/zt_9988_cms/xxtz_add.php",
		            dataType:'json',
		            data: {
		                "user":buDanUser,
		                "czje":buDanJE,
		                "tjrq":tjrq
		            },
		            success: function(data){
			            alert(data.message);
		            }
		        });
		        layer.closeAll();
			}

		 
		
	</SCRIPT>
	<style type="text/css">
.ztree li span.demoIcon{padding:0 2px 0 10px;}
.ztree li span.button.icon01{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/3.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.icon02{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/4.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.icon03{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/5.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.icon04{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/6.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.icon05{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/7.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.icon06{margin:0; background: url(/Public/js/ztree/css/zTreeStyle/img/diy/8.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
	</style>
 </HEAD>

<BODY>
<h1>人员展示和测试</h1>
 
 <ul id="treeDemo" class="ztree" style="width:100%;height:auto"></ul>
 
</BODY>
</HTML>