$(document).ready(function(){
	$("#user-comments-page").addClass(_page_additional_start_animation);
});

// Подключаем модуль NG-Animate к приложению UserPage
angular.module('UserCommentsPage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function UserCommentsCtrl($scope) {
		// Для дополнительной сортировки (каждому новому поднятию в списке $scope.order++, чтоб всегда новое было вверху)
		$scope.order = 1;

		// Действия после загрузки приложения
		angular.element(document).ready(function(){
			// Получаем хеш, чтобы поднимать в списке конкретное прилагательное через url#id_adjective
			hash = window.location.hash.substring(1);

			// Если хэш получен
			if (hash.length !== 0) {
				console.log(hash);
				
				// Искомое прилагательное – это ID который был передан в хэш
				sought_comment = $.grep($scope.comments, function(e) {
					return e.id == hash;
				});
				
				console.log(sought_comment);
				
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
			bootbox.alert(_ALERT_CAUTION + "<a href='login'>Войдите</a>, чтобы подписаться на пользователя");
		}
		
		// Оставить комментарий
		$scope.leaveComment = function() {
			// Если комментарий не введен - ничего не делать
			if (!$scope.comment) {
				return;
			}
			ajaxStart();
			$.post("?controller=user&action=AjaxLeaveComment", {"comment" : $scope.comment, "id_adjective" : $scope.id_adjective})
				.success(function(resp) {
					ajaxEnd();
					
					// Если добавляем в первый раз
					if (!$scope.comments) {
						// Инициализируем объект
						$scope.comments = [];
					}
					
					$scope.comments.push({
						"id"			: resp,
						"comment"		: $scope.comment,
						"id_user"		: $scope.commentator.id,
						"_ang_login"	: $scope.commentator.login,
						"_ang_ava"		: $scope.commentator.avatar,
						"_ang_stretch"	: $scope.commentator.stretch,
						"order"			: $scope.order++,
					});
					
					$scope.comment = "";
					$scope.$apply();
				})
				.error(function(){
					ajaxEnd();
					bootbox.alert(_ALERT_CAUTION + "<b>Произошла ошибка! Не удалось оставить комментарий.");					
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