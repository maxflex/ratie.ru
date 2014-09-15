<div class="container" ng-app="UpdatePage" ng-controller="UpdatePageCtrl">
	<div class="row">
		<div class="col-md-12">
			<center>
		 	<h1 class="text-white">Обновление</h1>
		 	<hr>
		 	<button class="btn btn-primary btn-large br5" ng-click="update()" ng-disabled="!users_count || updating || updated">Начать обновление</button>
		 	
		 	<br><br>
		 	<div class="adjective-bar update progress-primary" ng-class="{'active' : updating}">
		 		<div ng-show="users_count" class="update-info">{{current}}/{{users_count}}</div>
		 		<div ng-hide="users_count" class="update-info" style="background: white"><img src="img/loader/spinner.gif"></div>
		 		<div class="bar" style="width: {{progress}}%"></div>
		 	</div>
			
			<div ng-show="error" class="badge badge-important center-t animate-show-down" style="margin-top: 10%; width: 100%; padding-left: 0; padding-right: 0">
				<h4 class="text-white" style="font-family: 'RaleWayMedium'; font-size: 18px">
					<span class="glyphicon glyphicon-remove" style="top: 3px"></span> {{error}}
				</h4>
			</div>
			
			<div ng-show="updated" class="badge badge-success center-t animate-show-down" style="margin-top: 10%; width: 50%; padding-left: 0; padding-right: 0; white-space: normal;">
				<h4 class="text-white" style="font-family: 'RaleWayMedium'; font-size: 18px">
					<span class="glyphicon glyphicon-ok" style="top: 3px"></span> Обновление завершено
				</h4>
			</div>
			
			</center>
		</div>
	</div>
</div>