<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>信息管理系统</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
body,td,th {
	font-family: 宋体;
	font-size: 10px;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style></head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="97" background="images/top_bg.jpg"><table width="964" height="85" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="392" height="85" valign="middle"><table width="547" height="59" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="59"><img src="images/logo.jpg"></td>
            </tr>
          </table>
          <table width="479" height="21" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td width="479" align="left" style="color:#FFFFFF;"> 
			 
			    <?php
                include_once "../config/zt_config.php";
		     if(isset($_COOKIE['zt_user2'])){
	
      $db=mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
$u=$_COOKIE['zt_user2'];
$query1="select * from  xbmall_users where user_name='$u'";
$re1=mysql_query($query1,$db);
$p=mysql_fetch_assoc($re1);
echo "<font color='#ffffff' size='2px;' face='Arial black'>".$p['xm']."|角色:".$_COOKIE['js'].",</font>";
    	  }else{
	  echo "您尚示登录本系统";
	  }
?>
        
     <script>   
  var   CalendarData=new   Array(20);   
  var   madd=new   Array(12);   
  var   TheDate=new   Date();   
  var   tgString="甲乙丙丁戊己庚辛壬癸";   
  var   dzString="子丑寅卯辰巳午未申酉戌亥";   
  var   numString="一二三四五六七八九十";   
  var   monString="正二三四五六七八九十冬腊";   
  var   weekString="日一二三四五六";   
  var   sx="鼠牛虎兔龙蛇马羊猴鸡狗猪";   
  var   cYear;   
  var   cMonth;   
  var   cDay;   
  var   cHour;   
  var   cDateString;   
  var   DateString;   
  var   Browser=navigator.appName;   
    
  function   init()   
  {     
      CalendarData[0]=0x41A95;   
      CalendarData[1]=0xD4A;   
      CalendarData[2]=0xDA5;   
      CalendarData[3]=0x20B55;   
      CalendarData[4]=0x56A;   
      CalendarData[5]=0x7155B;   
      CalendarData[6]=0x25D;   
      CalendarData[7]=0x92D;   
      CalendarData[8]=0x5192B;   
      CalendarData[9]=0xA95;   
      CalendarData[10]=0xB4A;   
      CalendarData[11]=0x416AA;   
      CalendarData[12]=0xAD5;   
      CalendarData[13]=0x90AB5;   
      CalendarData[14]=0x4BA;   
      CalendarData[15]=0xA5B;   
      CalendarData[16]=0x60A57;   
      CalendarData[17]=0x52B;   
      CalendarData[18]=0xA93;   
      CalendarData[19]=0x40E95;   
      madd[0]=0;   
      madd[1]=31;   
      madd[2]=59;   
      madd[3]=90;   
      madd[4]=120;   
      madd[5]=151;   
      madd[6]=181;   
      madd[7]=212;   
      madd[8]=243;   
      madd[9]=273;   
      madd[10]=304;   
      madd[11]=334;   
    }   
    
  function   GetBit(m,n)   
  {     
        return   (m>>n)&1;   
  }   
    
  function   e2c()   
  {       
      var   total,m,n,k;   
      var   isEnd=false;   
      var   tmp=TheDate.getYear();   
      if   (tmp<1900)     tmp+=1900;   
      total=(tmp-2001)*365   
          +Math.floor((tmp-2001)/4)   
          +madd[TheDate.getMonth()]   
          +TheDate.getDate()   
          -23;   
      if   (TheDate.getYear()%4==0&&TheDate.getMonth()>1)   
          total++;   
      for(m=0;;m++)   
      {       
          k=(CalendarData[m]<0xfff)?11:12;   
          for(n=k;n>=0;n--)   
          {   
              if(total<=29+GetBit(CalendarData[m],n))   
              {     
                  isEnd=true;   
                  break;   
              }   
              total=total-29-GetBit(CalendarData[m],n);   
          }   
          if(isEnd)break;   
      }   
      cYear=2001   +   m;   
      cMonth=k-n+1;   
      cDay=total;   
      if(k==12)   
      {   
          if(cMonth==Math.floor(CalendarData[m]/0x10000)+1)   
              cMonth=1-cMonth;   
          if(cMonth>Math.floor(CalendarData[m]/0x10000)+1)   
              cMonth--;   
      }   
      cHour=Math.floor((TheDate.getHours()+3)/2);   
  }   
    
  function   GetcDateString()   
  {   var   tmp="";   
      tmp+=tgString.charAt((cYear-4)%10);       //年干   
      tmp+=dzString.charAt((cYear-4)%12);       //年支   
      tmp+="年(";   
      tmp+=sx.charAt((cYear-4)%12);   
      tmp+=")   ";   
      if(cMonth<1)   
      {     
        tmp+="闰";   
          tmp+=monString.charAt(-cMonth-1);   
      }   
      else   
          tmp+=monString.charAt(cMonth-1);   
      tmp+="月";   
      tmp+=(cDay<11)?"初":((cDay<20)?"十":((cDay<30)?"廿":"卅"));   
      if(cDay%10!=0||cDay==10)   
          tmp+=numString.charAt((cDay-1)%10);   
      tmp+="    ";   
      if(cHour==13)tmp+="夜";   
          tmp+=dzString.charAt((cHour-1)%12);   
      tmp+="时";   
      cDateString=tmp;   
      return   tmp;   
  }   
    
  function   GetDateString()   
  {     
      var   tmp="";   
      var   t1=TheDate.getYear();   
      if   (t1<1900)t1+=1900;   
      tmp+=t1   
                +"年"   
                +(TheDate.getMonth()+1)+"月"   
                +TheDate.getDate()+"日   "   
                +TheDate.getHours()+":"   
                +((TheDate.getMinutes()<10)?"0":"")   
                +TheDate.getMinutes() 
                +"   星期"+weekString.charAt(TheDate.getDay());     
      DateString=tmp;   
      return   tmp;   
  }   
    
  init();   
  e2c();   
  GetDateString();   
  GetcDateString();   
  document.write(DateString,"",cDateString);   
</script>			    </td>
            </tr>
          </table></td>
        <td width="572"><img src="images/top_right.jpg" width="680" height="100" border="0" usemap="#Map" /></td>
      </tr>
    </table></td>
  </tr>
</table>

<map name="Map" id="Map">
<area shape="rect" coords="211,69,338,97" href="sys_logout.php" target="_parent" />
<area shape="rect" coords="345,68,467,98" href="pass_mod.php" target="mainFrame" />
</map></body>
</html>
