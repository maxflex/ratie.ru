	$(document).ready(function(){
		//$("#main-page").addClass(_page_start_animation);
		
		// Инициализация приложения ВК
		VK.init({
		  apiId: _vk_app_id
		});
	});
	
	// Подключаем модуль NG-Animate к приложению UserPage
	angular.module('Auth', ['ngAnimate']);
	
	// Контроллер страницы пользователя
	function AuthCtrl($scope) {
		
		// После загрузи документа проверяем статус (залогинен ли?)
		angular.element(document).ready(function(){
			$scope.logged_in = false;
			
			// Получаем хеш, чтобы делать авто-логин по адресу ratie.ru/#login
			hash = window.location.hash.substring(1);
			
			// Если хэш получен
			if (hash == "login") {
				$scope.login();
			}
			// Проверяем, если залогинен
			/*
			VK.Auth.getLoginStatus(function(response) {
				// Проверяем, залогинен ли пользователь
				if (response.session) {
					$scope.getUserData();
				} else {
					$scope.logged_in = false;
					$scope.$apply();
				}
				
			});*/
		});	
		
		
		// Логин
		$scope.login = function() {
			$("#lightbox").css("display", "block");
			// Посылаем запрос на логин
			VK.Auth.login(function(response) {
				// Если залогинен
				if (response.session) {
					$scope.getUserData();
				} else {
					$scope.logged_in = false;
					$scope.$apply();
					
					$("#lightbox").css("display", "none");
					bootbox.alert(_ALERT_CAUTION + " <b>Не удалось войти.</b> <br><br> Необходимо разрешить доступ приложению Ratie для входа на сайт. Приложение запрашивает доступ ТОЛЬКО к основной информации, чтобы получить данные для быстрой автоматической регистрации! Мы не запрашиваем никаких дополнительных прав.");
				}
			});			
		}
		
		// Выход
		$scope.logout = function() {
			VK.Auth.logout(function(response) {
				delete $scope.user;
				$scope.logged_in = false;
				$scope.$apply();
			});
		}
		
		// Получаем данные пользователя
		$scope.getUserData = function() {
			// Проверяем, залогинен ли
			VK.Auth.getLoginStatus(function(response) {
				// Если залогинен
				if (response.status == "connected") {
					$scope.logged_in = true;
					VK.Api.call('users.get', {
						uids	: response.session.mid,
						fields	: "first_name,last_name,sex,photo_max,domain,connections"
					}, function(r) { 
						$scope.user = r.response[0];
						
						$.post("?controller=index&action=ajaxLoginOrRegister", $scope.user)
							.success(function(response) {
								console.log(response);
								
								// Ответ функции ajaxLoginOrRegister в JSON
								response = $.parseJSON(response);
								
								// Редирект на страницу пользователя
								goTo(response.login);
								
								// console.log(response);
							});
						console.log($scope.user);
						$scope.$apply();
					}); 
				} else {
					$("#lightbox").css("display", "none");
					bootbox.alert(_ALERT_CAUTION + "<b>Не удалось войти.<b><hr>Необходимо разрешить доступ приложению Ratie входа на сайт. Приложение запрашивает доступ ТОЛЬКО к основной информации, чтобы получить данные для быстрой автоматической регистрации! Мы не запрашиваем никаких других прав.");
				}
			});
		}
	}