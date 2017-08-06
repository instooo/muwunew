<include file="./Tpl/Template_7478/header.html" />

<style>
#container{width:605px;margin:0 auto;padding:10px 10px 0 10px;background:#fff;
list-style:none;border-left:2px #cacaca solid;border-right:2px #cacaca solid; border-bottom:2px #cacaca solid;border-top:2px #cacaca solid;overflow:hidden}
.juz ul{ width:605px; height:380px; margin-top:100px;}
.juz li{ float:left;}
.left{ width:120px; float:left; margin-left:30px;}
.right{ width:450px; margin-top:20px;}
.h1{ font-size:24px; color:#3f3f3f; width:480px; }
.h2{ color:#6f6f6f; font-size:12px; float:left;}

</style>

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
<a href="http://bbs.7477.com/">游戏论坛</a></span><i>游戏版权所有 Copyright 2015 7477.com 
<br/>粤ICP备14083534号 粤网文[2014]2170-469号 <br/>
抵制不良游戏，拒绝盗版游戏。 注意自我保护，谨防受骗上当。 适度游戏益脑，沉迷游戏伤身。 合理安排时间，享受健康生活。</i></div>


</body></html>
