$(function() {
	var windowwidth = $(window).width(); 
	$('.banner').width(windowwidth);
	$('.banner').height(windowwidth*600/1920);
	$('.banner ul li').width(windowwidth);
	$('.banner ul li a').width(windowwidth);
	$('.banner ul li a img').width(windowwidth);
	$(".nav").css('top',windowwidth*600/1920);
	
	
	var index=0, picTimer=null;
	var len = $('.banner li').length;
	var sWidth = $('.banner').width();
	var len = $('.banner').find("ul li").length;
	$('.banner').find("ul").css("width",sWidth * (len));
	$('.banner .b_num span').mouseover(function() {
		index = $(this).index();
		showPics(index);
	}).eq(0).trigger("mouseover");

	$('.banner').hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			index++;
            if(index==len)index=0;
            showPics(index); 
		},4000);
	}).trigger("mouseleave");
	
	function showPics(index) {
		$('.banner li').stop(true,false).fadeOut('fast').eq(index).fadeIn('fast');
		$('.banner .b_num span').removeClass("on").eq(index).addClass("on");
		 
	}

	slide(".apart_scroll",".apart_num","4000");

	$('.case_inner li').hover(function() {
		$(this).find('.case_hide').stop(true,false).slideDown();
	}, function() {
		$(this).find('.case_hide').stop(true,false).slideUp();
	});

	// 返回顶部
	$('.subnav_top').click(function() {
		$('html,body').animate({scrollTop: 0}, 200);
	});
})

function slide(wrap,num,speed){
	var sWidth = $(wrap).find("ul").width();
	var len = $(wrap).find("ul li").length;
	var index = 0;
	var picTimer;
	
	var btn = "<div class='apart_num'>";

	btn += "<div class='prev'></div><div class='next'></div></div>";
	$(wrap).append(btn);

	$(wrap).find(".prev").click(function() {
		index -= 1;
		if(index == -1) {index = len - 1;}
		showPics(index);
	});

	$(wrap).find(".next").click(function() {
		index += 1;
		if(index == len) {index = 0;}
		showPics(index);
	});

	$(wrap).find("ul").css("width",sWidth * (len));

	$(wrap).hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			index++;
            if(index==len)index=0;
            showPics(index); 
		},speed);
	}).trigger("mouseleave");
	
	function showPics(index) {
		var nowLeft = -index*sWidth;
		$('.apart_num span').text((index+1));
		$(wrap).find("ul").stop(true,false).animate({"left":nowLeft},300);
	}

};


















