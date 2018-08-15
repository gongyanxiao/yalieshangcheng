/*其他的工具栏配置选项自定义即可，编辑器实例化的时候使用 “toolbars:变量名” 即可，比如toolbars:Basic*/
/*最原始的配置
var Default=[['fullscreen', 'source', '|', 'undo', 'redo', '|','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|','rowspacingtop', 'rowspacingbottom', 'lineheight', '|','customstyle', 'paragraph', 'fontfamily', 'fontsize', '|','directionalityltr', 'directionalityrtl', 'indent', '|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|','link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|','simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|','horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|','inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|','print', 'preview', 'searchreplace', 'help', 'drafts']];
*/
/*以下配置我去除了一些无用的功能 比如谷歌地图国内打不开，百度APP等*/
var Default=[['fullscreen','source','|','undo','redo','|','bold','italic','underline','fontborder','strikethrough','superscript','subscript','removeformat','formatmatch','autotypeset','blockquote','pasteplain','|','forecolor','backcolor','insertorderedlist','insertunorderedlist','selectall','cleardoc','|','rowspacingtop','rowspacingbottom','lineheight','|','customstyle','paragraph','fontfamily','fontsize','|','directionalityltr','directionalityrtl','indent','|','justifyleft','justifycenter','justifyright','justifyjustify','|','touppercase','tolowercase','|','link','unlink','anchor','|','imagenone','imageleft','imageright','imagecenter','|','simpleupload','insertimage','emotion','scrawl','insertvideo', 'music','attachment','map','insertframe','insertcode','pagebreak','template','background','|','horizontal','date','time','spechars','snapscreen','wordimage','|','inserttable','deletetable','insertparagraphbeforetable','insertrow','deleterow','insertcol','deletecol','mergecells','mergeright','mergedown','splittocells','splittorows','splittocols','charts','|','print','preview','searchreplace','help','drafts']],/*Basic为测试简版，自己添加需要的即可*/Basic=[['fullscreen','source','undo','redo','|','bold','italic','underline','fontborder','strikethrough','superscript','subscript','|','removeformat','formatmatch','autotypeset','blockquote','pasteplain','|','forecolor','backcolor','insertorderedlist','insertunorderedlist','|','link','unlink','anchor','|','selectall','cleardoc','|','simpleupload','insertimage','attachment']];



/*********************************帝国CMS附件管理***************************************/
/*暂时未做此文件，使用的是官方默认的，有点小BUG，但是不影响大的使用，后期开发，后台更改路径的话，下面也做响应的修改*/
if(qiantai==0){//判断是后台
UE.registerUI('FileMain',function(editor,uiName){
var btn=new UE.ui.Button({name:'FileMain',title:'网站附件管理',cssRules:'background-position:-500px 0;',/*需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon*/
onclick:function(){
window.open('/e/admin/ecmseditor/FileMain.php?type=1&classid='+classid+'&infoid='+infoid+'&filepass='+filepass+'&sinfo=1&doing=1'+ehash,'','width=700,height=550,scrollbars=yes');
}});/*执行*/
return btn;}/*index 指定添加到工具栏上的那个位置，默认时追加到最后,editorId 指定这个UI是那个编辑器实例上的，默认是页面上所有的编辑器都会添加这个按钮*/
);
}else if(qiantai==1){//判断是前台

}