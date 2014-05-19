$(document).ready(function(){
	//$("#main-page").addClass(_page_start_animation);
	
	// Инициализация приложения ВК
	VK.init({
	  apiId: _vk_app_id
	});
});

var testy;
// Подключаем модуль NG-Animate к приложению UserPage
angular.module('TestPage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function TestCtrl($scope) {
	
		$scope.getFriends = function() {
			// Проверяем, залогинен ли
			VK.Auth.getLoginStatus(function(response) {
				// Если залогинен
				if (response.status == "connected") {

					VK.Api.call('friends.get', {
						user_id	: response.session.mid,
					}, function(r) { 
						
						$.post("?controller=user&action=ajaxGetFriends", {ids: r.response})
							.success(function(response) {
								// Получаем друзей
								$scope.friends = $.parseJSON(response);
								
								$scope.$apply();								
								//$scope.$apply();
								
								bootbox.alert("<center>" + _ALERT_FRIENDS + "Некоторые ваши друзья уже пользуются сайтом <hr></center>" 
												+ $("#friends").html());
							});
					}); 
				}
			});
		}
	}