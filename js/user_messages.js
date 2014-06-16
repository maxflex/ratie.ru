$(document).ready(function(){
	$("#user-comments-page").addClass(_page_additional_start_animation);
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
			// FireBase
			fb = new Firebase("https://ratie.firebaseio.com/" + $scope.id_viewing);	
			
			fb.on('child_added', function(snapshot) {
				$scope.comments.push(snapshot.val());
			});
			
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
			
			console.log($scope.comments);
		});
		
		
		
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
			if ($scope.intro_message) {
				
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
						
						message += "</center></span><hr>" + $scope.user_name + " <span style='font-family: RaleWayMedium'>не</span> увидит автора этого сообщение."
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
			ajaxStart();
			$.post("?controller=user&action=AjaxLeaveComment", {
				"comment" 		: $scope.comment, 
				"id_adjective" 	: $scope.id_adjective, 
				"id_viewing" 	: $scope.id_viewing
			})
				.success(function(resp) {
					ajaxEnd();
					
					// Если ответ от сервера – число (ID нового комметария), то добавляем в список 
					if (parseInt(resp)) {
						// Если добавляем в первый раз
						if (!$scope.comments) {
							// Инициализируем объект
							$scope.comments = [];
						}
						
						$scope.comments.$add({
							"id"			: resp,
							"comment"		: $scope.comment,
							"id_user"		: $scope.commentator.id,
							"_ang_login"	: $scope.commentator.login,
							"_ang_ava"		: $scope.commentator.avatar,
							"_ang_stretch"	: $scope.commentator.stretch,
						});
						
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
					ajaxEnd();
					bootbox.alert(_ALERT_CAUTION + "Произошла ошибка! Не удалось отправить сообщение.");					
				});
		}
		
		$scope.watchEnter = function($event) {
			// Отправляем комментарий по кнопке Enter
			if ($event.keyCode == 13) {
				$scope.leaveComment();
				$event.currentTarget.blur();
			}
		}
	}