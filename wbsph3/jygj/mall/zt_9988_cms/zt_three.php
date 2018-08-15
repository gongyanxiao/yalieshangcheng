<?php
header("Content-type:text/html;charset=utf-8");
$i1=strip_tags(trim($_GET['id']));
?>
<table width="674" height="62" border="0" align="center" cellpadding="0" cellspacing="0">
  <form name="one" method="post" action="zt_sort3_add_pro.php?id=<?php echo $i1;?>">
  <tr>
    <td align="center" bgcolor="#eeeeee">
    <input name="three" type="text" id="lm" size="45" style="border:1px #CCCCCC solid;">
    <input type="submit" name="Submit" value="添加三级栏目" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;"></td>
  </tr>
  </form>