	$(document).ready(function(){
		$("#user-additional").addClass(_page_start_animation);
	});
	
	// Подключаем модуль NG-Animate к приложению UserPage
	angular.module('UserAdditional', ['ngAnimate']);

	// Контроллер страницы пользователя
	function UserAdditionalCtrl($scope) {		
		// Сообщение о том, что необходимо войти
		$scope.notLoggedIn = function() {
			bootbox.alert(_ALERT_CAUTION + "<a href='http://ratie.ru/#login'>Войдите</a>, чтобы подписаться на пользователя");
		}
		
		// Подписаться/отписаться
		$scope.subscribe = function(id_user) {
			$scope.subscribed = !$scope.subscribed;
			$.post("?controller=user&action=AjaxSubscribe", {"id_user" : id_user})
				.success(function(resp){
				//	 console.log(resp);
				});
		}
	}