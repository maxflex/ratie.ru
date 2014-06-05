	// Анимация появления страницы
	var _page_start_animation 				= "animated fadeInDown";
	var _page_additional_start_animation 	= "animated fadeInLeft";
	
	// Картинки в начале сообщения
	var _ALERT_CLEAR_PHOTO		=	"<img src='img/icon/64/paintbrush2.png'>";
	var _ALERT_CAUTION			=	"<img src='img/icon/64/caution.png'>";
	var _ALERT_CAMERA			=	"<img src='img/icon/64/camera.png'>";
	var _ALERT_POLAROID			=	"<img src='img/icon/64/polaroids.png'>";
	var _ALERT_SECURITY			=	"<img src='img/icon/64/security.png'>";
	var _ALERT_FRIENDS			=	"<img src='img/icon/64/friends.png'>";
	var _ALERT_MEGAPHONE		=	"<img src='img/icon/64/megaphone.png'>";
	var _ALERT_PROFILE			=	"<img src='img/icon/64/profile.png'>";
	var _ALERT_CHAT				=	"<img src='img/icon/64/chat.png'>";
	var _ALERT_ADJHIDE			=	"<img src='img/icon/64/hide_adj.png' style='height: 64px; width: 64px' class='pull-left'>";
	var _ALERT_CHAT_LEFT		=	"<img src='img/icon/64/chat.png' class='pull-left'>";
	
	$(document).ready(function() {
		// Анимируем новости на всех страницах
		news_count_top = $("#news-count-top");
		
		// Если есть новости
		if (news_count_top.length) {
			news_count_top.addClass("animated bounceIn");	
			console.log("here");
		}
	});
	
	// Анимация загрузки AJAX
	function ajaxStart() {
		$(".top-loader").css("display", "block");
	}
	
	
	// Конец анимации загрузки AJAX
	function ajaxEnd() {
		$(".top-loader").css("display", "none");	
	}
	
	// Первая буква большая
	function ucfirst(string)
	{
	    return string.charAt(0).toUpperCase() + (string.slice(1)).toLowerCase();
	}
	
	// Переход на страницу 
	function goTo(location) {
		window.location = location;
	}
	
	// Редирект с параметрами  (принимает массив, параметр => значение)
	function redirect(params) {
		url = "index.php?" + $.param(params);
		window.location = url;
	}
	
	// Переход назад
	function goBack()
	{
		history.back();
	}
	
	// Показыват ссылку для копирования
	function showVoteLink(element)
	{
		bootbox.alert(_ALERT_CHAT_LEFT + "По этой ссылке друзья могут анонимно сказать, что думают о тебе. Разместите ее в социальных сетях, чтобы дать им возможность высказаться:<br><br>" +
		"<input id='votelink' type='text' class='bootbox-input bootbox-input-text form-control' value='" + element.innerHTML + "'>");
		
		setTimeout(function() {
			$("#votelink").focus();	
		}, 500)		  
	}

	
