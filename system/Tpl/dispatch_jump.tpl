<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>跳转提示</title>
<style >


body{margin:0 auto;font-size:12px;color:#333;font-family:Arial "宋体";background:#ebebeb}
div,form,input,img,ul,li,dl,dd,dt{margin:0;padding:0;border:0}
img{vertical-align:top}
ul,dl{list-style-type:none}
h1,h2,h3,h4,h5,h6{margin:0;padding:0;border:0;font-size:12px;font-weight:normal}
a{text-decoration:none}

#container{width:605px;margin:0 auto;padding:10px 10px 0 10px;background:#fff;
list-style:none;border-left:2px #cacaca solid;border-right:2px #cacaca solid; border-bottom:2px #cacaca solid;border-top:2px #cacaca solid;overflow:hidden}


.juz ul{ width:605px; height:380px; margin-top:100px;}
.juz li{ float:left;}
.left{ width:120px; float:left; margin-left:30px;}
.right{ width:450px; margin-top:20px;}

#topBox{background:url(/Public/system/images/global.png) repeat-x; height:157px; zoom:1;}
.top{line-height:35px;height:35px;width:991px; margin:0 auto;position:relative ;}
.top a{}
.top-l{ float:left; height:20px; line-height:20px; overflow:hidden; width:300px; margin-top:9px;}
.top-l ul li{ padding-left:18px; float:left; display:inline; margin-right:10px;}
.top-l ul li a{color:#999; text-shadow:0px 1px 0px #fff;}
.top-l ul li a:hover{ color:#fb7b00;}
.top-l ul .syBtn{background:url(/Public/system/images/homeTb.png) no-repeat 0px 3px;}
.top-l ul .pl{ width:2px; height:12px; overflow:hidden; background: no-repeat; margin:4px 0px 0px 0px; padding:0px;}
.top-l ul .scBtn{background:no-repeat 0px 3px;margin-left:10px; color:#999;}




#footer{width:940px;margin:20px auto 10px auto;text-align:center;padding-top:10px;border-top:1px solid #c0c0c0}
#footer span{display:block;line-height:20px;height:20px;overflow:hidden;color:#ccc;font-size:12px}
#footer span a{color:#444}
#footer span a:hover{color:#d70000}
#footer span font{margin:0 10px}
#footer i{color:#898989;font-style:normal;line-height:28px;display:block;margin-top:8px;font-size:12px}



.cont{ margin:70px; text-align:center;}
#left{ float:left; margin-top:70px; margin-left:120px;}
.cent{ text-align:center; padding-top:100px; float:left;}
#span1{ float:right; background:url(/Public/system/images/31wan.jpg); background-position:bottom; margin-top:150px; margin-right:10px; width:160px; height:65px;}
.h1{ font-size:24px; color:#3f3f3f; width:480px; }
.h2{ color:#6f6f6f; font-size:12px; float:left;}

</style>

</head>
<body style="background:#fcfcfc;">

<div id="top" >
<div id="topBox">

<div class="top "style="background:url(/Public/system/images/global.png)">

<div class="top-l left" style=""><ul>
<li class="pl"></li><li class="scBtn">您好，欢迎来到网页游戏平台！</li>
</ul></div>

</div>

</div></div>

<br />

<div id="container">
 
   <div class="juz">
  
   <ul> <li>
   
   <div class="left">
    
           <present name="message">
<img src="/Public/system/images/dui.jpg" />
<else/>
 <img src="/Public/system/images/cuowu.jpg" />
</present>
      </div>
      
 </li>  <li>
 
 <div class="right">
   <span class="h1">
   <present name="message">
 <?php echo($message); ?>
<else/>
 <?php echo($error); ?> 
</present>
</span>
     <br />
    <p class="h2">页面自动<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a>，等待时间： <b id="wait"><?php echo($waitSecond); ?></b></p>
    
</li>
 
 </ul>
 <script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time == 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
 </div>
 
 </div>

      </div>
  
  
  </div>
  
  
</div>
<br />

<div id="footer">
<span><a href="/service">关于我们</a><font>|</font>
<a href="/service">商务合作</a>
<font>|</font><a href="/service">法律声明</a>
<font>|</font><a href="/service">客服中心</a><font>|</font>
<a href="/service">人才招聘</a><font>|</font>
<a href="http://bbs.7477.com">游戏论坛</a></span><i>游戏版权所有 Copyright (c) 2015 7477.com All Rights Reserved
<br/>粤ICP备14083534号 粤网文[2014]2170-469号 <br/>     
抵制不良游戏，拒绝盗版游戏。 注意自我保护，谨防受骗上当。 适度游戏益脑，沉迷游戏伤身。 合理安排时间，享受健康生活。</i></div>


</body></html>      
