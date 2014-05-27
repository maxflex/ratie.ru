$(document).ready(function(){
	$("#profile-page").addClass(_page_start_animation);

	// Инициализация приложения ВК
	VK.init({
	  apiId: _vk_app_id
	});

});

// Подключаем модуль NG-Animate к приложению UserPage
angular.module('ProfilePage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function ProfileCtrl($scope) {
		
		// Просмотреть подписку
		$scope.showNews = function(id_user, id_last_seen) 
		{
			ajaxStart();
			$.post("?controller=profile&action=AjaxSubNews", {"id_user" : id_user, "id_last_seen" : id_last_seen})
				.success(function(response) {
					ajaxEnd();
					$scope.sub_watch = true;
					$scope.$apply();
					$(".sub-news-container").html(response);
				});
		}
		
		// Получить друзей на сайте
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
								// Если есть хоть какие-то друзья на сайте
								if (response != "false") {
									// Получаем друзей
									$scope.friends = $.parseJSON(response);
									
									// Количество друзей
									friends_count = $scope.friends.length;
									
									// Получаем последние 2 цифры
									last_2_digits	= friends_count % 100;
									// Получаем последнюю цифру
									last_digit		= friends_count % 10;
									
									// Текст по умолчанию
									text = "ваших друзей";
									
									// От 5 до 20 текст остается неизменным (ваших друзей)
									if (last_2_digits > 20 || last_2_digits < 5) {
										// 1 ваш друг
										if (last_digit == 1) {
											text = "ваш друг";
										} else {
										// иначе (от 2 до 4) «ваших друга»
											text = "ваших друга";
										}
									}
									
																	
									$scope.$apply();								
									bootbox.alert("<center>" + _ALERT_FRIENDS + friends_count + " " + text + " уже есть на Ratie: <hr></center>" 
													+ $("#friends").html());
								} else {
									bootbox.alert("<center>" + _ALERT_FRIENDS + " Никто из ваших друзей пока не использует Ratie</center>");
								}
							});
					}); 
				}
			});
		}
	}