	// Подключаем модуль NG-Animate к приложению UserPage
	angular.module('UpdatePage', ['ngAnimate']);

	// Контроллер страницы пользователя
	function UpdatePageCtrl($scope) {
		$scope.updateBy = 200;	// Сколько пользователей обновлять за раз
		$scope.progress = 0;
		$scope.current  = 0;
		
		$scope.start 	= 1;
		$scope.end 		= $scope.updateBy;
		
		// Получаем количество пользователей
		$.post("index.php?controller=update&action=AjaxGetUserCount")
			.success(function(response) {
				$scope.users_count = response;
				// Применяем изменения
				$scope.$apply();
		});
		
		$scope.update = function() {
			if (!$scope.updating) {
				$scope.updating = true;
				$scope.error = "";
				ajaxStart();	
			}
			$.post("index.php?controller=update&action=AjaxUpdate", {start: $scope.start, end: $scope.end})
				.success(function(response) {
					if (parseInt(response) == 1) {
						$scope.start	+= $scope.updateBy;
						$scope.end		+= $scope.updateBy;
						$scope.current 	+= $scope.updateBy;
						
						if ($scope.current <= $scope.users_count) {
							$scope.progress = $scope.calculatePercent();
							$scope.update();
						} else {
							$scope.progress = 100;
							$scope.current = $scope.users_count;
							$scope.updating = false;
							$scope.updated	= true;
							ajaxEnd();
						}
						$scope.$apply();
					} else {
						$scope.updating = false;
						$scope.error = response;
						ajaxEnd();
						$scope.$apply();
					}
				});
		}
		
		$scope.calculatePercent = function() {
			return Math.ceil($scope.current * 100 / $scope.users_count);
		}
		
		$scope.updateStep = function(start, end) {
			$.post("index.php?controller=update&action=AjaxUpdate", {"start": start, "end": end});
		}
	}