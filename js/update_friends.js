	$(document).ready(function(){	
		// Инициализация приложения ВК
		VK.init({
		  apiId: _vk_app_id
		});
	});
	
	// Подключаем модуль NG-Animate к приложению UserPage
	angular.module('UpdatePage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function UpdatePageCtrl($scope) {
		$scope.updateBy = 1;	// Сколько пользователей обновлять за раз
		$scope.progress = 0;
		$scope.current  = 0;
		
		$scope.id_user 	= 1;
		
		// Получаем количество пользователей
		$.post("index.php?controller=update&action=AjaxGetUserCount")
			.success(function(response) {
				$scope.users_count = response;
				// Применяем изменения
				$scope.$apply();
		});
		
		$scope.updateFriends = function() {
			console.log("HERE");
			
			VK.Api.call('friends.get', {user_id	: 1}, function(r) { 
				console.log(r);
			});
		}
		
		$scope.update = function() {
			if (!$scope.updating) {
				$scope.updating = true;
				$scope.error = "";
				ajaxStart();	
			}
			$.post("index.php?controller=update&action=AjaxGetIdVK", {id_user: $scope.id_user})
				.success(function(response) {
					console.log("VK ID FOR USER_" + $scope.id_user + ": " + parseInt(response));
					VK.Api.call('friends.get', {user_id	: parseInt(response)}, function(r) { 
						$.post("?controller=update&action=AjaxAddFriends", {id_user: $scope.id_user, ids: r.response})
							.success(function(response) {
								$scope.current = $scope.id_user;
								$scope.progress = $scope.calculatePercent();
								$scope.id_user++;
								if ($scope.id_user > $scope.users_count) {
									ajaxEnd();
									$scope.updating = false;
									$scope.updated = true;
								} else {
									$scope.update();
								}
								$scope.$apply();
							});
					});
				});
		}
		
		$scope.calculatePercent = function() {
			return Math.ceil($scope.current * 100 / $scope.users_count);
		}
		
		$scope.updateStep = function(start, end) {
			$.post("index.php?controller=update&action=AjaxUpdate", {"start": start, "end": end});
		}
	}