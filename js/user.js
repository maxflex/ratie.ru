$(document).ready(function(){
	$("#adjective").fancyInput();
	$("#user-page").addClass(_page_start_animation);
});

// Подключаем модуль NG-Animate к приложению UserPage
angular.module('UserPage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function UserCtrl($scope) {
		// Для дополнительной сортировки (каждому новому поднятию в списке $scope.order++, чтоб всегда новое было вверху)
		$scope.order = 1;
		
		// Для плавной загрузки баров, сначала у них ширина 0%, после загрузки – настоящая
		angular.element(document).ready(function(){
			// Старые проценты
			var old_percent = Array();
			
			// Сохраняем старые проценты и присваим им изначально 0
			angular.forEach($scope.adjectives, function(adj){
				old_percent.push(adj._ang_pos_percent);
				adj._ang_pos_percent = 0;
			});
			
			// Применяем изменения
			$scope.$apply();
			
			// Через 200 миллесекунд устанавливаем настоящие проценты опять
			setTimeout(function(){
				angular.forEach($scope.adjectives, function(adj, index){
					adj._ang_pos_percent = old_percent[index];
				});
				
				$scope.$apply();
			}, 200);	
			
			// Получаем хеш, чтобы поднимать в списке конкретное прилагательное через url#id_adjective
			hash = window.location.hash.substring(1);
			
			// Если хэш получен
			if (hash.length !== 0) {
				// Искомое прилагательное – это ID который был передан в хэш
				sought_adj = $.grep($scope.adjectives, function(adjective) {
					return adjective.id == hash;
				});
				
				// Если прилагательное найдено
				if (sought_adj.length !== 0) {
					// Поднимаем прилагательное в списке;
					sought_adj[0]._ang_new_order = $scope.order++;
					
					// Если это прилагательное скрыто
					if (sought_adj[0].hidden) {
						$scope.showHidden(); // То отображаем скрытые 
					}
					
					// Применяем изменения
					$scope.$apply();
				}
			}
		});
		
		// По ходу печати пишет прилагательное сверху
		$scope.updateHello = function() {
			$scope.adjective	= angular.lowercase($scope.adjective);
			
			// Подсчитываем кол-во пробелов
			if ($scope.adjective) {
				$scope.whitespace_count = $scope.adjective.split(" ").length - 1;
			}
			
			// Если пробелов больше двух – то ошибка (нельзя больше двух слов)
			$scope.many_words = $scope.whitespace_count >= 2;
			
			/*			
			$scope.already_added = false;	// Флаг, что такое прилагательное уже было добавлено

			// Если есть прилагательное и длинна его меньше 15, то проверяем было ли оно добавлено ранее
			if ($scope.adjective && $scope.adjective.length <= 15) {
				$scope.adjectives.forEach(function(adj){			
					if (angular.lowercase(adj._ang_adjective) == $scope.adjective) {	// Сравниваем прилагательное 
						$scope.already_added = true;	// Ставим флаг, что такое прилагательное уже было добавлено
						return;
					}
				});
			}*/
		};
		
		// Удалить буквы из инпута, фэнси-инпута и очистить прилагательное в SCOPE
		$scope.clearInput = function() {
			// Удаляем буквы из инпута
			$(".adjective-div").children("div").children("span").remove();	// Из фэнси-инпута
			$("#adjective").text("");										// Из обычного инпута
			
			// Удаляем буквы сверху (из $scope)
			$scope.adjective = "";
		}
		
		
		// Убрать прилагательное из видимости
		$scope.hide = function(adj) {
			adj.hidden = !adj.hidden;
			console.log(adj.id);
			$.post("?controller=user&action=AjaxHide", {"id" : adj.id});
		}
		
		// Сообщение о том, что необходимо войти
		$scope.notLoggedIn = function() {
			bootbox.alert(_ALERT_CAUTION + "<a href='login'>Войдите</a>, чтобы подписаться на пользователя");
		}
		
		// Добавить мысль о человеке
		$scope.think = function(){			
			// Если нужно показывать
			if ($scope.intro_message) {
				
				message = "<span style='font-family: RaleWayMedium'><center>";
				
				switch ($scope.intro_message) {
					case 1: {
						message += _ALERT_MEGAPHONE + "Мнение будет оставлено публично";
						
						message += "</center></span><hr>" + $scope.user_name + " будет знать, что проголосовали именно Вы."
						+ "<br><br> Но можно <a href='profile/edit' target='_blank'>включить анонимность</a>"
						+ " в настройках и оставаться незамеченным!";
						
						break;
					}
					case 2: {
						message += _ALERT_PROFILE + " Мнение будет оставлено анонимно";
						
						message += "</center></span><hr>" + $scope.user_name + " <span style='font-family: RaleWayMedium'>не</span> увидит автора этого мнения."
						+ "<br><br> Можно <a href='profile/edit' target='_blank'>выключить анонимность</a>"
						+ " и пользователи будут видеть, что голосуете именно Вы!";
						
						break;
					}
				}
				
				bootbox.confirm(message, function(ans) {
					if (ans === true) {
						$scope.submitThought();
					}
				});
			} else {
				$scope.submitThought();
			}
		}
		
		// Оставить мысль
		$scope.submitThought = function() {
			// Перед добавлением в список проверяем было ли добавлено такое прилагательное ранее
			result = $.grep($scope.adjectives, function(e){
				return (angular.lowercase(e._ang_adjective) == $scope.adjective);
			});
			
			// Если прилагательное НЕ было добавлено ранее
			if (result.length == 0) {
				
				// Анимация загрузки
				ajaxStart();
				// Пост-запрос
				$.post("?controller=user&action=AjaxAddThought", {"adjective" : $scope.adjective})
					.success(function(response) {
						// Завершение анимации
						ajaxEnd();
						
						// console.log(response);
						
						// В ответе должен быть ID только что добавле
						if ($.isNumeric(response) == false) {
							bootbox.alert(_ALERT_CAUTION + response);
						} else {
							// Если добавляем в первый раз
							if (!$scope.adjectives) {
								// Инициализируем объект
								$scope.adjectives = [];
							}
							
							// Добавляем в список
							$scope.adjectives.push({
								"_ang_adjective"	: ucfirst($scope.adjective),
								"_ang_pos"			: "voted",
								"_ang_pos_count"	: 1,
								"_ang_neg"			: "",
								"_ang_neg_count"	: 0,
								"_ang_pos_percent"	: 100,
								"_ang_new_order"	: $scope.order++,
								"_ang_order"		: 1,
								"_ang_comment_count": 0,
								"id"				: response
							});
						
							// Очищаем инпуты
							$scope.clearInput();
							
							// Применяем изменения к $scope
							$scope.$apply();								
						}		
					})
					.error(function(){
						// Завершение анимации
						ajaxEnd();
						bootbox.alert(_ALERT_CAUTION + "Произошла ошибка! Попробуйте позже");
					});
				
				// Иначе оставляем голос за уже существующее прилагательное
				} else {					
					// Поднимаем прилагательное в списке
					result[0]._ang_new_order = $scope.order++;
					
					// Если за прилагательное еще не голосовалось, то голосуем
					if (!result[0]._ang_pos) {
						$scope.vote(result[0], 1);	// Голосуем ЗА
					}
					
					$scope.clearInput();		
				}
		}
		
		// Функция голосования
		$scope.vote = function(adj, type) {
			// Голос за
			if (type == 1) {
				// Если не проголосовали за
				if (!adj._ang_pos) {
					// Проверяем был ли голос против, если да – убираем
					if (adj._ang_neg) {
						adj._ang_neg = "";
						adj._ang_neg_count--;
					}
					
					adj._ang_pos = "voted";
					adj._ang_pos_count++;
				} else {
					adj._ang_pos = "";
					adj._ang_pos_count--;
				}				
			} else { // Иначе голос против («иначе» с ударением на "и")
				// Если не проголосовали против
				if (!adj._ang_neg) {
					// Проверяем был ли голос за, если да – убираем
					if (adj._ang_pos) {
						adj._ang_pos = "";
						adj._ang_pos_count--;
					}
					
					adj._ang_neg = "voted";
					adj._ang_neg_count++;
				} else {
					adj._ang_neg = "";
					adj._ang_neg_count--;
				}
			}

			// Обновляем бар
			$scope.updateBarAndVote(adj, type);
			
			// adj._ang_pos = !(!type || adj._ang_pos) ? "voted" : ""; // КОИМПЛИКАЦИЯ!!! Впервые в жизни пригодилось, епта!! – не, нахуй не нужна...
		}
		
		// Обновить бар и проголосовать
		$scope.updateBarAndVote = function(adj, type) {
			// Отправка аякса на голосование
			$scope.ajaxVote(adj.id, type);
						
			total = adj._ang_pos_count + adj._ang_neg_count;
			
			new_width = adj._ang_pos_count * 100 / total;
						
			adj._ang_pos_percent = new_width;					
		}
		
		// Проголосовать (аякс)
		$scope.ajaxVote = function(id, type) {
			$.post("?controller=user&action=AjaxVote", {"id" : id, "type" : type})
				.success(function(response) {
					// Завершение анимации
					// ajaxEnd();
					console.log(response);
					return true;
				})
				.error(function(){
					// ajaxEnd();
					bootbox.alert(_ALERT_CAUTION + "<b>Произошла ошибка! Голос не учтен.</b><br>Обновите страницу и попробуйте еще раз");
					
					return false;
				});
		}
		
		// Подписаться/отписаться
		$scope.subscribe = function(id_user) {
			$scope.subscribed = !$scope.subscribed;
			$.post("?controller=user&action=AjaxSubscribe", {"id_user" : id_user})
				.success(function(resp){
				//	 console.log(resp);
				});
		}
		
		// Показать скрытые мнения
		$scope.showHidden = function() {
			// Если показываем скрытые – прокручиваем к ним
			if (!$scope.show_hidden) {
				$('html, body').animate({
			        scrollTop: $("#hidden-thoughts-button").offset().top - 60
			    }, 700);
			} else {
				$('html, body').animate({
			        scrollTop: $("#adjective-list-angular").prop("scrollHeight") - 420
			    }, 700);
			}
			
			$scope.show_hidden = !$scope.show_hidden;
		}
		
		// Комментировать прилагательное
		$scope.comment = function(id_adj) {
			window.location = document.URL + "/comments-" + id_adj;
		}
	}
	

