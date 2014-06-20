sound = new Audio();

if (sound.canPlayType('audio/mpeg;')) {
	sound.src = "sound/chord.mp3";
} else {
	sound.src = "sound/chord.ogg";
}

comments_loaded = false; // При первой загрузке чата не играть звуковое оповещение
id_last_comment = false; // ID последнего добавленного комментария (не играть звук, если коммент от себя же)

$(document).ready(function(){
	$("#user-comments-page").addClass(_page_additional_start_animation);
	
	// Инициализация приложения ВК
	VK.init({
	  apiId: _vk_app_id
	});
});

// Подключаем модуль NG-Animate к приложению UserPage
angular.module('UserCommentsPage', ['ngAnimate', 'firebase'])
	.filter('reverse', function() {
      function toArray(list) {
         var k, out = [];
         if( list ) {
            if( angular.isArray(list) ) {
               out = list;
            }
            else if( typeof(list) === 'object' ) {
               for (k in list) {
                  if (list.hasOwnProperty(k)) { out.push(list[k]); }
               }
            }
         }
         return out;
      }
      return function(items) {
         return toArray(items).slice().reverse();
      };
   });
   
	// Контроллер страницы пользователя
	function UserCommentsCtrl($scope, $firebase) {
		// Для дополнительной сортировки (каждому новому поднятию в списке $scope.order++, чтоб всегда новое было вверху)
		$scope.order = 1;		
		
		
		// Действия после загрузки приложения
		angular.element(document).ready(function(){	
			// Элемент поля ввода сообщения
			comment_input = $("#comment-input");
				
			// FireBase
			fb = new Firebase("https://ratie.firebaseio.com/" + $scope.id_viewing);	
			
			
			fb.on('child_added', function(snapshot) {								
				// Если стоит надпись «Ваш комментарий будет первым -- убрать ее
				if (!$scope.have_messages) {
					$scope.have_messages = 1;
				}
				
				// Если своя же страница, то обновляем id последнего просмотренного сообщения
				if ($scope.own_page) {
					$.post("?controller=user&action=AjaxUpdateNewMessagesCount");	
				}
				
				if (comments_loaded && (snapshot.val().id != id_last_comment)) {
					sound.play();
				}
								
				$scope.comments.push(snapshot.val());
			});
			
			// Подсчитываем кол-во комментариев
			/*fb.on('value', function(snapshot) {
			   $scope.comments_count = 0;
			   snapshot.forEach(function() {
			       $scope.comments_count++;
			   });
			});*/
			
			// Получаем хеш, чтобы поднимать в списке конкретное прилагательное через url#id_adjective
			hash = window.location.hash.substring(1);

			// Если хэш получен
			if (hash.length !== 0) {
							
				// Искомое прилагательное – это ID который был передан в хэш
				sought_comment = $.grep($scope.comments, function(e) {
					return e.id == hash;
				});
								
				// Если прилагательное найдено
				if (sought_comment.length !== 0) {
					// Поднимаем прилагательное в списке;
					sought_comment[0].order = $scope.order++;
					
					/* // Если это комментарий скрыт
					if (sought_comment[0].hidden) {
						$scope.showHidden(); // То отображаем скрытые 
					} */
					
					// Применяем изменения
					$scope.$apply();
				}
			}
			
			$scope.comments = $firebase(fb);
			
			$scope.comments.$on("loaded", function() {
				comments_loaded = true;
			});
			
			//console.log($scope.comments);
		});
		
		$scope.startLoading = function() {
			comment_input.prop("disabled", true);
			comment_input.addClass("loading");
		}
		
		$scope.endLoading = function() {
			comment_input.prop("disabled", false);
			comment_input.removeClass("loading");
			comment_input.focus();
		}
		
		// Подписаться/отписаться
		$scope.subscribe = function(id_user) {
			$scope.subscribed = !$scope.subscribed;
			$.post("?controller=user&action=AjaxSubscribe", {"id_user" : id_user})
				.success(function(resp){
				//	 console.log(resp);
				});
		}
		
		// Сообщение о том, что необходимо войти
		$scope.notLoggedIn = function() {
			bootbox.alert(_ALERT_CAUTION + "<a href='http://ratie.ru/#login'>Войдите</a>, чтобы подписаться на пользователя");
		}
		
		// Оставить комментарий
		$scope.leaveComment = function() {
			// Если комментарий не введен - ничего не делать
			if (!$scope.comment) {
				return;
			}
			
			// Если нужно показывать сообщение (один раз только показывается для зарегистрированных)
			if ($scope.intro_message && !$scope.own_page) {
				
				message = "<span style='font-family: RaleWayMedium'><center>";
				
				switch ($scope.intro_message) {
					case 1: {
						message += _ALERT_CHAT + "Сообщение будет оставлено публично";
						
						message += "</center></span><hr>" + $scope.user_name + " будет видеть, что это Ваше сообщение."
						+ "<br><br><a href='profile/edit' target='_blank'>Включив анонимность</a> в настройках, никто не узнает"
						+ " кто это написал!";
						
						break;
					}
					case 2: {
						message += _ALERT_PROFILE + " Сообщение будет оставлено анонимно";
						
						message += "</center></span><hr>" + $scope.user_name + " <span style='font-family: RaleWayMedium'>не</span> увидит автора этого сообщения."
						+ "<br><br> Можно <a href='profile/edit' target='_blank'>выключить анонимность</a>"
						+ " и пользователи будут видеть, что пишите именно Вы!";
						
						break;
					}
				}
				
				bootbox.confirm(message, function(ans) {
					if (ans === true) {
						$scope.intro_message = 0; // Если уже оставили мнение, больше не показывать сообщение
						$scope.submitComment();
					}
				});
			} else {
				$scope.submitComment();
			}
		}
		
		// Отправить комментарий
		$scope.submitComment = function()
		{
			$scope.startLoading();
			$.post("?controller=user&action=AjaxLeaveComment", {
				"comment" 		: $scope.comment, 
				"id_adjective" 	: $scope.id_adjective, 
				"id_viewing" 	: $scope.id_viewing
			})
				.success(function(resp) {
					$scope.endLoading();
					// Если ответ от сервера – число (ID нового комметария), то добавляем в список 
					if (parseInt(resp)) {
						// Если добавляем в первый раз
						if (!$scope.comments) {
							// Инициализируем объект
							$scope.comments = [];
						}
						
						id_last_comment = resp; // Получаем ID последнего комментария
						
						$scope.comments.$add({
							"id"			: id_last_comment,
							"comment"		: $scope.comment,
							"id_user"		: $scope.commentator.id,
							"_ang_login"	: $scope.commentator.login,
							"_ang_ava"		: $scope.commentator.avatar,
							"_ang_stretch"	: $scope.commentator.stretch,
						});
						
						// Если стоит надпись «Ваш комментарий будет первым -- убрать ее
						if (!$scope.have_messages) {
							$scope.have_messages = 1;
						}
						
						// new_comment.order = $scope.order++;
						// $scope.comments[$scope.comments.length - 1].order = $scope.order++;
						
						//console.log($scope.comments.val());
						
						//$scope.comments.$add($scope.comments[$scope.comments.length - 1]);
						//fb.push($scope.comments[$scope.comments.length - 1]);

						$scope.comment = "";
						$scope.$apply();
					} else {
						// Иначе это ошибка
						bootbox.alert(_ALERT_CAUTION + resp);
					}
					
				})
				.error(function(){
					$scope.endLoading();
					bootbox.alert(_ALERT_CAUTION + "Произошла ошибка! Не удалось отправить сообщение.");					
				});
		}
		
		// Предлагаем друзьям оценить
		$scope.offerChat = function() {
					
			var offer_messages = [
				"Открытый анонимный чат со мной в режиме реального времени",
				"Начни открытую анонимную беседу со мной",
				"Начни анонимный или публичный диалог со мной",
			];
			
			// Получаем случайное сообщение с предложением оценить
			random_message = offer_messages[Math.floor(Math.random()*offer_messages.length)];
			
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
		
		$scope.watchEnter = function($event) {
			// Отправляем комментарий по кнопке Enter
			if ($event.keyCode == 13) {
				$scope.leaveComment();
			//	$event.currentTarget.blur();
			}
		}
	}