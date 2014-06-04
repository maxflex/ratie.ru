	var messages = [
		"Скажи всё, что думаешь обо мне анонимно",
		"А что ты думаешь обо мне? Выскажись анонимно или публично",
	];
		
	$(document).ready(function(){	
		// Инициализация приложения ВК
		VK.init({
		  apiId: _vk_app_id
		});
	});
	
	// Предложить друзьям оценить
	function offerVote() {
		// Получаем случайное сообщение с предложением оценить
		random_message = messages[Math.floor(Math.random()*messages.length)];
		
		VK.Api.call('wall.post', {
			message: random_message + ": " + document.URL,
			attachments: document.URL
			
		}, function(r) {
			// Если пользователь поделился, увеличеваем кол-во shares
			if (typeof r.response != "undefined") {
				$.post("index.php?controller=user&action=AjaxAddShare");
			}
		});
	}