/**************** В ПАМЯТЬ О JQUERY. ANGULAR Рулит *******************
	// Голосование за
	function voteFor(id) {
		like 			= $("#vote-for-" + id);
		like_count		= $("#vote-for-count-" + id);
		dislike 		= $("#vote-against-" + id);
		dislike_count	= $("#vote-against-count-" + id);
		
		// Проверяем, был ли голос за, если да, убираем голос
		if (like.hasClass("voted")) {
			like.removeClass("voted");
			like_count.html(parseInt(like_count.html()) - 1);
			updateBarAndVote(id, 1);
			return;
		}
		
		// Проверяем, был ли голос против, если да, убираем его
		if (dislike.hasClass("voted")) {
			dislike.removeClass("voted");
			dislike_count.html(parseInt(dislike_count.html()) - 1);
		}
		
		like.addClass("voted");
		like_count.html(parseInt(like_count.html()) + 1);
		updateBarAndVote(id, 1);
	}
	
	// Голосование за
	function voteAgainst(id) {
		like 			= $("#vote-for-" + id);
		like_count		= $("#vote-for-count-" + id);
		dislike 		= $("#vote-against-" + id);
		dislike_count	= $("#vote-against-count-" + id);
		
		// Проверяем, был ли голос против, если да, убираем голос
		if (dislike.hasClass("voted")) {
			dislike.removeClass("voted");
			dislike_count.html(parseInt(dislike_count.html()) - 1);
			updateBarAndVote(id, 0);
			return;
		}
		
		// Проверяем, был ли голос за, если да, убираем его
			if (like.hasClass("voted")) {
			like.removeClass("voted");
			like_count.html(parseInt(like_count.html()) - 1);
		}
		
		dislike.addClass("voted");
		dislike_count.html(parseInt(dislike_count.html()) + 1);
		updateBarAndVote(id, 0);
	}
	
	// Обновляет BAR
	function updateBarAndVote(id, type) {
		// Отправка аякса на голосование
		ajaxVote(id, type);
		
		like_count		= parseInt($("#vote-for-count-" + id).html());
		dislike_count	= parseInt($("#vote-against-count-" + id).html());
		
		total = like_count + dislike_count;
		
		new_width = like_count * 100 / total;
				
		$("#bar-" + id).css("width", new_width + "%");
	}
	
	// Аякс-запрос на голосование
	function ajaxVote(id, type){
		// Анимация загрузки
		// ajaxStart();
		
		// Пост-запрос
		$.post("?controller=user&action=AjaxVote", {"id" : id, "type" : type})
			.success(function(response) {
				// Завершение анимации
				// ajaxEnd();
				return true;
			})
			.error(function(){
				// ajaxEnd();
				bootbox.alert("<b>Произошла ошибка! Голос не учтен.</b><br>Обновите страницу и попробуйте еще раз");
				
				return false;
			});
	}
	
	function addNewAnimation() {
		div = $("#adjective-list");
		
		div.prepend("<h1>WORK</h1>");
	} */