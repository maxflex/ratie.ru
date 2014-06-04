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
			
			
			
			if (hash == "logout") {
				// Убиваем куку о том, что залогинены
				// $.removeCookie("logged");
			}
			
			if (hash == "dev_vk_logout") {
				$scope.logout();
			}
			// Если залогинены -- автовход
			/* if ($.cookie("logged")) {
				VK.Auth.getLoginStatus(function(response) {
					// Проверяем, залогинен ли пользователь
					if (response.session) {
						$scope.getUserData();
					} else {
						$scope.logged_in = false;
						$scope.$apply();
						// Убиваем куку о том, что залогинены
						// $.removeCookie("logged");
					}
					
				});
			} */
			
		});	
		
		
		// Логин
		$scope.login = function() {
			// Посылаем запрос на логин
			VK.Auth.login(function(response) {
				// Если залогинен
				if (response.session) {
					$scope.getUserData();
				} else {
					$scope.logged_in = false;
					$scope.$apply();
					
					$("#lightbox").css("display", "none");
					bootbox.alert(_ALERT_CAUTION + " <span style='font-family: RaleWayMedium'>Не удалось войти</span><!--<br><br>Необходимо разрешить доступ приложению Ratie входа на сайт. Приложение запрашивает доступ ТОЛЬКО к основной информации, чтобы получить данные для быстрой автоматической регистрации! Мы не запрашиваем никаких других прав. -->");
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
			$("#lightbox").css("display", "block");
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
								
								// Устанавливаем куку о том, что залогинены
								// $.cookie("logged", response.login);
								
								// Редирект на страницу пользователя
								goTo(response.login);
							});
						console.log($scope.user);
						$scope.$apply();
					}); 
				} else {
					// Убиваем куку о том, что залогинены
					// $.removeCookie("logged");
					
					$("#lightbox").css("display", "none");
					bootbox.alert(_ALERT_CAUTION + " <span style='font-family: RaleWayMedium'>Не удалось войти</span><!-- <br><br>Необходимо разрешить доступ приложению Ratie входа на сайт. Приложение запрашивает доступ ТОЛЬКО к основной информации, чтобы получить данные для быстрой автоматической регистрации! Мы не запрашиваем никаких других прав. -->");
				}
			});
		}
	}