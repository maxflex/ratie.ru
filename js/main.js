$(document).ready(function() {
	/***** Переменные ******/
	imac 	= $("#imac");
	iphone 	= $("#iphone");
	
	imac_in_animation 	= "bounceInRight";
	imac_out_animation 	= "bounceOutUp";
									
	iphone_in_animation = "bounceInRight";
	iphone_out_animation= "bounceOutUp";
	
	slide_in			= "fadeInDown";
	slide_out			= "fadeOutDown";
	
	// Количество слайдов
	slides				= $("div[id^='slide-']").length;
	
	animationTime	= 7000;
	slideTime		= 5500;
	/**********************/
	
	
	/* НАЧАЛЬНЫЕ АНИМАЦИИ */
	
	$(".logo").addClass("animated fadeInDown");	
	
	$("#slide-1").css({"display" : "block"});
	$("#slide-1").addClass("bounceInLeft").one('webkitAnimationEnd mozAnimationEnd oAnimationEnd animationEnd', function(){
		$(this).removeClass("bounceInLeft");
		current_slide_number = 1;
		
		/* АНИМАЦИЯ ОПИСАНИЙ К САЙТУ (СЛАЙДОВ)*/
		setInterval(function() {
			// Получаем ID следующего слайда
			next_slide_number = (current_slide_number == slides ? 1 : current_slide_number +1);
						
			// Находим элементы
			current_slide	= $("#slide-" + current_slide_number);
			next_slide		= $("#slide-" + next_slide_number);
			
			// Анимация
			// current_slide.removeClass(slide_in);
			current_slide.addClass(slide_out).one('webkitAnimationEnd mozAnimationEnd oAnimationEnd animationEnd', function(){
				$(this).removeClass(slide_in);
			});
			
			// Проверяем видимость
			if ((next_slide).is(":visible") == false) {
				next_slide.css("display", "block");
			}
			
			
			next_slide.removeClass(slide_out);
			next_slide.addClass(slide_in);

			// Увеличиваем текущий слайд
			current_slide_number = next_slide_number;
			
		}, slideTime);
	});
		
	$(".login-button").addClass("animated fadeInUp");
	
	
	/* АНИМАЦИЯ МАКА И АФЙОНА*/
	
	imac.addClass(imac_in_animation);
	
	setTimeout(function() {
		iphone.css({"display" : "block"});
	}, animationTime);
	
	setInterval(function() {
		if (imac.hasClass(imac_in_animation)) {
			
			imac.removeClass(imac_in_animation);
			iphone.removeClass(iphone_out_animation);
			
			imac.addClass(imac_out_animation);
			
			iphone.addClass(iphone_in_animation);
			//console.log("iphone");
		} else {
			iphone.removeClass(iphone_in_animation);
			imac.removeClass(imac_out_animation);
			iphone.addClass(iphone_out_animation);
			
			imac.addClass(imac_in_animation);			
			//console.log("imac");
		}
	}, animationTime);
});